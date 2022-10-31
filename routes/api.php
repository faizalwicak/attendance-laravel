<?php

use App\Http\Controllers\Api\LoginController;
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

Route::post('/login', [LoginController::class, 'login']);

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/profile', [LoginController::class, 'profile']);
    Route::get('/school', [LoginController::class, 'school']);
    Route::get('/grade', [LoginController::class, 'grade']);

    Route::post('/clock', [LoginController::class, 'clock']);
});
