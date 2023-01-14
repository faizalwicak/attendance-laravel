<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClockController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ImportantLinkController;
use App\Http\Controllers\Mobile\ClockActionController;
use App\Http\Controllers\Mobile\HomeController;
use App\Http\Controllers\Mobile\LeaveActionController;
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
        if (auth()->user()->role == 'SUPERADMIN') {
            return redirect('/school');
        }
        if (auth()->user()->role == 'USER') {
            return redirect()->route('mobile.home');
        }
        return redirect('/overview');
    }
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'actionLogin'])->name('login');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/logout', [LoginController::class, 'actionLogout'])->name('logout');

    Route::group(['middleware' => ['role:SUPERADMIN']], function () {
        Route::resource('/school', SchoolController::class);
    });

    Route::group(['middleware' => ['role:SUPERADMIN,ADMIN']], function () {
        Route::resource('/admin', AdminController::class);
        Route::get('/admin/{id}/access', [AdminController::class, 'operatorAccess']);
        Route::put('/admin/{id}/access', [AdminController::class, 'operatorAccessAction']);
    });

    Route::group(['middleware' => ['role:ADMIN']], function () {
        Route::resource('/grade', GradeController::class);

        Route::resource('/student', StudentController::class);
        Route::post('/student/{id}/reset-device', [StudentController::class, 'resetDevice']);
        Route::get('/student/import', [StudentController::class, 'importStudent']);
        Route::post('/student/importAction', [StudentController::class, 'importStudentAction']);

        Route::resource('/quote', QuoteController::class);

        Route::resource('/important-link', ImportantLinkController::class);

        Route::resource('/notification', NotificationController::class);

        Route::resource('/event', EventController::class);
    });

    Route::group(['middleware' => ['role:ADMIN,OPERATOR']], function () {
        Route::get('/overview', [OverviewController::class, 'index']);
        Route::get('/record/day', [RecordController::class, 'recordDay']);
        Route::get('/record/month', [RecordController::class, 'recordMonth']);
        Route::get('/record/export', [RecordController::class, 'recordMonthExport']);

        // detail record by user
        Route::post('/record/user/{user_id}/clock-in', [ClockController::class, 'clockIn']);
        Route::post('/record/user/{user_id}/clock-out', [ClockController::class, 'clockOut']);

        Route::get('/record/user/{user_id}/{day}', [RecordController::class, 'detailByQuery']);

        // leave
        Route::get('/record/leave', [LeaveController::class, 'index']);
        Route::post('/record/leave', [LeaveController::class, 'store']);
        Route::put('/record/leave/status', [LeaveController::class, 'leaveStatus']);

        Route::get('/record/user/{user_id}/{day}/leave', [LeaveController::class, 'create']);
    });

    Route::group(['middleware' => ['role:SUPERADMIN,ADMIN,OPERATOR']], function () {
        Route::get('/me/profile', [ProfileController::class, 'updateProfilePage']);
        Route::post('/me/profile', [ProfileController::class, 'updateProfileAction']);
        Route::get('/me/password', [ProfileController::class, 'changePasswordPage']);
        Route::post('/me/password', [ProfileController::class, 'changePasswordAction']);
        Route::get('/me/school', [SchoolController::class, 'updateSchoolPage']);
        Route::post('/me/school', [SchoolController::class, 'updateSchoolAction']);
    });

    Route::name('mobile.')->prefix('mobile')->middleware(['role:USER'])->group(function () {
        Route::get('/home', [HomeController::class, 'home'])->name('home');
        Route::get('/friend', [HomeController::class, 'friend'])->name('friend');
        Route::get('/notification', [HomeController::class, 'notification'])->name('notification');
        Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
        Route::get('/leave', [LeaveActionController::class, 'index'])->name('leave.list');
        Route::get('/leave/create', [LeaveActionController::class, 'create'])->name('leave.create');
        Route::post('/leave/create', [LeaveActionController::class, 'store'])->name('leave.store');

        // Route::get('/attend', [ClockActionController::class, 'index'])->name('attend.list');
        // Route::get('/attend/create', [ClockActionController::class, 'create'])->name('attend.create');

        Route::get('/clock', [ClockActionController::class, 'clockPage'])->name('clock');
        Route::post('/clock-in', [ClockActionController::class, 'clockIn'])->name('clock.in');
        Route::post('/clock-out', [ClockActionController::class, 'clockOut'])->name('clock.out');
        Route::get('/clock-history', [ClockActionController::class, 'clockHistory'])->name('clock.history');

        Route::put('/profile/image', [HomeController::class, 'updateImage'])->name('profile.updateimage');
        Route::get('/profile/password', [HomeController::class, 'password'])->name('profile.password');
        Route::post('/profile/password', [HomeController::class, 'updatePassword'])->name('profile.updatepassword');
    });
});
