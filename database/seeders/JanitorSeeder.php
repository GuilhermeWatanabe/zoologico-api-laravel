<?php

namespace Database\Seeders;

use App\Models\Janitor;
use App\Models\User;
use Illuminate\Database\Seeder;

class JanitorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(10)->create()->each(function ($user) {
            $newJanitor = Janitor::create();
            $newJanitor->user()->save($user);
        });
    }
}
