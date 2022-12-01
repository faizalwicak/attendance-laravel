<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RecordController;
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
        if (Auth::user()->role == 'SUPERADMIN') {
            return redirect('/school');
        }
        return redirect('/overview');
    }
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'actionLogin'])->name('login');


Route::group(['middleware' => ['auth']], function () {
    Route::get('/logout', [LoginController::class, 'actionLogout'])->name('logout');
    // Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/absent/{id}/accept', [HomeController::class, 'accept']);
    Route::get('/absent/{id}/decline', [HomeController::class, 'decline']);

    Route::group(['middleware' => ['role:SUPERADMIN']], function () {
        Route::resource('/school', SchoolController::class);
    });

    Route::group(['middleware' => ['role:SUPERADMIN,ADMIN']], function () {
        Route::get('/admin', [AdminController::class, 'index']);
        Route::get('/admin/create', [AdminController::class, 'create']);
        Route::post('/admin', [AdminController::class, 'store']);
        Route::get('/admin/{id}/edit', [AdminController::class, 'edit']);
        Route::put('/admin/{id}', [AdminController::class, 'update']);
        Route::delete('/admin/{id}', [AdminController::class, 'destroy']);
        Route::get('/admin/{id}/access', [AdminController::class, 'operatorAccess']);
        Route::put('/admin/{id}/access', [AdminController::class, 'operatorAccessAction']);
    });

    Route::group(['middleware' => ['role:ADMIN']], function () {
        Route::resource('/grade', GradeController::class);
        Route::get('/grade/{grade_id}/student', [StudentController::class, 'indexGrade']);

        Route::get('/student', [StudentController::class, 'index']);
        Route::get('/student/create', [StudentController::class, 'create']);
        Route::post('/student', [StudentController::class, 'store']);
        Route::get('/student/{id}/edit', [StudentController::class, 'edit']);
        Route::put('/student/{id}', [StudentController::class, 'update']);
        Route::delete('/student/{id}', [StudentController::class, 'destroy']);

        Route::get('/student/import', [StudentController::class, 'importStudent']);
        Route::post('/student/importAction', [StudentController::class, 'importStudentAction']);
        // Route::get('/student/export', [StudentController::class, 'exportStudent']);

        Route::get('/quote', [QuoteController::class, 'index']);
        Route::get('/quote/create', [QuoteController::class, 'create']);
        Route::post('/quote/store', [QuoteController::class, 'store']);
        Route::delete('/quote/{id}', [QuoteController::class, 'destroy']);
        Route::get('/quote/{id}/edit', [QuoteController::class, 'edit']);
        Route::put('/quote/{id}', [QuoteController::class, 'update']);

        Route::get('/overview', [OverviewController::class, 'index']);

        Route::get('/record/month', [RecordController::class, 'records_month']);
        Route::get('/record/day', [RecordController::class, 'records_day']);
        Route::get('/record/leave', [RecordController::class, 'record_leave']);
        Route::get('/record/{id}', [RecordController::class, 'record_detail']);
        Route::put('/record/{id}', [RecordController::class, 'record_status']);

        Route::resource('/event', EventController::class);
    });

    Route::get('/me/profile', [ProfileController::class, 'updateProfilePage']);
    Route::post('/me/profile', [ProfileController::class, 'updateProfileAction']);
    Route::get('/me/password', [ProfileController::class, 'changePasswordPage']);
    Route::post('/me/password', [ProfileController::class, 'changePasswordAction']);
    Route::get('/me/school', [SchoolController::class, 'updateSchoolPage']);
    Route::post('/me/school', [SchoolController::class, 'updateSchoolAction']);
});
