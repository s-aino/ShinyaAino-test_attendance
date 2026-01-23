<?php

namespace App\Models;

use App\Models\AttendanceCorrection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
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

    /* =====================
     * 基本判定
     * ===================== */

    public function hasWorked(): bool
    {
        return !is_null($this->clock_in);
    }

    public function hasFinished(): bool
    {
        return !is_null($this->clock_out);
    }

    // 休憩合計（分）
    public function totalBreakMinutes(): int
    {
        return $this->breaks
            ->whereNotNull('break_end')
            ->sum(function ($break) {
                return $break->break_start->diffInMinutes($break->break_end);
            });
    }

    // 実働合計（分）
    //退勤が無ければ null
    public function totalWorkMinutes(): ?int
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }

        $totalMinutes = $this->clock_in->diffInMinutes($this->clock_out);
        $breakMinutes = $this->totalBreakMinutes();

        return max(0, $totalMinutes - $breakMinutes);
    }

    // 勤怠は複数の修正申請を持つ（履歴として）
    public function correctionRequests()
    {
        return $this->hasMany(AttendanceCorrection::class);
    }

    /* =====================
     * 表示用（← ここが重要）
     * ===================== */

    public function breakTimeFormatted(): string
    {
        if (!$this->hasFinished()) {
            return '';
        }

        $minutes = $this->totalBreakMinutes();
        return sprintf('%d:%02d', floor($minutes / 60), $minutes % 60);
    }

    public function workTimeFormatted(): string
    {
        $minutes = $this->totalWorkMinutes();
        if ($minutes === null) {
            return '';
        }

        return sprintf('%d:%02d', floor($minutes / 60), $minutes % 60);
    }

    public function clockInFormatted(): string
    {
        return $this->clock_in?->format('H:i') ?? '';
    }

    public function clockOutFormatted(): string
    {
        return $this->clock_out?->format('H:i') ?? '';
    }

    // 表示用：休憩一覧（申請中なら空行を1つ足す）
    public function breaksForDisplay(bool $isPending)
    {
        $breaks = $this->breaks()->orderBy('break_start')->get();

        if ($isPending) {
            $breaks->push(null); // 入力用の空行
        }

        return $breaks;
    }

    public function toDisplayArray(): array
    {
        return [
            'clock_in' => $this->clockInFormatted(),
            'clock_out' => $this->clockOutFormatted(),
            'breaks' => $this->breaks->map(fn($b) => [
                'start' => optional($b->break_start)->format('H:i'),
                'end'   => optional($b->break_end)->format('H:i'),
            ])->toArray(),
        ];
    }
}
