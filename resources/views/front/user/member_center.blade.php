@extends('front.common')

@section('body')
<link href="{{ asset('css/member_center.css') }}" rel="stylesheet">
<style>
    .center-container {
        min-height: calc(100vh - 150px);
    }
</style>

<div class="center-container">
    <!-- 左側選單區 -->
    <div class="sidebar">
        <a href="{{ url('/user') }}" class="sidebar-button"
            >⮑ 修改會員資料</a
        >
        <a href="{{ url('/index') }}" class="sidebar-button">⮑ 投票紀錄</a>
    </div>

    <!-- 右側內容區 -->
    <div class="content">
        <div class="modify-box">
            <h1>修改會員資料</h1>
            <form>
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
                <button type="submit" class="modify-button">
                    儲存
                </button>
            </form>
        </div>
    </div>
</div>


@endsection