<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AttendanceApprovalRequest;

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

    public function update(
        AttendanceApprovalRequest $request,
        $id
    ) {
        // dd($request->all());
        $attendance = Attendance::with(['breaks', 'correctionRequests'])
            ->findOrFail($id);

        // 承認待ち申請
        $pending = $attendance->correctionRequests()
            ->where('status', 'pending')
            ->latest()
            ->first();

        // ============================
        // 更新データを決定（必ず validated）
        // ============================
        if ($pending) {
            // 承認：申請データを request に流し直す
            $request->merge($pending->requested_data);
            $data = $request->validated();
        } else {
            // 管理者の直接修正
            $data = $request->validated();
        }

        DB::transaction(function () use ($attendance, $data, $pending) {

            // 勤怠本体
            $attendance->update([
                'clock_in'  => $data['clock_in']  ?? null,
                'clock_out' => $data['clock_out'] ?? null,
                'reason'    => $data['reason']    ?? null,
            ]);

            // 休憩（全削除 → 再作成）
            $attendance->breaks()->delete();

            $date = $attendance->date instanceof Carbon
                ? $attendance->date->format('Y-m-d')
                : $attendance->date;

            foreach ($data['breaks'] ?? [] as $break) {

                if (empty($break['start']) && empty($break['end'])) {
                    continue;
                }

                $attendance->breaks()->create([
                    'break_start' => $break['start']
                        ? Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $break['start'])
                        : null,

                    'break_end' => $break['end']
                        ? Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $break['end'])
                        : null,
                ]);
            }

            // 申請があれば承認済みに
            if ($pending) {
                $pending->update(['status' => 'approved']);
            }
        });

        return redirect()
            ->route('admin.attendance.show', $attendance->id)
            ->with('success', '勤怠情報を更新しました');
    }
}
