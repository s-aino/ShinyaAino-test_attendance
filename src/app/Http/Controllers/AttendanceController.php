<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{

    /**
     * 勤怠登録画面（表示）
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        return view('attendance.index', [
            'attendance' => $attendance,
            'date' => $today,
            'time' => $now,
        ]);
    }
    /**
     * 出勤処理
     */
    public function start()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 同日すでに勤怠レコードがあるか確認
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if ($attendance) {
            return back()->withErrors([
                'attendance' => '本日はすでに出勤済みです。',
            ]);
        }

        Attendance::create([
            'user_id'  => $user->id,
            'date'     => $today,
            'clock_in' => Carbon::now(),
        ]);

        return back()->with('status', '出勤しました。');
    }

    public function end()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // 出勤していない or すでに退勤済み
        if (! $attendance || $attendance->clock_out) {
            return back()->withErrors([
                'attendance' => '退勤できません。',
            ]);
        }

        $attendance->update([
            'clock_out' => Carbon::now(),
        ]);

        return back()->with('status', '退勤しました。');
    }
}
