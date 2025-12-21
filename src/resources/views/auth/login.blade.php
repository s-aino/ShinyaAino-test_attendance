@extends('layouts.app')

@section('title', 'ログイン')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-container">
    <h1 class="auth-title">ログイン</h1>

    <form method="POST" action="{{ route('login') }}" novalidate class="auth-form">
        @csrf

        <div class="auth-field">
            <label for="email">メールアドレス</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                autocomplete="email">

            @error('email')
            <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="auth-field">
            <label for="password">パスワード</label>
            <input
                id="password"
                type="password"
                name="password"
                autocomplete="current-password">

            @error('password')
            <p class="auth-error">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="auth-button">
            ログインする
        </button>

        <div class="auth-link">
            <a href="{{ route('register') }}">会員登録はこちら</a>
        </div>
    </form>
</div>
@endsection