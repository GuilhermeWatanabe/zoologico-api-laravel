<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Services\AnimalService;
use App\Services\ImgurService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
            'nickname',
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
            array_merge($newUser->toArray(), $newAnimal->toArray()),
            201
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //validation
        $validator = Validator::make(
            array_merge(['id' => $id], $request->all()),
            array_merge($this->idRules, $this->validationrules),
            $this->validationMessages,
            $this->validationAttributes
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        //gets the animal by the given id to check if the given password is correct
        $animal = Animal::find($id);
        if (!Hash::check($request->password, $animal->password)) {
            return response()->json(['error' => 'Senha inválida.']);
        }

        //image upload to imgur
        $response = Http::withHeaders([
            'Authorization' => 'Client-ID 599b2d427ea9e85'
        ])->post('https://api.imgur.com/3/image', [
            'image' => base64_encode(file_get_contents($request->image->path()))
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Falha ao fazer upload do arquivo.'], 500);
        }

        $animal->fill(array_merge(
            $request->except('password, image'),
            ['image_url' => $response->json('data')['link']]
        ));
        $animal->save();

        return response()->json($animal, 200);
    }

    /**
     * Mark the animal by given id with liked or disliked by the logged animal
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function voting(Request $request, $id)
    {
        //validation
        $validator = Validator::make(
            array_merge(['id' => $id], $request->all()),
            array_merge($this->idRules, ['dislike' => 'exclude_if:like,like']),
            $this->validationMessages,
            $this->validationAttributes
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $animalVoted = Animal::find($id);
        if (is_null($request->like)) {
            $animalVoted->dislikes++;
        }
        if (is_null($request->dislike)) {
            $animalVoted->likes++;
        }
        $animalVoted->save();

        $animalVoting = auth('api-animals')->user();
        $animalVoting->interactions++;
        if ($animalVoting->interactions == $animalVoting->id) {
            $animalVoting->interactions++;
        }
        $animalVoting->save();

        return response()->json(['message' => 'Votado com sucesso.']);
    }

    /**
     * Disables one animal by the given id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disable($id)
    {
        //validation
        $validator = Validator::make(
            ['id' => $id],
            $this->idRules,
            $this->validationMessages,
            $this->validationAttributes
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $animal = Animal::find($id);
        $animal->is_enabled = false;
        $animal->save();

        return response()->json($animal, 200);
    }

    /**
     * Return animals to vote
     *
     * @return \Illuminate\Http\Response
     */
    public function toVote()
    {
        $user = auth('api-animals')->user();

        $list = DB::table('animals')->where('id', '<>', $user->id)->where('id', '>=', $user->interactions)->get(['id', 'nickname', 'scientific_name', 'zoo_wing', 'image_url']);

        return response()->json($list, 200);
    }
}
