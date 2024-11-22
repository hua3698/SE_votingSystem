@extends('admin.common')

@section('body')
    <div class="container">
        <div class="title d-flex justify-content-between mb-3">
            <h4 class="fw-bold">後台管理員</h4>
            <button id="add_new_user" class="btn-success">建立新的管理員</button>
        </div>
        <table class="table table-bordered user_table">
            <thead>
                <tr>
                    <th></th>
                    <th>姓名</th>
                    <th>登入信箱</th>
                    <th>更新</th>
                    <th>帳號更新時間</th>
                    <th>刪除</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $key => $user)
                    <tr data-id="{{ $user->user_id }}">
                        <td>{{ $key + 1 }}</td>
                        <td class="name">{{ $user->name }}</td>
                        <td class="email">{{ $user->email }}</td>
                        <td class="btnUpdate icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                            </svg>
                        </td>
                        <td>{{ $user->updated_at }}</td>
                        <td class="btnDelete icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                            </svg>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="updateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">更新使用者資料</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul data-id="">
                        <li class="mb-2">姓名：<input class="modalInput" id="updateName"></li>
                        <li class="mb-2">信箱：<input class="modalInput" id="updateEmail" type="email"></li>
                        {{-- <li class="mb-2">
                            重新設定密碼：
                            <input class="modalInput" id="updateEmail" value="********" type="email">
                            <small class="text-danger d-none" id="passwordError">密碼至少為8個字元</small>
                        </li> --}}
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btnConfirm">更新</button>
                    <button type="button" class="btn btn-secondary" id="btnClose">關閉</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script_js')
<script>
    $(function() {
        $('#add_new_user').on('click', function() {
            window.location.href="{{ url('/outstand/admin/createadmin') }}"
        })

        $('.btnUpdate').on('click', function() {
            const user_id = $(this).parents('tr').data('id')
            const name = $(this).parents('tr').find('.name').html()
            const email = $(this).parents('tr').find('.email').html()

            console.log(user_id)

            $('#updateName').val(name)
            $('#updateEmail').val(email)
            $('#updateModal ul').data('id', user_id)
            
            $('#updateModal').modal('show')
        })

        $('.btnDelete').on('click', function() {
            const email = $(this).parents('tr').find('.email').html()
            const del = confirm(`確定要刪除 ${email} 嗎?`)
            if(del) {
                $.ajax({
                    type: 'DELETE',
                    url: "{{ route('delete.admin') }}",
                    contentType: 'application/json',
                    data: JSON.stringify({ email: email }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                }).done(function(re) {
                    alert(re.message);
                    location.reload();
                }).fail(function(re) {
                    console.log(re);
                    alert('新增失敗，請重試');
                });
            }
        })

        $('#btnConfirm').on('click', function() {
            const name = $('#updateName').val();
            const email = $('#updateEmail').val();
            const user_id = $('#updateModal ul').data('id')

            // if (password.length < 8) {
            //     $('#passwordError').removeClass('d-none');
            //     return;
            // } else {
            //     $('#passwordError').addClass('d-none');
            // }

            $.ajax({
                type: 'PUT',
                url: "{{ route('update.admin') }}",
                contentType: 'application/json',
                data: JSON.stringify({
                    name: name,
                    email: email,
                    user_id: user_id
                }),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            }).done(function(response) {
                console.log(response);
                alert('更新成功');
                $('#btnClose').click();
                location.reload();
            }).fail(function(error) {
                console.log(error);
                alert('更新失敗');
            });
        })

        $('#btnClose').on('click', function() {
            $('#updateModal').modal('hide')
        })
    })
</script>
@endsection