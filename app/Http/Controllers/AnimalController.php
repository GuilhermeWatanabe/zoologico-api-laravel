<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Services\AnimalService;
use App\Services\ImgurService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AnimalController extends Controller
{
    public function __construct()
    {
        $this->service = new AnimalService();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Animal::all([
            'id',
            'likes',
            'dislikes',
            'is_enabled'
        ]));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validation
        $validator = $this->service->validation($request->all());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //image upload to imgur
        $imgurResponse = ImgurService::uploadImage($request->image->path());

        if ($imgurResponse->failed()) {
            return response()->json(
                ['error' => 'Falha ao fazer upload do arquivo.'],
                500
            );
        }

        $newUser = UserService::registerUser(
            $request->only('name', 'email', 'password')
        );
        $newAnimal = $this->service->registerAnimal(
            $newUser,
            array_merge(
                $request->only(
                    'name',
                    'scientific_name',
                    'zoo_wing'
                ),
                ['image_url' => $imgurResponse->json('data')['link']]
            )
        );

        return response()->json(
            array_merge(
                $newUser->toArray(),
                $newAnimal->makeHidden('id')->toArray()
            ),
            201
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //validation
        $validator = $this->service->validation($request->except('_method'));

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //gets the logged animal
        $animal = auth()->user();
        if (!Hash::check($request->password, $animal->password)) {
            return response()->json(['error' => 'Senha inválida.']);
        }

        //image upload to imgur
        $imgurResponse = ImgurService::uploadImage($request->image->path());

        if ($imgurResponse->failed()) {
            return response()->json(
                ['error' => 'Falha ao fazer upload do arquivo.'],
                500
            );
        }

        $updatedAnimal = $this->service->updateAnimal(
            $animal,
            array_merge(
                $request->except('password, image', '_method'),
                ['image_url' => $imgurResponse->json('data')['link']]
            )
        );

        return response()->json($updatedAnimal, 200);
    }

    /**
     * Mark the animal by the given id with liked or disliked by the logged animal
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voting(Request $request, $id)
    {
        $animalVoted = Animal::find($id);
        if (is_null($animalVoted)) {
            return response()->json(['error' => 'Este animal não existe.']);
        }
        if (is_null($request->like) && is_null($request->dislike)) {
            return response()->json(['error' => 'Voto inválido.']);
        }

        //send the like parammeter, if the user has voted dislike, will send null
        $this->service->animalVote($animalVoted, $request->like);

        return response()->json(['message' => 'Votado com sucesso.']);
    }

    /**
     * Disables one animal by the id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disable($id)
    {
        //validation
        $validator = $this->service->validateId(compact('id'));

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $this->service->disable($id);

        return response()->json(['message' => 'Desativado com sucesso.'], 200);
    }

    /**
     * Return the list of animals to vote
     *
     * @return \Illuminate\Http\Response
     */
    public function toVote()
    {
        $user = auth()->user();
        $animal = $user->profileable;

        $list = DB::table('animals')
            ->where('animals.id', '<>', $animal->id)
            ->where('animals.id', '>=', $animal->interactions)
            ->join('users', 'animals.id', '=', 'users.profileable_id')
            ->get(['animals.id', 'users.name', 'animals.scientific_name', 'animals.zoo_wing', 'animals.image_url']);

        return response()->json($list, 200);
    }
}
