<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(
                ['error' => 'E-mail e/ou senha inválidos.'],
                401
            );
        }

        return response()->json(
            ['token' => auth()->user()->createToken('secret key')->plainTextToken],
            200
        );
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Token deleted.'], 200);
    }}
