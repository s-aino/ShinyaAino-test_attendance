<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員登録</title>
</head>
<body>
<h1>会員登録</h1>

@if ($errors->any())
<ul style="color:red;">
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

<form method="POST" action="{{ route('register') }}"novalidate>
    @csrf
    <div>
        <label>名前</label>
        <input type="text" name="name" value="{{ old('name') }}">
    </div>
    <div>
        <label>メール</label>
        <input type="email" name="email" value="{{ old('email') }}">
    </div>
    <div>
        <label>パスワード</label>
        <input type="password" name="password">
    </div>
    <div>
        <label>パスワード確認</label>
        <input type="password" name="password_confirmation">
    </div>
    <button type="submit">登録</button>
</form>

<a href="{{ route('login') }}">ログインはこちら</a>
</body>
</html>
