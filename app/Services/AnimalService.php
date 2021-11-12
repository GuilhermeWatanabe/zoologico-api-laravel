<?php

namespace App\Services;

use App\Models\Animal;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AnimalService
{
    public function __construct()
    {
        $this->idRules = [
            'id' => 'required|integer|exists:animals'
        ];
        $this->validationrules = [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'scientific_name' => 'required|string',
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
            'name' => 'apelido',
            'scientific_name' => 'nome científico',
            'password' => 'senha',
            'zoo_wing' => 'ala do zoológico',
            'image' => 'imagem',
            'email' => 'e-mail'
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

    public function registerAnimal(User $user, array $data)
    {
        $newAnimal = Animal::create($data);
        $newAnimal->user()->save($user);

        return $newAnimal;
    }
}
