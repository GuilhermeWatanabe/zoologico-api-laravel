<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class JanitorService
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
     * Validate the given data.
     *
     * @param array $data
     * @return void
     */
    public function validation(array $data)
    {
        return Validator::make(
            $data, 
            $this->validationrules, 
            $this->validationMessages, 
            $this->validationAttributes
        );
    }
}
