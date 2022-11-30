<?php

use App\Http\Controllers\Api\ClockController;
use App\Http\Controllers\Api\LeaveController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\Api\RecordController;
use App\Models\Record;
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
    Route::get('/profile', [ProfileController::class, 'profile']);
    Route::get('/school', [ProfileController::class, 'school']);
    Route::get('/grade', [ProfileController::class, 'grade']);

    Route::post('/password', [LoginController::class, 'changePassword']);

    Route::post('/clock-in', [ClockController::class, 'clockIn']);
    Route::post('/clock-out', [ClockController::class, 'clockOut']);

    Route::get('/clock-status', [ClockController::class, 'clockStatus']);
    Route::get('/clock-history', [ClockController::class, 'history']);

    Route::get('/leave', [LeaveController::class, 'index']);
    Route::post('/leave', [LeaveController::class, 'create']);

    Route::get('/quote', [QuoteController::class, 'index']);

    Route::get('/record-friend', [RecordController::class, 'friend']);
});
