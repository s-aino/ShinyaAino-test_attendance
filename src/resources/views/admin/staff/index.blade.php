@extends('layouts.app')

@section('title', 'スタッフ一覧')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/attendance-common.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<div class="page-container">

    {{-- ページタイトル --}}
    <div class="page-title-wrap">
        <h2 class="page-title">
            <span class="page-title-bar"></span>
            スタッフ一覧
        </h2>
    </div>

    {{-- 一覧テーブル --}}
    <div class="list-table-wrap">
        <table class="list-table">
            <thead>
                <tr>
                    <th class="list-th">名前</th>
                    <th class="list-th">メールアドレス</th>
                    <th class="list-th">月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staffs as $staff)
                    <tr class="list-tr">
                       <td class="list-td list-truncate">
                            {{ $staff->name }}
                        </td>
                        <td class="list-td list-truncate">
                            {{ $staff->email }}
                        </td>
                        <td class="list-td">
                            <a
                                href="{{ route('admin.attendance.staff.show', $staff->id) }}"
                                class="list-detail-link"
                            >
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
