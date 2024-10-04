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
        {{-- <script src="{{ asset('plugins/bootstrap/popper.min.js') }}"></script> --}}
        {{-- <script src="{{ asset('plugins/bootstrap/bootstrap.min.js') }}"></script> --}}

        {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script> --}}
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="{{ asset('js/main.js') }}"></script>
    </head>
    <body>
        <div class="header shadow">
            <div class="container">
                <div class="title">
                    <h2><a href="{{ url('outstand') }}">桃園區網線上投票平台 - 管理端</a></h2>
                </div>
            </div>
        </div>
        @yield('body')

        @yield('script_js')

    </body>
</html>
