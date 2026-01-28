<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StaffController extends Controller
{
    public function index()
    {
        // staff ロールのユーザーのみ取得
        $staffs = User::where('role', 'staff')
            ->orderBy('name')
            ->get();

        return view('admin.staff.index', compact('staffs'));
    }


    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        // 表示月
        $month = $request->query('month', now()->format('Y-m'));

        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

        // 指定スタッフの勤怠を取得
        $attendanceMap = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ])
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy(fn($attendance) => $attendance->date->toDateString());

        // rows を構築
        $rows = collect();

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $rows->push([
                'date' => $date->copy(),
                'attendance' => $attendanceMap->get($date->toDateString()),
            ]);
        }

        return view('admin.attendance.staff.show', [
            'user'         => $user,
            'rows'         => $rows,
            'currentMonth' => $month,
        ]);
    }

    public function csv(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $month = $request->query('month', now()->format('Y-m'));

        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

        // 勤怠を日付キーの辞書に
        $attendanceMap = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ])
            ->get()
            ->keyBy(fn($attendance) => $attendance->date->toDateString());

        // rows を作る（画面と同じ考え方）
        $rows = collect();

        for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
            $rows->push([
                'date'       => $date->copy(),
                'attendance' => $attendanceMap->get($date->toDateString()),
            ]);
        }

        $fileName = sprintf(
            '%s_%s_attendance.csv',
            $user->name,
            $month // 例: 2026-01
        );

        // CSVレスポンス
        return new StreamedResponse(function () use ($rows, $user, $month) {

            // ▼ 書き込み先を「HTTPレスポンス（= ブラウザ）」にする
            // ここに書いた内容が、そのままCSVとしてダウンロードされる
            $stream = fopen('php://output', 'w');

            // ▼ BOMを書き込む（Excelで日本語が文字化けしないようにする）
            fwrite($stream, "\xEF\xBB\xBF");

            // ヘッダー行
            fputcsv($stream, [
                '氏名',
                '日付',
                '出勤時間',
                '退勤時間',
                '休憩時間',
                '実働時間'
            ]);

            foreach ($rows as $row) {

                // この日の勤怠（なければ null）
                $attendance = $row['attendance'];

                fputcsv($stream, [
                    $user->name,
                    $row['date']->format('Y/m/d'),
                    $attendance?->clockInFormatted(),
                    $attendance?->clockOutFormatted(),
                    $attendance?->breakTimeFormatted(),
                    $attendance?->workTimeFormatted(),
                ]);
            }


            fclose($stream);
        }, 200, [
            // ▼ レスポンスはCSVですよ、という指定
            'Content-Type'        => 'text/csv; charset=UTF-8',
            // ▼ ブラウザに「ダウンロードさせる」ための指定
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
