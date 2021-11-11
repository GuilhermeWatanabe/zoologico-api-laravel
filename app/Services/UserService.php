<?php
namespace App\Services;

use App\Models\User;

class UserService
{
    public static function registerUser(array $data)
    {
        return User::create($data);
    }
}