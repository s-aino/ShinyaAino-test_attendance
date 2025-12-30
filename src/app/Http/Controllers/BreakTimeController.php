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
            return back();
        }

        $onBreak = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->exists();

        if ($onBreak) {
            return back();
        }


        // 休憩に入る
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::now(),
        ]);

        return back();
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
            return back();
        }

        $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->first();

        if (! $break) {
            return back();
        }

        // 休憩から戻る
        $break->update([
            'break_end' => Carbon::now(),
        ]);

        return back();
    }
}
