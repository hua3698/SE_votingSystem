<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>桃園區網線上投票平台 - 管理端</title>
        <link rel="icon" href="{{ asset('assets/header.jpg') }}" type="image/x-icon">
        <link href="{{ asset('plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <script src="{{ asset('plugins/jquery/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap/bootstrap.bundle.js') }}"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/main.js') }}"></script>
    </head>
    <body>
        <div class="header-bar">
            <div class="container">
                <h2><a href="{{ url('outstand') }}">桃園區網線上投票平台 - 管理端</a></h2>
            </div>
        </div>
        <div class="second_bar shadow-sm">
            <div class="container">
                @if(session('email'))
                    <div class="category_block">
                        <a href="{{ url('outstand') }}" class="me-3">
                            <span>管理投票活動</span>
                        </a>
                        <a href="{{ url('outstand/users') }}" class="me-3">
                            <span>管理使用者</span>
                        </a>
                    </div>
                    <div class="login">
                        <span class="pe-3">{{ session('email') }}</span>
                        <a href="{{ route('logout') }}" class="btn btn-outline-secondary" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            登出
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                @endif
            </div>
        </div>
        @yield('body')

        @yield('script_js')

    </body>
</html>
