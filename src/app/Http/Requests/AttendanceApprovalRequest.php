<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class AttendanceApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in'  => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'clock_out' => ['required', 'regex:/^\d{2}:\d{2}$/'],

            'breaks' => ['nullable', 'array'],
            'breaks.*.start' => ['nullable', 'regex:/^\d{2}:\d{2}$/'],
            'breaks.*.end'   => ['nullable', 'regex:/^\d{2}:\d{2}$/'],

            'reason' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'clock_in.regex'        => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out.regex'       => '出勤時間もしくは退勤時間が不適切な値です',
            'breaks.*.start.regex'  => '休憩時間が不適切な値です',
            'breaks.*.end.regex'    => '休憩時間が不適切な値です',
            'reason.required'       => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            $clockIn  = $this->input('clock_in');
            $clockOut = $this->input('clock_out');

            // 出勤 >= 退勤
            if ($clockIn && $clockOut && $clockIn >= $clockOut) {
                $validator->errors()->add(
                    'clock_in',
                    '出勤時間もしくは退勤時間が不適切な値です'
                );
            }

            foreach ($this->input('breaks', []) as $i => $b) {

                $start = $b['start'] ?? null;
                $end   = $b['end']   ?? null;

                // 片方だけ入力
                if (($start && !$end) || (!$start && $end)) {
                    $validator->errors()->add(
                        "breaks.$i.start",
                        '休憩時間が不適切な値です'
                    );
                    continue;
                }

                if (!$start || !$end) continue;

                // 開始 >= 終了
                if ($start >= $end) {
                    $validator->errors()->add(
                        "breaks.$i.start",
                        '休憩時間が不適切な値です'
                    );
                }

                // 出勤前 / 退勤後
                if ($clockIn && $start < $clockIn) {
                    $validator->errors()->add(
                        "breaks.$i.start",
                        '休憩時間が不適切な値です'
                    );
                }

                if ($clockOut && $end > $clockOut) {
                    $validator->errors()->add(
                        "breaks.$i.end",
                        '休憩時間もしくは退勤時間が不適切な値です'
                    );
                }
            }
        });
    }
}
