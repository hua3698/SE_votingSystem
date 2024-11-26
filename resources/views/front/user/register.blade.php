@extends('front.common')

@section('body')
{{-- <link href="{{ asset('css/login.css') }}" rel="stylesheet"> --}}
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />

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
    <div class="register-container">
        <div class="register-box">
            <h1>會員註冊</h1>
            <form method="POST" action="{{ route('user.register') }}">
                @csrf
                <div class="input-group">
                    <label for="nickname">暱稱 </label>
                    <input
                        type="text"
                        id="nickname"
                        placeholder="請輸入暱稱(最多10個字)"
                        required
                    />
                </div>
                <div class="input-group">
                    <label for="email">電子郵件</label>
                    <input
                        type="email"
                        id="email"
                        placeholder="請輸入電子郵件"
                        required
                    />
                </div>
                <div class="input-group">
                    <label for="password">密碼</label>
                    <input
                        type="password"
                        id="password"
                        placeholder="請輸入密碼"
                        required
                    />
                </div>
                <button type="submit" class="register-button">註冊</button>
            </form>
        </div>
    </div>
</div>
@endsection
