<?php
namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Creates an User.
     *
     * @param array $data
     * @return User
     */
    public static function registerUser(array $data)
    {
        return User::create($data);
    }
}