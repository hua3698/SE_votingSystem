<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>軟體工程第十一組 - 投票管理系統</title>
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
        <script src="{{ asset('js/main.js') }}"></script>
    
    </head>
    <body>
        <div class="header-bar">
            <div class="container">
                <h2><a href="{{ url('outstand') }}">投票管理系統 - 管理後台</a></h2>
            </div>
        </div>
        <div class="second_bar shadow-sm">
            <div class="container">
                @if(session('email'))
                    <div class="category_block">
                        <a href="{{ url('outstand') }}" class="me-3">
                            <span>管理投票活動</span>
                        </a>
                        <a href="{{ url('outstand/admin/list') }}" class="me-3">
                            <span>後台管理員</span>
                        </a>
                        <a href="{{ url('outstand/user/list') }}" class="me-3">
                            <span>使用者列表</span>
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
