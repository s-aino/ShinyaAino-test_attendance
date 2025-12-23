<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BreakTimeController extends Controller
{
    /**
     * 休憩開始
     */
    public function start()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // 今日の勤怠を取得
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // 出勤していない or すでに休憩中
        if (! $attendance) {
            return back()->withErrors(['break' => '出勤していません。']);
        }

        $onBreak = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->exists();

        if ($onBreak) {
            return back()->withErrors(['break' => 'すでに休憩中です。']);
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::now(),
        ]);

        return back()->with('status', '休憩を開始しました。');
    }

    /**
     * 休憩終了
     */
    public function end()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (! $attendance) {
            return back()->withErrors(['break' => '出勤していません。']);
        }

        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->first();

        if (! $break) {
            return back()->withErrors(['break' => '休憩中ではありません。']);
        }

        $break->update([
            'break_end' => Carbon::now(),
        ]);

        return back()->with('status', '休憩を終了しました。');
    }
}
