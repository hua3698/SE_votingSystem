<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>軟體工程第XX組 - 投票管理系統</title>
        <link rel="icon" href="{{ asset('assets/ncu.avif') }}" type="image/x-icon">
        <link href="{{ asset('plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

        <script src="{{ asset('plugins/jquery/jquery-3.7.1.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap/bootstrap.bundle.js') }}"></script>
        {{-- <script src="{{ asset('plugins/bootstrap/popper.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('plugins/bootstrap/bootstrap.min.js') }}"></script> --}}

        {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> --}}
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <div class="header mb-5">
            <div class="header-bar shadow">
                <div class="container">
                    <div class="title">
                        <h2><a href="{{ url('outstand') }}">軟體工程第XX組 - 投票管理系統</a></h2>
                    </div>
                </div>
            </div>
            <div class="login px-5">
                @if(session('email'))
                    <div class="pe-3">{{ session('email') }}</div>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        登出
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endif
            </div>
        </div>
        @yield('body')

        @yield('script_js')

    </body>
</html>
