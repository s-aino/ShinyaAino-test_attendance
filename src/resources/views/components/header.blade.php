<header class="header">
    <div class="header__inner">
        <div class="header__logo">
            <img src="{{ asset('images/logo.png') }}" alt="COACHTECH">
        </div>

        {{-- ログイン画面ではナビ非表示 --}}
        @if (
        auth()->check() &&
        !request()->is('login') &&
        !request()->is('admin/login')
        )

        <nav class="header__nav">
            @if(auth()->user()->role === 'staff')
            <a href="/attendance">勤怠</a>
            <a href="/attendance/list">勤怠一覧</a>
            <a href="/stamp_correction_request/list">申請</a>
            @elseif(auth()->user()->role === 'admin')
            <a href="/admin/attendance/list">勤怠一覧</a>
            <a href="/admin/staff/list">スタッフ一覧</a>
            <a href="/stamp_correction_request/list">申請一覧</a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="header__logout">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
        </nav>
        @endif
    </div>
</header>