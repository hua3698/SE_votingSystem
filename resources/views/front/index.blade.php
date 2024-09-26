<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>桃園區網線上投票平台</title>
    <link href="{{ asset('plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/front.css') }}" rel="stylesheet">

    <script src="{{ asset('plugins/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/bootstrap.bundle.js') }}"></script>
</head>
<body>
    @yield('body')
</body>
</html>