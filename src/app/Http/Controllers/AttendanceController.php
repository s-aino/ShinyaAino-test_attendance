<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceCorrection;
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

        $onBreak = false;

        if ($attendance) {
            $onBreak = \App\Models\BreakTime::where('attendance_id', $attendance->id)
                ->whereNull('break_end')
                ->exists();
        }

        $statusLabel = '勤務外';

        if (! $attendance) {
            $statusLabel = '勤務外';
        } elseif ($attendance->clock_out) {
            $statusLabel = '退勤済';
        } elseif ($onBreak) {
            $statusLabel = '休憩中';
        } else {
            $statusLabel = '出勤中';
        }



        return view('attendance.index', [
            'attendance' => $attendance,
            'date' => $today,
            'time' => $now,
            'onBreak' => $onBreak,
            'statusLabel' => $statusLabel,
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
            return back();
        }

        Attendance::create([
            'user_id'  => $user->id,
            'date'     => $today,
            'clock_in' => Carbon::now(),
        ]);

        return back();
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
            return back();
        }

        $attendance->update([
            'clock_out' => Carbon::now(),
        ]);

        return back();
    }

    public function show($id)
    {
        $user = Auth::user();

        $attendance = Attendance::with(['breaks', 'correctionRequests'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();


        // 勤務していない日
        if (! $attendance) {
            abort(404);
        }

        $latestCorrection = $attendance->correctionRequests()
            ->latest()
            ->first();

        $isPending = $latestCorrection && $latestCorrection->status === 'pending';

        $breakRows = $attendance
            ? $attendance->breaksForDisplay($isPending)
            : collect();

        // 表示用データをここで一本化
        if ($isPending) {
            $displayData = $latestCorrection->requested_data;
        } else {
            $displayData = [
                'clock_in'  => optional($attendance->clock_in)->format('H:i'),
                'clock_out' => optional($attendance->clock_out)->format('H:i'),
                'breaks'    => $attendance->breaks->map(function ($break) {
                    return [
                        'start' => optional($break->break_start)->format('H:i'),
                        'end'   => optional($break->break_end)->format('H:i'),
                    ];
                })->toArray(),
                'reason' => '',
            ];
        }

        return view('attendance.show', [
            'attendance'  => $attendance,
            'isPending'   => $isPending,
            'displayData' => $displayData,
            'breakRows'  => $breakRows,
        ]);
    }
}
