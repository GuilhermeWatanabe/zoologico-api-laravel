<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JanitorController;
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

//auth routes
Route::post('login', [AuthController::class, 'login']);

//animals routes
Route::get('animal', [AnimalController::class, 'index']);
Route::post('animal', [AnimalController::class, 'store']);
//used POST to the update method because i can't upload files with PUT/PATH
Route::post('animal/{id}', [AnimalController::class, 'update']);
Route::patch('animal/{id}', [AnimalController::class, 'disable']);

Route::group(['middleware' => ['apiJWT']], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::patch('animal/voting/{id}', [AnimalController::class, 'voting']);
    Route::get('animal/to-vote', [AnimalController::class, 'toVote']);
});

//janitors routes
Route::post('janitor', [JanitorController::class, 'store']);
