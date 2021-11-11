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

Route::post('login', [AuthController::class, 'login']);

Route::post('janitor', [JanitorController::class, 'store']);

Route::post('animal', [AnimalController::class, 'store']);

Route::post('logout', [AuthController::class, 'logout']);

//animals routes
Route::get('animal', [AnimalController::class, 'index']);
//used POST to the update method because I can't upload files with PUT/PATH
Route::post('animal/{id}', [AnimalController::class, 'update']);
Route::patch('animal/{id}', [AnimalController::class, 'disable']);
Route::patch('animal/voting/{id}', [AnimalController::class, 'voting']);
Route::get('animal/to-vote', [AnimalController::class, 'toVote']);

Route::group(['middleware' => ['apiJWT']], function () {
});

