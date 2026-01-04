<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
            'requested_data' => $requestedData,
            'status'        => 'pending',
        ]);
        return redirect()
            ->route('attendance.show', $attendanceId);
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');

        $correctionRequests = AttendanceCorrection::where('attendance_correction_requests.user_id', auth()->id())
            ->where('attendance_correction_requests.status', $status)
            ->join(
                'attendances',
                'attendance_correction_requests.attendance_id',
                '=',
                'attendances.id'
            )
            ->orderBy('attendances.date', 'asc')
            ->select('attendance_correction_requests.*')
            ->with(['attendance'])
            ->get();

        return view('correction_requests.index', compact(
            'correctionRequests',
            'status'
        ));
    }
}
