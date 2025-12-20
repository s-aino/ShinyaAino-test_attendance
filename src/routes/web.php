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

/*
|--------------------------------------------------------------------------
| 仮画面（あとで Blade / Controller に置き換える）
|--------------------------------------------------------------------------
*/
Route::get('/attendance', [AttendanceController::class, 'index'])
    ->middleware(['auth']);

Route::get('/admin', function () {
    return '管理者画面';
})->middleware(['auth']);
