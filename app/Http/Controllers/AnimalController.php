<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


class AnimalController extends Controller
{
    public function __construct()
    {
        $this->idRules = [
            'id' => 'required|integer|exists:animals'
        ];
        $this->validationrules = [
            'nickname' => 'required|string',
            'scientific_name' => 'required|string',
            'password' => 'required|string',
            'zoo_wing' => 'required|string',
            'image' => 'required|image'
        ];
        $this->validationMessages = [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute não é um nome/texto válido.',
            'image' => 'A imagem não é válida.',
            'integer' => 'O :attribute precisa ser um número inteiro.',
            'exists' => 'Este cadastro não existe.',
            'email' => 'E-mail inválido.',
            'unique' => 'E-mail já está cadastrado.'
        ];
        $this->validationAttributes = [
            'nickname' => 'apelido',
            'scientific_name' => 'nome científico',
            'password' => 'senha',
            'zoo_wing' => 'ala do zoológico',
            'image' => 'imagem',
            'email' => 'e-mail'
        ];
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
        $validator = Validator::make(
            $request->all(),
            array_merge($this->validationrules, ['email' => 'required|email|unique:animals,email']),
            $this->validationMessages,
            $this->validationAttributes
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
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

        return Animal::create(array_merge(
            $request->only(
                'nickname',
                'scientific_name',
                'email',
                'password',
                'zoo_wing'
            ),
            ['image_url' => $response->json('data')['link']]
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
        if(is_null($request->like)) {
            $animalVoted->dislikes++;
        }
        if(is_null($request->dislike)) {
            $animalVoted->likes++;
        }
        $animalVoted->save();

        $animalVoting = auth('api-animals')->user();
        $animalVoting->interactions++;
        if($animalVoting->interactions == $animalVoting->id) {
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
