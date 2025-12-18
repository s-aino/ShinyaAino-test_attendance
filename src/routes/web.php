<?php

use Illuminate\Support\Facades\Route;

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

    return redirect('/user');
})->middleware(['auth']);

/*
|--------------------------------------------------------------------------
| 仮画面（あとで Blade / Controller に置き換える）
|--------------------------------------------------------------------------
*/
Route::get('/user', function () {
    return '一般ユーザー画面';
})->middleware(['auth']);

Route::get('/admin', function () {
    return '管理者画面';
})->middleware(['auth']);
