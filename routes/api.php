<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\JanitorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('animal', [AnimalController::class, 'store']);
//used POST to the update method because i can't upload files with PUT/PATH
Route::post('animal/{id}', [AnimalController::class, 'update']);

Route::post('janitor', [JanitorController::class, 'store']);
