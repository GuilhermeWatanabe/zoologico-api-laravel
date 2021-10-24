<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnimalController extends Controller
{
    public function __construct()
    {
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
            'image' => 'A imagem não é válida.'
        ];
        $this->validationAttributes = [
            'nickname' => 'apelido',
            'scientific_name' => 'nome científico',
            'password' => 'senha',
            'zoo_wing' => 'ala do zoológico',
            'image' => 'imagem'
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
        //
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
