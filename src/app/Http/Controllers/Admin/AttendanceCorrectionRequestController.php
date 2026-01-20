<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Http\Request;

class AttendanceCorrectionRequestController extends Controller
{
    public function edit($id)
    {
        $correctionRequest = AttendanceCorrectionRequest::with('attendance.user')
            ->findOrFail($id);

        // requested_data を配列として扱う
        $requested = $correctionRequest->requested_data ?? [];

        // 休憩（空でないものだけ）
        $breaks = collect($requested['breaks'] ?? [])
            ->filter(fn($b) => !empty($b['start']) || !empty($b['end']))
            ->values();

        // 予備1行
        $breaks->push([
            'start' => '',
            'end'   => '',
        ]);

        return view('admin.correction_requests.edit', [
            'correctionRequest' => $correctionRequest,
            'requested'         => $requested,
            'breakRows'         => $breaks,
        ]);
    }

    public function approve(AttendanceCorrectionRequest $attendanceCorrectionRequest)
    {
        $attendanceCorrectionRequest->update([
            'status' => 'approved',
        ]);

        return redirect()
            ->route('correction_requests.index', ['status' => 'approved'])
            ->with('success', '勤怠修正申請を承認しました');
    }
}
