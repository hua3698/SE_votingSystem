<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>軟體工程第十一組 - 投票管理系統</title>
    <link rel="icon" href="{{ asset('assets/NCU.Logo.png') }}" type="image/x-icon">

    <link href="{{ asset('plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/front.css') }}" rel="stylesheet">

    <script src="{{ asset('plugins/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/bootstrap.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h2 class="title">
        <img src="{{ asset('assets/NCU.Logo.png') }}" height="10%" width="10%">
        <span>軟體工程第十一組 - 投票管理系統</span>
    </h2>
    @yield('body')
</body>
</html>