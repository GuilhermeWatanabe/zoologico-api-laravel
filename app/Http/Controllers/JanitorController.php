<?php

namespace App\Http\Controllers;

use App\Models\Janitor;
use App\Models\User;
use App\Services\JanitorService;
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

        $newUser = User::create($request->all());
        $newJanitor = Janitor::create();
        $newJanitor->user()->save($newUser);

        return response()->json($newUser, 201);
    }
}
