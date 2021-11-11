<?php

namespace App\Http\Controllers;

use App\Services\JanitorService;
use App\Services\UserService;
use Illuminate\Http\Request;

class JanitorController extends Controller
{
    public function __construct()
    {
        $this->service = new JanitorService();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->service->validation($request->all());

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $newUser = UserService::registerUser($request->all());
        $this->service->registerJanitor($newUser);

        return response()->json($newUser, 201);
    }
}
