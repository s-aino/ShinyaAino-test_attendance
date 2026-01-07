<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        // 対象日（デフォルト：今日）
        $date = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : Carbon::today();

        // 当日の勤怠（ユーザー付き）
        $attendances = Attendance::with('user')
            ->whereDate('date', $date)
            ->get();

        return view('admin.attendance.index', [
            'date' => $date,
            'attendances' => $attendances,
        ]);
    }
    public function show($id)
    {
        $attendance = Attendance::with(['user', 'breaks', 'correctionRequests'])
            ->findOrFail($id);

        // 管理者でも申請中があれば編集不可
        $hasPendingRequest = $attendance->correctionRequests()
            ->where('status', 'pending')
            ->exists();

        $isPending = $hasPendingRequest;

        // 表示データを先に確定
        if ($isPending) {
            $latestCorrection = $attendance->correctionRequests()
                ->latest()
                ->first();

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
                'reason' => $attendance->reason ?? '',
            ];
        }

        // breakRows は displayData から作る

        // ① まず breaks を collection に
        $breakRows = collect($displayData['breaks'] ?? [])
            // ② 「完全に空の行（予備行）」を除外（過去データ対策）
            ->filter(function ($break) {
                return !(
                    empty($break['start']) &&
                    empty($break['end'])
                );
            })
            ->values();

        // ③ 通常時のみ「予備 1 行」を追加
        if (!$isPending) {
            $breakRows->push([
                'start' => '',
                'end'   => '',
            ]);
        }
        return view('admin.attendance.show', [
            'attendance'        => $attendance,
            'displayData'       => $displayData,
            'breakRows'         => $breakRows,
            'hasPendingRequest' => $hasPendingRequest,
            'isPending'         => $isPending,
        ]);
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::with(['breaks', 'correctionRequests'])
            ->findOrFail($id);

        // 申請中があれば管理者でも修正不可
        $hasPendingRequest = $attendance->correctionRequests()
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingRequest) {
            return back()->withErrors([
                'error' => '申請中のため修正できません。',
            ]);
        }

        DB::transaction(function () use ($request, $attendance) {

            $attendance->update([
                'clock_in'  => $request->clock_in,
                'clock_out' => $request->clock_out,
                'reason'    => $request->reason,
            ]);

            // 休憩は一度削除して再作成
            $attendance->breaks()->delete();

            foreach ($request->breaks ?? [] as $break) {
                if ($break['start'] && $break['end']) {
                    $attendance->breaks()->create([
                        'break_start' => $break['start'],
                        'break_end'   => $break['end'],
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.attendance.list')
            ->with('message', '勤怠情報を修正しました');
    }
}
