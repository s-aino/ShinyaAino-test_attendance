@extends('layouts.app')

@section('title', '修正申請詳細')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance-common.css') }}">
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<div class="page-container">

    <div class="page-title-wrap">
        <h2 class="page-title">勤怠詳細</h2>
    </div>

    <div class="attendance-card">

        <form
            id="attendance-admin-update-form"
            method="POST"
            action="{{ route('stamp_correction_request.approve.update', $correctionRequest->id) }}">
            @csrf
            @method('PUT')
            <table class="attendance-detail-table">
                <tbody>

                    {{-- 名前 --}}
                    <tr>
                        <th class="label">名前</th>
                        <td class="value">
                            <div class="value-text">
                                {{ $correctionRequest->attendance->user->name }}
                            </div>
                        </td>
                    </tr>

                    {{-- 日付 --}}
                    <tr>
                        <th class="label">日付</th>
                        <td class="value">
                            <div class="date-block">
                                <span class="date-year">{{ optional($correctionRequest->attendance->date)->format('Y年') }}</span>
                                <span class="date-md">{{ optional($correctionRequest->attendance->date)->format('n月j日') }}</span>
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
                                    class="time-input is-readonly"
                                    value="{{ $requested['clock_in'] ?? '' }}"
                                    readonly
                                    disabled>

                                <span class="time-sep">〜</span>

                                <input
                                    type="text"
                                    name="clock_out"
                                    class="time-input is-readonly"
                                    value="{{ $requested['clock_out'] ?? '' }}"
                                    readonly
                                    disabled>
                            </div>
                        </td>
                    </tr>

                    {{-- 休憩（既存 + 1） --}}
                    @foreach ($breakRows as $i => $break)
                    <tr>
                        <th class="label">
                            {{ $i === 0 ? '休憩' : '休憩' . ($i + 1) }}
                        </th>

                        <td class="value">
                            <div class="time-row">
                                <input
                                    type="text"
                                    name="breaks[{{ $i }}][start]"
                                    class="time-input is-readonly"
                                    value=" {{ $break['start'] ?? '' }}"
                                    readonly
                                    disabled>

                                <span class="time-sep">〜</span>

                                <input
                                    type="text"
                                    name="breaks[{{ $i }}][end]"
                                    class="time-input is-readonly"
                                    value="{{ $break['end'] ?? '' }}"
                                    readonly
                                    disabled>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    {{-- 備考 --}}
                    <tr>
                        <th class="label">備考</th>
                        <td class="value">
                            <textarea
                                name="reason"
                                class="reason-textarea is-readonly"
                                readonly
                                disabled
                                rows="2">{{ $requested['reason'] ?? '' }}</textarea>
                        </td>
                    </tr>

                </tbody>
            </table>
        </form>
    </div>

    {{-- アクション --}}
    <div class="attendance-action">
        @if ($correctionRequest->status === 'approved')
        <button class="btn btn--gray btn-fix" disabled>
            承認済み
        </button>
        @else
        <button type="submit" form="attendance-admin-update-form" class="btn btn--black btn-fix">承認</button>
        @endif
    </div>
</div>
@endsection