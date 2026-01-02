<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrection extends Model
{
    use HasFactory;

    protected $table = 'attendance_correction_requests';


    protected $fillable = [
        'attendance_id',
        'user_id',
        'requested_data',
        'status',
    ];

    protected $casts = [
        'requested_data' => 'array',
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
