<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Attendance')</title>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>

<body>
    @include('components.header')

    <main class="main">
        @yield('content')
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const flash = document.querySelector('.flash-message');
            if (!flash) return;

            setTimeout(() => {
                flash.classList.add('is-hidden');
            }, 5000);

            setTimeout(() => {
                flash.remove();
            }, 5800);
        });
    </script>

</body>

</html>