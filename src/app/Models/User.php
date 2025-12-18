<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* relations */

    // ユーザーの勤怠（1日1件）
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // ユーザーの修正申請
    public function attendanceCorrectionRequests()
    {
        return $this->hasMany(AttendanceCorrectionRequest::class);
    }
}
