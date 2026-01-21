<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BreakTime;

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

    public function approve(AttendanceCorrectionRequest $correctionRequest)
    {
        DB::transaction(function () use ($correctionRequest) {

            // ① 申請データ取得
            $requestedData = $correctionRequest->requested_data;
            $attendance    = $correctionRequest->attendance;

            // ② 勤怠（出退勤）更新
            $attendance->update([
                'clock_in'  => $requestedData['clock_in'],
                'clock_out' => $requestedData['clock_out'],
            ]);

            // ③ 既存の休憩を全削除
            $attendance->breakTimes()->delete();

            // ④ 申請された休憩を再作成
            if (!empty($requestedData['breaks'])) {
                foreach ($requestedData['breaks'] as $break) {

                    // start / end が両方あるものだけ登録
                    if ($break['start'] && $break['end']) {
                        BreakTime::create([
                            'attendance_id' => $attendance->id,
                            'break_start'   => $break['start'],
                            'break_end'     => $break['end'],
                        ]);
                    }
                }
            }

            // ⑤ 申請ステータスを approved に更新
            $correctionRequest->update([
                'status' => 'approved',
            ]);
        });

        return redirect()
            ->route('stamp_correction_request.approve', $correctionRequest->id)
            ->with('success', '勤怠修正申請を承認しました');
    }
}
