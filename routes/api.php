<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AuthController;
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

Route::post('login', [AuthController::class, 'login']);

Route::post('animal', [AnimalController::class, 'store']);
//used POST to the update method because i can't upload files with PUT/PATH
Route::post('animal/{id}', [AnimalController::class, 'update']);

Route::group(['middleware' => ['apiJWT']], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('janitor', [JanitorController::class, 'store']);
});
