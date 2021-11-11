<?php

namespace App\Http\Controllers;

use App\Models\Janitor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JanitorController extends Controller
{
    public function __construct()
    {
        $this->validationrules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
        ];
        $this->validationMessages = [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute não é um nome/texto válido.',
            'email' => 'Digite um e-mail válido.',
            'unique' => 'Email já cadastrado.'
        ];
        $this->validationAttributes = [
            'name' => 'nome',
            'email' => 'e-mail',
            'password' => 'senha',
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationrules, $this->validationMessages, $this->validationAttributes);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $newUser = User::create($request->all());
        $newJanitor = Janitor::create();
        $newJanitor->user()->save($newUser);

        return response()->json($newUser, 201);
    }
}
