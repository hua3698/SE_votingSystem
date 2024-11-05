@extends('admin.common')

@section('body')
    <div class="container">
        <div class="text-center my-5">
            <h2 class="">新增後台管理員</h2>
        </div>
        <div class="vote_form_div">
            <div class="mb-3 row">
                <label for="name" class="col-sm-2 col-form-label">姓名</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name">
                    <small class="text-danger d-none" id="nameError">請輸入使用者名稱</small>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="email" class="col-sm-2 col-form-label">登入信箱</label>
                <div class="col-sm-10">
                    <input type="email" class="form-control" id="email">
                    <small class="text-danger d-none" id="emailError">請輸入有效的電子郵件</small>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="password" class="col-sm-2 col-form-label">密碼</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="password">
                    <small class="text-danger d-none" id="passwordError">密碼至少為8個字元</small>
                </div>
            </div>
            <div class="mb-3 row">
                <label for="password2" class="col-sm-2 col-form-label">確認密碼</label>
                <div class="col-sm-10">
                    <input type="password" class="form-control" id="password2">
                    <small class="text-danger d-none" id="password2Error">密碼不相符</small>
                </div>
            </div>
            <div class="text-center mb-4">
                <button id="btnSubmit" class="btn btn-primary">確認送出</button>
                <button id="btnCancel" class="btn btn-secondary">取消</button>
            </div>
        </div>
    </div>
@endsection

@section('script_js')
<script>
    $(function() {
        $('#btnSubmit').on('click', function() {
            let isValid = true;

            const name = $('#name').val().trim();
            const email = $('#email').val().trim();
            const password = $('#password').val();
            const password2 = $('#password2').val();

            $('.text-danger').addClass('d-none');

            if (name === '') {
                $('#nameError').removeClass('d-none');
                isValid = false;
            }

            if (email === '' || !/^[\w-]+(\.[\w-]+)*@([\w-]+\.)+[a-zA-Z]{2,7}$/.test(email)) {
                $('#emailError').removeClass('d-none');
                isValid = false;
            }

            if (password.length < 8) {
                $('#passwordError').removeClass('d-none');
                isValid = false;
            }

            if (password !== password2) {
                $('#password2Error').removeClass('d-none');
                isValid = false;
            }

            if (isValid) {
                const post_data = {
                    _token : "{{ csrf_token() }}",
                    name: name,
                    email: email,
                    password: password
                };

                $.ajax({
                    type: 'POST',
                    url: "{{ route('create.user') }}",
                    contentType: 'application/json',
                    data: JSON.stringify(post_data),
                }).done(function(re) {
                    console.log(re);
                    alert('新增成功');
                    location.href = "{{ url('outstand/users') }}";
                }).fail(function(re) {
                    console.log(re);
                    alert('新增失敗，請重試');
                });
            }
        });

        $('#btnCancel').on('click', function() {
            location.href = "{{ url('outstand/user') }}"
        })

    })
</script>
@endsection