<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>軟體工程第十一組 - 投票管理系統</title>
    <link rel="icon" href="{{ asset('assets/logo.png') }}" type="image/x-icon">

    <link href="{{ asset('plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('css/front_vote.css') }}" rel="stylesheet"> --}}

    <script src="{{ asset('plugins/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/bootstrap.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <header class="header">
        <div class="logo">
            <a href="{{ url('/index') }}">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo" /> 投票吧
            </a>
        </div>
        <div class="divider"></div>
        <nav class="nav-links">
            <ul>
                <li><a href="{{ url('/index') }}">探索</a></li>
                <li><a href="{{ url('/user') }}">會員中心</a></li>
            </ul>
        </nav>

        <div class="buttons">
            @if(session('frontuser'))
                <div class="pe-3">{{ session('frontuser') }}</div>
                <a href="{{ url('/user/logout') }}" class="button login">登出</a>
            @else
                <a href="{{ url('/user/login') }}" class="button login">登入</a>
                <a href="{{ url('/user/register') }}" class="button register">註冊</a>
            @endif
        </div>
    </header>
    @yield('body')
    <footer class="footer">
        <div class="footer-logo">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo" />
            投票吧
        </div>
        <div class="footer-text">
            中央大學資訊管理學系碩士班｜軟體工程｜期末作業用無盈利用途
        </div>
    </footer>
</body>
</html>