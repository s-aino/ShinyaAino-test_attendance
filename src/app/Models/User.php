<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;

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
    public function attendanceCorrections()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
