@extends('layouts.app')

@section('title', '申請一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance-common.css') }}">
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endpush

@section('content')
<div class="page-container">
    <div class="page-title-wrap">
        <h2 class="page-title">申請一覧</h2>
    </div>

    <div class="request-tabs-wrap">
        <div class="request-tabs">
            <a href="{{ route('correction_requests.index', ['status' => 'pending']) }}"
                class="{{ $status === 'pending' ? 'active' : '' }}">
                承認待ち
            </a>

            <a href="{{ route('correction_requests.index', ['status' => 'approved']) }}"
                class="{{ $status === 'approved' ? 'active' : '' }}">
                承認済み
            </a>
        </div>
    </div>


    <table class="request-table list-table">
        <thead>
            <tr>
                <th class="status-cell">状態</th>
                <th class="name-cell">名前</th>
                <th class="date-cell">対象日時</th>
                <th class="reason-cell">申請理由</th>
                <th class="created-cell">申請日時</th>
                <th class="detail-cell">詳細</th>
            </tr>
        </thead>

        <tbody>
            @foreach($correctionRequests as $request)
            <tr>
                <td class="status-cell">
                    {{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}
                </td>

                <td class="name-cell list-truncate">
                    <span class="truncate">
                        {{ $request->attendance?->user?->name }}
                    </span>
                </td>
                <td class="date-cell">
                    {{ $request->attendance ? $request->attendance->date->format('Y/m/d') : '-' }}
                </td>

                <td class="reason-cell list-truncate">
                    <span class="truncate">
                        {{ $request->requested_data['reason'] ?? '' }}
                    </span>
                </td>
                <td class="created-cell">
                    {{ $request->created_at->format('Y/m/d') }}
                </td>

                <td class="detail-cell">
                    @if(auth()->user()->role === 'admin')
                    <a href="{{ route('stamp_correction_request.approve', $request->id) }}">
                        詳細
                    </a>
                    @else
                    <a href="{{ route('attendance.show', $request->attendance_id) }}">
                        詳細
                    </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection