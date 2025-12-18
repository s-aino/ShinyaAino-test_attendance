<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
</head>
<body>
<h1>ログイン</h1>

@if ($errors->any())
<ul style="color:red;">
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div>
        <label>メール</label>
        <input type="email" name="email" value="{{ old('email') }}">
    </div>
    <div>
        <label>パスワード</label>
        <input type="password" name="password">
    </div>
    <button type="submit">ログイン</button>
</form>

<a href="{{ route('register') }}">会員登録はこちら</a>
</body>
</html>
