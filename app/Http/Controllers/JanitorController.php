<?php

namespace App\Http\Controllers;

use App\Models\Janitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JanitorController extends Controller
{
    public function __construct()
    {
        $this->validationrules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:janitors,email',
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        return response()->json(Janitor::create($request->all()), 201);
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
        //
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
}
