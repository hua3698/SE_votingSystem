<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入</title>
    <link href="{{ asset('plugins/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
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
    <div class="container">
        <div class="modal_box">
            <div class="modal-dialog" role="document">
                <div class="modal-content rounded-4 shadow">
                    <div class="modal-header p-5 pb-4 border-bottom-0 mb-3">
                        <h1 class="fw-bold mb-0 fs-2">Vote</h1>
                    </div>
                    <div class="modal-body p-5 pt-0">
                        <form  method="POST" action="{{ route('login.submit') }}">
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
</body>
</html>
