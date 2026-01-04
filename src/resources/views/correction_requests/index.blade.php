@extends('layouts.app')

@section('title', '申請一覧')

@push('styles')
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


    <table class="request-table">
        <thead>
            <tr>
                <th class="status-cell">状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
        </thead>

        <tbody>
            @forelse($correctionRequests as $request)
            <tr>
                <td class="status-cell">
                    {{ $request->status === 'pending' ? '承認待ち' : '承認済み' }}
                </td>

                <td class="truncate name-cell">{{ $request->attendance->user->name }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($request->attendance->date)->format('Y/m/d') }}
                </td>

                <td class="truncate reason-cell">
                    {{ $request->requested_data['reason'] ?? '' }}
                </td>
                <td>
                    {{ $request->created_at->format('Y/m/d') }}
                </td>

                <td>
                    <a href="{{ route('attendance.show', $request->attendance_id) }}">
                        詳細
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <!-- <td colspan="6">承認済みはありません。</td> -->
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection