<?php

namespace Tests\Feature;

use App\Models\Janitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JanitorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if fails with no request data.
     *
     * @return void
     */
    public function test_if_fails_with_no_data()
    {
        $response = $this->register();

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['name', 'email', 'password'], null);
    }

    /**
     * Test if fails with invalid email.
     *
     * @return void
     */
    public function test_if_fails_with_invalid_email()
    {
        $janitor = Janitor::factory()->make();
        $janitor->email  = 'invalid';

        $response = $this->register($janitor->toArray());

        $response->assertStatus(400);
        $response->assertJsonPath('email.0', 'Digite um e-mail válido.');
    }

    /**
     * Test if fails when try to register duplicated email.
     *
     * @return void
     */
    public function test_if_fails_when_try_to_register_duplicated_email()
    {
        $janitor = Janitor::factory()->make();

        $this->register($janitor->toArray());
        $response = $this->register($janitor->toArray());

        $response->assertStatus(400);
        $response->assertJsonPath('email.0', 'Email já cadastrado.');
    }

    public function test_if_succeed_sending_the_correct_data()
    {
        $janitor = Janitor::factory()->make();

        $response = $this->register($janitor->toArray());

        $response->assertCreated();
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('janitors', 1);
        $this->assertDatabaseHas('users', [
            'name' => $janitor->name,
            'email' => $janitor->email,
        ]);
    }

    /**
     * Helper function to make request and register a Janitor.
     *
     * @param array $data
     * @return \Illuminate\Testing\TestResponse
     */
    private function register(array $data = [])
    {
        return $this->post('/api/janitor', $data);
    }
}
