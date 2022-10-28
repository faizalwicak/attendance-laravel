<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'actionLogin'])->name('login');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/logout', [LoginController::class, 'actionLogout'])->name('logout');
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::group(['middleware' => ['role:SUPERADMIN']], function () {
        Route::resource('/school', SchoolController::class);

        Route::get('/admin', [AdminController::class, 'index']);
        Route::get('/admin/create', [AdminController::class, 'create']);
        Route::post('/admin', [AdminController::class, 'store']);
        Route::get('/admin/{id}/edit', [AdminController::class, 'edit']);
        Route::put('/admin/{id}', [AdminController::class, 'update']);
        Route::delete('/admin/{id}', [AdminController::class, 'destroy']);    
    });

    Route::group(['middleware' => ['role:ADMIN']], function () {
        Route::resource('/grade', GradeController::class);
    
        Route::get('/student', [StudentController::class, 'index']);
        Route::get('/student/create', [StudentController::class, 'create']);
        Route::post('/student', [StudentController::class, 'store']);
        Route::get('/student/{id}/edit', [StudentController::class, 'edit']);
        Route::put('/student/{id}', [StudentController::class, 'update']);
        Route::delete('/student/{id}', [StudentController::class, 'destroy']);
    });
        
});
