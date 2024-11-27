@extends('front.common')

@section('body')
{{-- <link href="{{ asset('css/login.css') }}" rel="stylesheet"> --}}
<link rel="stylesheet" href="{{ asset('css/register.css') }}" />
<div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @elseif (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}，請前往登入頁</li>
            </ul>
        </div>
    @endif
    <div class="register-container">
        <div class="register-box">
            <h1 class="fw-bold">會員註冊</h1>
            <form method="POST" action="{{ route('user.register') }}">
                @csrf
                <div class="input-group">
                    <label for="nickname">暱稱 </label>
                    <input
                        type="text"
                        id="nickname"
                        name="name"
                        placeholder="請輸入暱稱(最多10個字)"
                        required
                    />
                </div>
                <div class="input-group">
                    <label for="email">電子郵件</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="請輸入電子郵件"
                        required
                    />
                </div>
                <div class="input-group">
                    <label for="password">密碼</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
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
