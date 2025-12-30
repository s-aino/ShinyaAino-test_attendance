@extends('layouts.app')

@section('title', '勤怠一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<div class="attendance-list">

    {{-- タイトル --}}
    <h2 class="page-title">勤怠一覧</h2>

    {{-- 月切り替え --}}
    <div class="attendance-list__month">

        {{-- 前月 --}}
        <a href="{{ route('attendance.list', ['month' => \Carbon\Carbon::parse($currentMonth)->subMonth()->format('Y-m')]) }}"
            class="month-nav month-nav--prev">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                <path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z" />
            </svg>
            <span class="month-nav__text">前月</span>
        </a>

        {{-- 今月 --}}
        <span class="attendance-list__current-month">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                <path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z" />
            </svg>
            {{ \Carbon\Carbon::parse($currentMonth)->format('Y/m') }}
        </span>

        {{-- 翌月 --}}
        <a href="{{ route('attendance.list', ['month' => \Carbon\Carbon::parse($currentMonth)->addMonth()->format('Y-m')]) }}"
            class="month-nav month-nav--next">
            <span class="month-nav__text">翌月</span>
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z" />
            </svg>
        </a>
    </div>

    {{-- 一覧テーブル --}}
    <div class="attendance-table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                @php
                $hasWork = $attendance->clock_in !== null;
                @endphp

                <tr>
                    {{-- 日付（06/01(木) 形式） --}}
                    <td>
                        {{ \Carbon\Carbon::parse($attendance->date)->format('m/d') }}
                        ({{ \Carbon\Carbon::parse($attendance->date)->isoFormat('ddd') }})
                    </td>

                    {{-- 出勤 --}}
                    <td>
                        {{ $hasWork ? $attendance->clock_in->format('H:i') : '' }}
                    </td>

                    {{-- 退勤 --}}
                    <td>
                        {{ $attendance->clock_out ? $attendance->clock_out->format('H:i') : '' }}
                    </td>

                    {{-- 休憩（hh:mm） --}}
                    <td>
                        @if ($attendance->clock_out)
                        @php
                        $breakMinutes = $attendance->totalBreakMinutes();
                        $h = floor($breakMinutes / 60);
                        $m = $breakMinutes % 60;
                        @endphp
                        {{ sprintf('%d:%02d', $h, $m) }}
                        @endif
                    </td>

                    {{-- 勤務合計（※ 後で実装、今は空白） --}}
                    <td></td>

                    {{-- 詳細 --}}
                    <td>
                        <a href="{{ route('attendance.detail', $attendance->id) }}" class="detail-link">
                            詳細
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection