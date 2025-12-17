<?php

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime:H:i',
        'clock_out' => 'datetime:H:i',
    ];

    // 勤怠は1人のユーザーに属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 勤怠は複数の休憩を持つ
    public function breaks()
    {
        return $this->hasMany(BreakTime::class);
    }

    // 勤怠は複数の修正申請を持つ（履歴として）
    public function correctionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }
}
