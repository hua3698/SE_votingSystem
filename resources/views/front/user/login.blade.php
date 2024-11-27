@extends('front.common')

@section('body')
{{-- <link href="{{ asset('css/login.css') }}" rel="stylesheet"> --}}
<style>
    /* 登入頁樣式 */
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background: linear-gradient(to right, #c1dfc4, #eff8ee);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        box-sizing: border-box;
    }
</style>
<div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="login-container">
        <div class="login-box">
            <h1 class="fw-bold">會員登入</h1>
            <form method="POST" action="{{ route('user.login') }}">
                @csrf
                <div class="input-group">
                    <label for="email">電子郵件</label>
                    <input type="email" name="email" id="email" placeholder="請輸入電子郵件">
                </div>
                <div class="input-group">
                    <label for="password">密碼</label>
                    <input type="password" name="password" id="password" placeholder="請輸入密碼">
                </div>
                <button type="submit" class="login-button">登入</button>
            </form>
        </div>
    </div>
</div>
@endsection
