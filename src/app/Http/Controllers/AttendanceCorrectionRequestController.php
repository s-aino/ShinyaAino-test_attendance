<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttendanceCorrectionStoreRequest;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceCorrectionRequestController extends Controller
{
    /**
     * 修正申請の登録
     */
    public function store(AttendanceCorrectionStoreRequest $request, $attendanceId)
    {
        $user = Auth::user();

        $attendance = Attendance::where('id', $attendanceId)
            ->where('user_id', $user->id)
            ->first();

        if (! $attendance) {
            abort(404);
        }

        $validated = $request->validated();

        AttendanceCorrectionRequest::create([
            'attendance_id' => $attendance?->id,
            'user_id'       => $user->id,
            'status'        => 'pending',
            'requested_data' => [
                'clock_in'  => $validated['clock_in']  ?? null,
                'clock_out' => $validated['clock_out'] ?? null,
                'breaks'    => $validated['breaks']    ?? null,
                'reason'    => $validated['reason'],
            ],
        ]);

        return redirect()->route('attendance.show', $attendanceId);
    }

    /**
     * 一般スタッフ・管理者 申請一覧
     */
    public function index(Request $request)
    {

        $status = $request->query('status', 'pending');

        $query = AttendanceCorrectionRequest::with('attendance.user')
            ->where('status', $status)
            ->orderBy('created_at', 'desc');

        // 一般スタッフだけ制限
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        $correctionRequests = $query->get();

        return view('correction_requests.index', compact(
            'correctionRequests',
            'status'
        ));
    }
}
