<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'ログイン情報が正しくありません。',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        $loginType = $request->input('login_type');

        //  管理者ログイン画面から
        if ($loginType === 'admin') {

            if ($user->role === 'staff') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => '管理者として登録されていません。',
                ]);
            }

            // admin のみここに来る
            return redirect()->route('admin.attendance.list');
        }
        //  一般ログイン画面
        if ($user->role === 'admin') {
            return redirect()->route('admin.attendance.list');
        }

        return redirect()->route('attendance.index');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
