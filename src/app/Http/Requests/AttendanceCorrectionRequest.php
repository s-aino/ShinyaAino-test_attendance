<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttendanceCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in'  => ['nullable', 'regex:/^\d{2}:\d{2}$/'],
            'clock_out' => ['nullable', 'regex:/^\d{2}:\d{2}$/'],

            'breaks'            => ['nullable', 'array'],
            'breaks.*.start'    => ['nullable', 'regex:/^\d{2}:\d{2}$/'],
            'breaks.*.end'      => ['nullable', 'regex:/^\d{2}:\d{2}$/'],

            'reason' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            // 出勤・退勤
            'clock_in.regex'  => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.regex' => '出勤時間もしくは退勤時間が不適切な値です',

            // 休憩
            'breaks.*.start.regex' => '休憩時間が不適切な値です',
            'breaks.*.end.regex'   => '休憩時間が不適切な値です',

            // 備考
            'reason.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {

            $clockIn  = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            // ① 退勤前に出勤できない
            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            foreach ($this->input('breaks', []) as $i => $break) {
                $start = $break['start'] ?? null;
                $end   = $break['end']   ?? null;

                if (!$start || !$end) continue;

                // ② 休憩開始 >= 休憩終了
                if ($start >= $end) {
                    $validator->errors()->add(
                        "breaks.$i.start",
                        '休憩時間が不適切な値です'
                    );
                }

                // ③ 出勤前 or 退勤後の休憩禁止
                if (
                    ($clockIn && $start < $clockIn) ||
                    ($clockOut && $end > $clockOut)
                ) {
                    $validator->errors()->add(
                        "breaks.$i.start",
                        '休憩時間が不適切な値です'
                    );
                }
            }
        });
    }
}
