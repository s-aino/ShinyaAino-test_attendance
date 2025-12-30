<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceListController extends Controller
{
    public function index(Request $request)
    {
        // 表示対象の月（YYYY-MM）
        $month = $request->query('month', now()->format('Y-m'));

        // 月の開始日・終了日
        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth   = Carbon::parse($month)->endOfMonth();

        // ログインユーザー
        $user = Auth::user();

        // 勤怠取得（自分の分のみ）＋ 休憩を eager load
        $attendanceMap = Attendance::with('breaks')
            ->where('user_id', $user->id)
            ->whereBetween('date', [
                $startOfMonth->toDateString(),
                $endOfMonth->toDateString(),
            ])
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy(function ($attendance) {
                return $attendance->date->toDateString();
            });
            
        return view('attendance.list', [
            'attendanceMap' => $attendanceMap,
            'currentMonth'  => $month,
            'startOfMonth'  => $startOfMonth,
            'endOfMonth'    => $endOfMonth,
        ]);
    }
    public function detail($id)
    {
        $attendance = Attendance::with('breaks')
            ->where('id', $id)
            ->where('user_id', auth()->id()) // 自分の勤怠だけ
            ->firstOrFail();

        return view('attendance.detail', compact('attendance'));
    }
}
