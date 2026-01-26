<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AttendanceCorrectionRequestController as AdminAttendanceCorrectionRequestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\AttendanceCorrectionRequestController;
use App\Http\Controllers\BreakTimeController;
use App\Http\Controllers\Admin\StaffController as AdminstaffController;
/*
|--------------------------------------------------------------------------
| Guest Routes（未ログイン）
|--------------------------------------------------------------------------
*/

// ルートアクセス時はログインへ
Route::get('/', function () {
    return redirect('/login');
});

// 管理者ログイン画面
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])
    ->name('admin.login');

// ログイン処理（全ユーザー共通）
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->name('login');

/*
|--------------------------------------------------------------------------
| Authenticated Routes（一般ユーザー）
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // 勤怠登録画面
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    Route::post('/attendance/start', [AttendanceController::class, 'start'])
        ->name('attendance.start');

    Route::post('/attendance/end', [AttendanceController::class, 'end'])
        ->name('attendance.end');

    // 休憩
    Route::post('/attendance/break/start', [BreakTimeController::class, 'start'])
        ->name('break.start');

    Route::post('/attendance/break/end', [BreakTimeController::class, 'end'])
        ->name('break.end');

    // 勤怠一覧
    Route::get('/attendance/list', [AttendanceListController::class, 'index'])
        ->name('attendance.list');

    // 勤怠詳細
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])
        ->name('attendance.show');

    // 修正申請
    Route::post('/attendance/{attendance}/correction', [AttendanceCorrectionRequestController::class, 'store'])
        ->name('attendance.correction.store');

    // 申請一覧(一般スタッフ・管理者)
    Route::get('/stamp_correction_request/list', [AttendanceCorrectionRequestController::class, 'index'])
        ->name('correction_requests.index');
});

/*
|--------------------------------------------------------------------------
| Admin Routes（管理者専用）
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    // ->name('admin.') 
    ->group(function () {

        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])
            ->name('admin.attendance.list');

        Route::get('/attendance/{id}', [AdminAttendanceController::class, 'show'])
            ->name('admin.attendance.show');

        Route::put('/attendance/{id}', [AdminAttendanceController::class, 'update'])
            ->name('admin.attendance.update');

        Route::get('/staff/list', [AdminStaffController::class, 'index'])
            ->name('admin.staff.list');

        Route::get('/attendance/staff/{user}', [AdminStaffController::class, 'show'])
            ->name('admin.attendance.staff.show');
    });
Route::middleware(['auth', 'admin'])
    ->group(function () {
        Route::get(
            '/stamp_correction_request/approve/{attendance_correct_request_id}',
            [AdminAttendanceCorrectionRequestController::class, 'edit']
        )->name('stamp_correction_request.approve');

        Route::put(
            '/stamp_correction_request/approve/{attendance_correction_request}',
            [AdminAttendanceCorrectionRequestController::class, 'approve']
        )->name('stamp_correction_request.approve.update');
    });
