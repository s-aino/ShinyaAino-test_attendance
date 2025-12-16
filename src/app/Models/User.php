<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    // ユーザーは複数の勤怠を持つ
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // ユーザーは複数の修正申請を出す
    public function attendanceCorrectionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }
}