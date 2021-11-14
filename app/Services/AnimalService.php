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
     * Validates the given data.
     *
     * @param array $data
     * @return void
     */
    public function validation(array $data, array $additionalRules = null, bool $withId = false)
    {
        $validationrules = $this->validationrules;

        if (!is_null($additionalRules)) {
            $validationrules = array_merge($validationrules, $additionalRules);
        }

        if ($withId === true) {
            $validationrules = array_merge($validationrules, $this->idRules);
        }

        return Validator::make(
            $data,
            $validationrules,
            $this->validationMessages,
            $this->validationAttributes
        );
    }

    /**
     * Validates the id.
     *
     * @param array $id
     * @return void
     */
    public function validateId(array $id)
    {
        return Validator::make(
            $id,
            $this->idRules,
            $this->validationMessages,
            $this->validationAttributes
        );
    }

    /**
     * Creates an Animal and associates it to the given user.
     *
     * @param User $user
     * @return Animal
     */
    public function registerAnimal(User $user, array $data)
    {
        $newAnimal = Animal::create($data);
        $newAnimal->user()->save($user);

        return $newAnimal;
    }

    /**
     * Updates the animal information.
     *
     * @param User $user
     * @param array $data
     * @return array
     */
    public function updateAnimal(User $user, array $data)
    {
        //get the animal data
        $profileable = $user->profileable;

        $user->fill($data);
        $user->save();
        $profileable->fill($data);
        $profileable->save();

        return array_merge(
            $user->makeHidden(
                [
                    'email_verified_at',
                    'profileable',
                    'created_at',
                    'updated_at'
                ]
            )->toArray(),
            $profileable->makeHidden(
                [
                    'is_enabled',
                    'likes',
                    'dislikes',
                    'interactions'
                ]
            )->toArray()
        );
    }

    /**
     * Register a vote
     *
     * @param Animal $animalVoted
     * @param $vote
     * @return void
     */
    public function animalVote(Animal $animalVoted, $vote)
    {
        //increases the number of like or dislike of the voted animal
        if (is_null($vote)) {
            $animalVoted->dislikes++;
        } else {
            $animalVoted->likes++;
        }
        $animalVoted->save();

        //increases the number of interactions in the logged animal
        $animalVoting = auth()->user();
        $profileable = $animalVoting->profileable;
        $profileable->interactions++;
        if ($profileable->interactions == $profileable->id) {
            $profileable->interactions++;
        }
        $profileable->save();
    }

    /**
     * Disables an animal by the id.
     *
     * @param integer $id
     * @return void
     */
    public function disable(int $id)
    {
        $animal = Animal::find($id);
        $animal->is_enabled = false;
        $animal->save();
    }
}
