<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function index()
    {
        return '管理者 勤怠一覧（仮）';
    }

    public function show($id)
    {
        return "管理者 勤怠詳細 ID={$id}（仮）";
    }
}
