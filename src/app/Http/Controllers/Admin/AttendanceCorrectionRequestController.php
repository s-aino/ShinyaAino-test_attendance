<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Http\Request;

class AttendanceCorrectionRequestController extends Controller
{
    /**
     * 管理者用 申請一覧
     */
    public function index(Request $request)
    {
        dd('admin index reached');
        // 表示切替（デフォルトは pending）
        $status = $request->query('status', 'pending');

        $correctionRequests = AttendanceCorrectionRequest::with([
            'attendance.user',
        ])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')->get();

        return view('admin.correction_requests.index', compact(
            'correctionRequests',
            'status'
        ));
    }
}
