@extends('layouts.app')

@section('title', '勤怠登録')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<div class="attendance-container">

    <h1 class="attendance-title">勤怠打刻</h1>

    {{-- 日付・時刻 --}}
    <div class="attendance-time">
        <p class="attendance-date">{{ $date->format('Y-m-d') }}</p>
        <p class="attendance-clock">{{ $time->format('H:i') }}</p>
    </div>

    {{-- ボタンエリア --}}
    <div class="attendance-buttons">

        @if ($attendance)
        {{-- 出勤済み（今日はもう出勤している） --}}
        <p>本日は出勤済みです。</p>
        @else
        {{-- 未出勤 --}}
        <form method="POST" action="{{ route('attendance.start') }}">
            @csrf
            <button type="submit" class="btn btn-black">
                出勤
            </button>
        </form>
        @endif

    </div>

</div>
@endsection