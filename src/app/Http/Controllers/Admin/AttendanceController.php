<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Http\Controllers\Controller;

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
        return "管理者 勤怠詳細 ID={$id}（仮）";
    }
}
