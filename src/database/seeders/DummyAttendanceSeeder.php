<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class DummyAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $userIds = [2, 7, 8];

        $periods = [
            ['2025-12-01', '2025-12-31'],
            ['2026-01-01', '2026-01-29'],
        ];

        foreach ($userIds as $userId) {

            foreach ($periods as [$start, $end]) {

                $date = Carbon::parse($start);

                while ($date->lte(Carbon::parse($end))) {

                    if ($date->isWeekday()) {

                        // 🔒 既存チェック（二重登録防止）
                        $exists = Attendance::where('user_id', $userId)
                            ->whereDate('date', $date->toDateString())
                            ->exists();

                        if ($exists) {
                            $date->addDay();
                            continue;
                        }

                        // 出勤 8〜10時
                        $clockIn = $date->copy()->setTime(
                            rand(8, 10),
                            rand(0, 59)
                        );

                        // 🎲 退勤し忘れを混ぜる（20%）
                        $forgotClockOut = rand(1, 100) <= 20;

                        $clockOut = null;

                        if (!$forgotClockOut) {
                            $clockOut = $date->copy()->setTime(
                                rand(16, 18),
                                rand(0, 59)
                            );

                            if ($clockOut->lte($clockIn)) {
                                $clockOut = $clockIn->copy()->addHours(8);
                            }
                        }

                        $attendance = Attendance::create([
                            'user_id' => $userId,
                            'date' => $date->toDateString(),
                            'clock_in' => $clockIn,
                            'clock_out' => $clockOut,
                        ]);

                        // 🟡 退勤ありのときだけ休憩を作る
                        if (!$forgotClockOut) {

                            $breakStart = $date->copy()->setTime(
                                rand(11, 13),
                                rand(0, 59)
                            );

                            $breakEnd = $breakStart->copy()->addMinutes(45);

                            BreakTime::create([
                                'attendance_id' => $attendance->id,
                                'break_start' => $breakStart,
                                'break_end' => $breakEnd,
                            ]);
                        }
                    }

                    $date->addDay();
                }
            }
        }
        $this->seedAinoSpecial();
    }

    private function seedAinoSpecial(): void
{
    $userId = 3;

    $start = Carbon::parse('2025-12-01');
    $end   = Carbon::parse('2026-01-29');

    $weekBuffer = [];

    $date = $start->copy();

    while ($date->lte($end)) {

        $weekKey = $date->format('o-W'); // 年＋週番号

        if (!isset($weekBuffer[$weekKey])) {
            $weekBuffer[$weekKey] = [];
        }

        // 土日は必ず候補
        if ($date->isWeekend()) {
            $weekBuffer[$weekKey][] = $date->copy();
        } else {
            // 平日は50%で候補
            if (rand(1, 100) <= 50) {
                $weekBuffer[$weekKey][] = $date->copy();
            }
        }

        $date->addDay();
    }

    // 週5に調整
    foreach ($weekBuffer as $weekDates) {

        shuffle($weekDates);
        $workDays = array_slice($weekDates, 0, 5);

        foreach ($workDays as $workDate) {

            // 🔒 二重防止
            $exists = Attendance::where('user_id', $userId)
                ->whereDate('date', $workDate->toDateString())
                ->exists();

            if ($exists) continue;

            // 出勤 7時台
            $clockIn = $workDate->copy()->setTime(7, rand(0, 59));

            // 🎲 退勤忘れ 25%
            $forgotClockOut = rand(1, 100) <= 25;

            $clockOut = null;

            if (!$forgotClockOut) {
                $clockOut = $workDate->copy()->setTime(16, rand(0, 59));
            }

            $attendance = Attendance::create([
                'user_id' => $userId,
                'date' => $workDate->toDateString(),
                'clock_in' => $clockIn,
                'clock_out' => $clockOut,
            ]);

            // 休憩① 11時頃
            $b1Start = $workDate->copy()->setTime(11, rand(0, 30));
            $b1End = rand(1, 100) <= 15
                ? null // 戻り忘れ
                : $b1Start->copy()->addMinutes(30);

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => $b1Start,
                'break_end' => $b1End,
            ]);

            // 休憩② 14時頃
            $b2Start = $workDate->copy()->setTime(14, rand(0, 30));
            $b2End = rand(1, 100) <= 15
                ? null
                : $b2Start->copy()->addMinutes(30);

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => $b2Start,
                'break_end' => $b2End,
            ]);
        }
    }
}

}
