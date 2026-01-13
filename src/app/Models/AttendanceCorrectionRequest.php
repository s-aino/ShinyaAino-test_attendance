<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;

    /**
     * 一括代入を許可するカラム
     */
    protected $fillable = [
        'attendance_id',
        'user_id',
        'status',
        'requested_data',
    ];
    /**
     * キャスト定義
     */
    protected $casts = [
        'requested_data' => 'array',
    ];
    /**
     * 勤怠（1申請は1勤怠に紐づく）
     */
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * 申請者ユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
