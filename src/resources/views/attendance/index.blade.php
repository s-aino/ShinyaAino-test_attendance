@extends('layouts.app')

@section('title', '勤怠登録')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<div class="page-container">

    <div class="attendance-main">
        <div class="attendance-status">
            {{ $statusLabel }}
        </div>

        {{-- 日付・時刻 --}}
        <div class="attendance-time">
            <p class="attendance-date">
                {{ $date->isoFormat('YYYY年M月D日(ddd)') }}
            </p>
            <p class="attendance-clock">{{ $time->format('H:i') }}</p>
        </div>

        {{-- ボタンエリア --}}
        <div class="attendance-buttons">

            @if (! $attendance)
            {{-- 未出勤 --}}
            <form method="POST" action="{{ route('attendance.start') }}">
                @csrf
                <button type="submit" class="btn btn-black">出勤</button>
            </form>
            {{-- 出勤中（休憩していない） --}}
            @elseif ($attendance && ! $attendance->clock_out && ! $onBreak)

            <form method="POST" action="{{ route('attendance.end') }}">
                @csrf
                <button type="submit" class="btn btn-black">
                    退勤
                </button>
            </form>
            <form method="POST" action="{{ route('break.start') }}">
                @csrf
                <button type="submit" class="btn btn-white">
                    休憩入
                </button>
            </form>



            {{-- 休憩中 --}}
            @elseif ($attendance && ! $attendance->clock_out && $onBreak)
            <form method="POST" action="{{ route('break.end') }}">
                @csrf
                <button type="submit" class="btn btn-white">
                    休憩戻
                </button>
            </form>

            {{-- 退勤済み --}}
            @else
            <p class="attendance-finish-message">お疲れ様でした。</p> @endif

        </div>
    </div>
</div>
@endsection