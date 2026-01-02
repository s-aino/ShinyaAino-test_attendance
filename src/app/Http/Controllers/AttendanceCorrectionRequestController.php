<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceCorrectionRequest;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Facades\Auth;

class AttendanceCorrectionRequestController extends Controller
{
    public function store(AttendanceCorrectionRequest $request, $attendanceId)
    {
        $user = Auth::user();

        $attendance = Attendance::where('id', $attendanceId)
            ->where('user_id', $user->id)
            ->first();

        // バリデーション済みデータ取得
        $validated = $request->validated();

        // requested_data にまとめる（COACHTECH想定）
        $requestedData = [
            'clock_in'  => $validated['clock_in']  ?? null,
            'clock_out' => $validated['clock_out'] ?? null,
            'breaks'    => $validated['breaks']    ?? null,
            'reason'    => $validated['reason'],
        ];

        AttendanceCorrection::create([
            'attendance_id' => $attendance?->id, // 勤務していない日は null
            'user_id'       => $user->id,
            'requested_data'=> $requestedData,
            'status'        => 'pending',
        ]);
        return redirect()
            ->route('attendance.show', $attendanceId);
    }
}
