<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\BreakTimeController;
use App\Http\Controllers\AttendanceListController;

/*
|--------------------------------------------------------------------------
| Top
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/login');
});

/*
|--------------------------------------------------------------------------
| Dashboard（role 分岐）
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'admin') {
        return redirect('/admin');
    }

    return redirect('/attendance');
})->middleware(['auth']);

Route::middleware(['auth'])->group(function () {

    // 勤怠登録画面（一般ユーザー）
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');
    Route::post('/attendance/start', [AttendanceController::class, 'start'])
        ->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'end'])
        ->name('attendance.end');
    Route::post('/attendance/break/start', [BreakTimeController::class, 'start'])
        ->name('break.start');

    Route::post('/attendance/break/end', [BreakTimeController::class, 'end'])
        ->name('break.end');

    // 勤怠一覧（一般ユーザー）
    Route::get('/attendance/list', [AttendanceListController::class, 'index'])
        ->name('attendance.list');

    // 勤怠詳細
    Route::get('/attendance/detail/{id}', [AttendanceListController::class, 'detail'])
        ->name('attendance.detail');
});
// Route::get('/admin', function () {
//     return '管理者画面';
// })->middleware(['auth']);
