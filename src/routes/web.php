<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

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
});
// Route::get('/admin', function () {
//     return '管理者画面';
// })->middleware(['auth']);
