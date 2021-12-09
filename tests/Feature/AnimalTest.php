<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnimalTest extends TestCase
{
    /**
     * Test if fails with no request data.
     *
     * @return void
     */
    public function testtest_if_fails_with_no_data_example()
    {
        $response = $this->registerRoute();

        $response->assertStatus(400);
        $response->assertJsonValidationErrors([
            'name' => 'O campo apelido é obrigatório.',
            'email' => 'O campo e-mail é obrigatório.',
            'password' => 'O campo senha é obrigatório.',
            'scientific_name' => 'O campo nome científico é obrigatório.',
            'zoo_wing' => 'O campo ala do zoológico é obrigatório.',
            'image' => 'O campo imagem é obrigatório.',
        ], null);
    }

    /**
     * Test if register a new Animal when sending the right data.
     *
     * @return Animal
     */
    public function test_if_register_an_animal_sending_the_correct_data()
    {
        $animal = Animal::factory()->make();
        $response = $this->registerRoute($animal->toArray());

        $response->assertCreated();
        $response->assertJsonPath('name', $animal->name);
        $response->assertJsonPath('email', $animal->email);
        $response->assertJsonPath('scientific_name', $animal->scientific_name);
        $response->assertJsonPath('zoo_wing', $animal->zoo_wing);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('animals', 1);
        $this->assertDatabaseHas('users', [
            'name' => $animal->name,
            'email' => $animal->email
        ]);
        $this->assertDatabaseHas('animals', [
            'scientific_name' => $animal->scientific_name,
            'zoo_wing' => $animal->zoo_wing,
        ]);

        return $animal;
    }

    /**
     * Test if fails with invalid email.
     *
     * @return void
     */
    public function test_if_fails_with_invalid_email()
    {
        $animal = Animal::factory()->make();
        $animal->email = 'invalid';

        $response = $this->registerRoute($animal->toArray());

        $response->assertStatus(400);
        $response->assertJsonPath('email.0', 'E-mail inválido.');
        $response->assertJsonCount(1);
    }

    /**
     * Test if fails when try to register duplicated email.
     *
     * @depends test_if_register_an_animal_sending_the_correct_data
     * @param Animal $animal
     * @return void
     */
    public function test_if_fails_when_try_to_register_duplicated_email(Animal $animal)
    {
        $response = $this->registerRoute($animal->toArray());

        $response->assertStatus(400);
        $response->assertJsonPath('email.0', 'E-mail já está cadastrado.');
        $response->assertJsonCount(1);
    }

    /**
     * Test if fails with invalid image.
     *
     * @return void
     */
    public function test_if_fails_with_invalid_image()
    {
        $animal = Animal::factory()->make();
        $animal->image = 'not an image';
        $response = $this->registerRoute($animal->toArray());

        $response->assertStatus(400);
        $response->assertJsonPath('image.0', 'A imagem não é válida.');
        $response->assertJsonCount(1);
    }

    /**
     * Test if updates a registered user.
     *
     * @depends test_if_register_an_animal_sending_the_correct_data
     * @param Animal $animal
     * @return void
     */
    public function test_if_updates_a_registered_user(Animal $animal)
    {
        $anotherAnimal = Animal::factory()->make();
        $anotherAnimal->password = $animal->password;

        //get all the information about the user from the USER model.
        $user = $this->getFromUserModel($animal->email);

        $response = $this->actingAs($user)->post('/api/animal', array_merge(
            $anotherAnimal->toArray(),
            ['_method' => 'PATCH']
        ));

        $response->assertStatus(200);
        $response->assertJsonPath('name', $anotherAnimal->name);
        $response->assertJsonPath('email', $anotherAnimal->email);
        $response->assertJsonPath('scientific_name', $anotherAnimal->scientific_name);
        $response->assertJsonPath('zoo_wing', $anotherAnimal->zoo_wing);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('animals', 1);
        $this->assertDatabaseHas('users', [
            'name' => $anotherAnimal->name,
            'email' => $anotherAnimal->email
        ]);
        $this->assertDatabaseHas('animals', [
            'scientific_name' => $anotherAnimal->scientific_name,
            'zoo_wing' => $anotherAnimal->zoo_wing,
        ]);
    }

    /**
     * Helper function to make request and register a Janitor.
     *
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    public function registerRoute(array $data = [])
    {
        return $this->post('/api/animal', $data);
    }

    /**
     * Register an animal and returns it.
     *
     * @return Animal
     */
    public function registerAnAnimal()
    {
        $animal = Animal::factory()->make();

        $this->registerRoute($animal->toArray());

        return $animal;
    }

    /**
     * Helper to get the informations by email FROM THE USER MODEL instead of the ANIMAL
     * (some things don't work if I use the animal's model. EX:Auth::user(), 
     * because the authentication is made with USER model not with the ANIMAL model).
     *
     * @param string $email
     * @return User
     */
    public function getFromUserModel(string $email)
    {
        return User::where('email', $email)->first();
    }
}
