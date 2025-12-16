<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'requested_clock_in',
        'requested_clock_out',
        'requested_breaks',
        'reason',
        'status',
    ];

    protected $casts = [
        'requested_breaks' => 'array',
    ];

    // 修正申請は1つの勤怠に属する
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    // 修正申請は1人のユーザーが出す
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}