<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    /**
     * 管理者ログイン画面表示
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }
}
