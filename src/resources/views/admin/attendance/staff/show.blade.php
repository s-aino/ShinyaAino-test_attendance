@extends('layouts.app')

@section('title', $user->name . 'さんの勤怠')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance-common.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<div class="page-container">

    {{-- タイトル --}}
    <div class="page-title-wrap">
        <h2 class="page-title">{{ $user->name }}さんの勤怠</h2>
    </div>
    {{-- 月切り替え --}}
    <div class="attendance-list__month">

        {{-- 前月 --}}
        <a href="{{ route('admin.attendance.staff.show', ['user'  => $user->id,'month' => \Carbon\Carbon::parse($currentMonth)->subMonth()->format('Y-m')]) }}"
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
        <a href="{{ route('admin.attendance.staff.show', ['user'  => $user->id,'month' => \Carbon\Carbon::parse($currentMonth)->addMonth()->format('Y-m')]) }}"
            class="month-nav month-nav--next">
            <span class="month-nav__text">翌月</span>
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#1f1f1f">
                <path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z" />
            </svg>
        </a>
    </div>

    {{-- 一覧テーブル --}}
    <div class="attendance-table-wrapper">
        <table class="attendance-table list-table">
            <thead>
                <tr>
                    <th class="col-date">日付</th>
                    <th class="col-time">出勤</th>
                    <th class="col-time">退勤</th>
                    <th class="col-time">休憩</th>
                    <th class="col-total">合計</th>
                    <th class="col-detail">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                @php
                $attendance = $row['attendance'];
                @endphp
                <tr>
                    {{-- 日付 --}}
                    <td class="col-date">
                        {{ $row['date']->format('m/d') }}
                        ({{ $row['date']->isoFormat('ddd') }})
                    </td>

                    {{-- 出勤 --}}
                    <td class="col-time">
                        {{ $attendance?->clockInFormatted() }}
                    </td>

                    {{-- 退勤 --}}
                    <td class="col-time">
                        {{ $attendance?->clockOutFormatted() }}
                    </td>

                    {{-- 休憩 --}}
                    <td class="col-time">
                        {{ $attendance?->breakTimeFormatted() }}
                    </td>

                    {{-- 合計 --}}
                    <td class="col-time">
                        {{ $attendance?->workTimeFormatted() }}
                    </td>

                    {{-- 詳細 --}}
                    <td class="col-detail">
                        @if($attendance)
                        <a href="{{ route('admin.attendance.show', $attendance->id) }}"
                            class="detail-link">
                            詳細
                        </a>
                        @else
                        <span class="detail-link is-disabled">
                            詳細
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="attendance-action">
        <a
            href="{{ route('admin.attendance.staff.csv', [
      'user' => $user->id,
      'month' => $currentMonth
  ]) }}"
            class="btn btn--black btn-fix btn-csv"> CSV出力
        </a>

    </div>
    @endsection