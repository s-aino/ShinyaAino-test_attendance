<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Carbon;

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


    public function show(Request $request, User $user)
    {
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

        // rows を構築（← ここが超重要）
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
}
