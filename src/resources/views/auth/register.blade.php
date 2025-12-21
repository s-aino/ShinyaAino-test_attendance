@extends('layouts.app')

@section('title', '会員登録')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
    <h1 class="auth-title">会員登録</h1>

    <!-- {{-- エラーメッセージ --}}
    @if ($errors->any())
    <ul class="auth-errors">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
    @endif -->

    <form method="POST" action="{{ route('register') }}" novalidate class="auth-form">
        @csrf

        <div class="auth-field">
            <label for="name">名前</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name') }}">

            @error('name')
            <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="auth-field">
            <label for="email">メールアドレス</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}">

            @error('email')
            <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">パスワード</label>
            <input
                id="password"
                type="password"
                name="password">

            @error('password')
            <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password_confirmation">パスワード確認</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation">
        </div>

        <button type="submit" class="auth-button">
            登録する
        </button>
    </form>

    <div class="auth-link">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </div>
</div>
@endsection