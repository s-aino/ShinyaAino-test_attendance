@extends('layouts.app')

@section('title', '勤怠詳細')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<div class="page-container">

    <div class="page-title-wrap">
        <h2 class="page-title">勤怠詳細</h2>
    </div>

    <div class="attendance-card">

        <form id="attendance-correction-form" method="POST" action="{{ route('attendance.correction.store', $attendance->id) }}">
            @csrf

            <table class="attendance-detail-table">
                <tbody>

                    {{-- 名前 --}}
                    <tr>
                        <th class="label">名前</th>
                        <td class="value">
                            <div class="value-text">
                                {{ $attendance->user->name }}
                            </div>
                        </td>
                    </tr>

                    {{-- 日付 --}}
                    <tr>
                        <th class="label">日付</th>
                        <td class="value">
                            <div class="date-block">
                                <span class="date-year">{{ optional($attendance->date)->format('Y年') }}</span>
                                <span class="date-md">{{ optional($attendance->date)->format('n月j日') }}</span>
                            </div>
                        </td>
                    </tr>

                    {{-- 出勤・退勤 --}}
                    <tr>
                        <th class="label">出勤・退勤</th>
                        <td class="value">
                            <div class="time-row">
                                <input
                                    type="text"
                                    name="clock_in"
                                    class="time-input {{ $isPending ? 'is-readonly' : '' }}"
                                    value="{{ old('clock_in', $displayData['clock_in'] ?? '') }}"
                                    {{ $isPending ? 'disabled' : '' }}>

                                <span class="time-sep">〜</span>

                                <input
                                    type="text"
                                    name="clock_out"
                                    class="time-input {{ $isPending ? 'is-readonly' : '' }}"
                                    value="{{ old('clock_out', $displayData['clock_out'] ?? '') }}"
                                    {{ $isPending ? 'disabled' : '' }}>
                            </div>

                            <div class="error-area">
                                @error('clock_in')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                                @error('clock_out')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </td>
                    </tr>

                    {{-- 休憩（既存 + 1） --}}
                    @php
                    $MAX_BREAKS = 5;

                    /**
                    * 実際に入力されている休憩のみカウント
                    */
                    $breaks = collect($displayData['breaks'])->filter(function ($b) {
                    return !empty($b['start']) || !empty($b['end']);
                    })->values();

                    $breakCount = $breaks->count();

                    /**
                    * 表示数の決定
                    * 申請前 : 実休憩 + 1（最大5）
                    * 申請中 : 実休憩のみ
                    */
                    $rows = $isPending
                    ? min($breakCount, $MAX_BREAKS)
                    : min($breakCount + 1, $MAX_BREAKS);
                    @endphp

                    @for ($i = 0; $i < $rows; $i++)
                        @php $break=$breaks[$i] ?? null; @endphp
                        <tr>
                        <th class="label">{{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}</th>
                        <td class="value">
                            <div class="time-row">
                                <input
                                    type="text"
                                    name="breaks[{{ $i }}][start]"
                                    class="time-input {{ $isPending ? 'is-readonly' : '' }}"
                                    value="{{ old("breaks.$i.start", $break['start'] ?? '') }}"
                                    {{ $isPending ? 'disabled' : '' }}>
                                <span class="time-sep">〜</span>
                                <input
                                    type="text"
                                    name="breaks[{{ $i }}][end]"
                                    class="time-input {{ $isPending ? 'is-readonly' : '' }}"
                                    value="{{ old("breaks.$i.end", $break['end'] ?? '') }}"
                                    {{ $isPending ? 'disabled' : '' }}>
                            </div>

                            <div class="error-area">
                                @error("breaks.$i.start") <p class="form-error">{{ $message }}</p> @enderror
                                @error("breaks.$i.end") <p class="form-error">{{ $message }}</p> @enderror
                            </div>
                        </td>
                        </tr>
                        @endfor

                        {{-- 備考 --}}
                        <tr>
                            <th class="label">備考</th>
                            <td class="value">
                                <textarea
                                    name="reason"
                                    class="reason-textarea"
                                    rows="2"
                                    {{ $isPending ? 'disabled' : '' }}>{{ old('reason', $displayData['reason']) }}</textarea>

                                <div class="error-area">
                                    @error('reason')
                                    <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </td>
                        </tr>

                </tbody>
            </table>
        </form>
    </div>

    {{-- アクション --}}
    <div class="attendance-action">
        @if ($isPending)
        <p class="text-danger attendance-message">
            *承認待ちのため修正はできません。
        </p>
        @else
        <button type="submit" form="attendance-correction-form" class="btn btn--black btn-fix">修正</button>
        @endif
    </div>

</div>
@endsection