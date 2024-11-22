@extends('admin.common')

@section('body')
<style>
    body {
        background-color: #f2f3f5;
    }

    .login_container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 60vh;
    }

    .modal_box {
        width: 400px;
    }

    .modal-content {
        background-color: #fdfdfd;
    }

    .modal-header {
        justify-content: center;
    }
</style>
<body>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="login_container">
        <div class="modal_box">
            <div class="modal-dialog" role="document">
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header p-5 pb-4 border-bottom-0 mb-3">
                        <h1 class="fw-bold mb-0 fs-2">管理員登入</h1>
                    </div>
                    <div class="modal-body p-5 pt-0">
                        <form method="POST" action="{{ route('login.submit') }}">
                            @csrf
                            <div class="form-floating mb-4">
                                <input type="email" name="email" class="form-control rounded-3" id="floatingInput" placeholder="name@example.com">
                                <label for="floatingInput">Email</label>
                            </div>
                            <div class="form-floating mb-5">
                                <input type="password" name="password" class="form-control rounded-3" id="floatingPassword" placeholder="Password">
                                <label for="floatingPassword">Password</label>
                            </div>
                            <button class="w-100 mb-3 btn btn-lg rounded-3 btn-primary" type="submit">登入</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection