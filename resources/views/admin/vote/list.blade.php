@extends('admin.common')

@section('body')
    <div class="container admin_container">
        <h2 class="text-center fw-bold mb-5">
            {{ $vote_event->event_name }} 
        </h2>
        <div class="shadow block mb-5">
            <div class="d-flex justify-content-between mb-5">
                <div>
                    <p>
                        @if ($vote_event->status == 0)
                            <span class="fs-6 badge text-bg-secondary">尚未開始</span>
                        @elseif ($vote_event->status == 1)
                            <span class="fs-6 badge text-bg-success">投票進行中</span>
                        @else 
                            <span class="fs-6 badge text-bg-danger">已結束</span>
                        @endif
                    </p>
                    <p class="card-text fs-5">
                        <span class="bar">
                            <strong>投票開始：</strong> {{ $vote_event->start_time }}
                        </span>
                    </p>
                    <p class="card-text fs-5">
                        <span class="bar">
                            <strong>投票結束：</strong> {{ $vote_event->end_time }}
                        </span>
                    </p>
                </div>
                <div class="btn-group">
                    <div class="dropdown">
                        <ul class="list-group">
                            @if ($vote_event->status == 0)
                                <a href="{{ route('vote.edit.page', ['event_id' => $vote_event->event_id]) }}" class="list-group-item list-group-item-action">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                    </svg>
                                    編輯
                                </a>
                            @endif
                            <a href="{{ route('vote.check', ['event_id' => $vote_event->event_id]) }}" class="list-group-item list-group-item-action">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16">
                                    <path d="M4 11H2v3h2zm5-4H7v7h2zm5-5v12h-2V2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z"/>
                                </svg>
                                查看投票狀況
                            </a>
                            @if ($vote_event->status == 2)
                                <a href="{{ route('admin.vote.result', ['event_id' => $vote_event->event_id]) }}" class="list-group-item list-group-item-action">
                                    <svg width="16" height="16" fill="currentColor" class="bi bi-trophy" viewBox="0 0 16 16">
                                        <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z"/>
                                    </svg>
                                    查看開票結果
                                </a>
                            @endif
                            @if ($vote_event->status != 1)
                                <a id="btnDelete" href="#" class="list-group-item list-group-item-action">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                    </svg>
                                    刪除投票活動
                                </a>
                            @endif
                        </ul>
                    </div>
                    <div class="threedot">
                        <svg width="25" height="25" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                            <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                        </svg>
                    </div class="threedot">
                </div>
            </div> 
            <div class="col-12 mb-5 px-3">
                <table class="table table-bordered detail_table">
                    <thead>
                        <tr>
                            <th scope="col">每人最多可以投幾票</th>
                            <th scope="col">最多選出幾名winner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="col">{{ $vote_event->max_vote_count }}</th>
                            <th scope="col">{{ $vote_event->number_of_winners }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="card-text fs-5">
                <span class="bar">
                    <strong>候選人</strong>
                </span>
            </p>
            <div class="col-12 mb-3 px-3">
                <table class="table table-bordered table-hover detail_table">
                    <thead>
                        <tr>
                            <th scope="col">序號</th>
                            <th scope="col">候選人名稱</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $cand)
                        <tr>
                            <th scope="row">{{ $cand['number'] }}</th>
                            <td>{{ $cand['name'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script_js')
<script>
    $(function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        $('.dropdown').hide()
        $('.threedot').on('click', function() {
            $('.dropdown').toggle()
        })

        $('#btnDelete').on('click', function(e) {
            e.preventDefault()

            let del = confirm('確定要刪除投票嗎?')

            if (del) {
                let post_data = {}
                post_data._token = "{{ csrf_token() }}"
                post_data.event_id = {{ $vote_event->event_id }}
                post_data.del = 1

                $.ajax({
                    type: 'PUT',
                    url: "{{ route('del.vote') }}",
                    contentType: 'application/json',
                    data: JSON.stringify(post_data),
                }).done(function(re) {
                    console.log(re)
                    alert('設定成功')
                    location.href = "{{ url('outstand') }}"
                }).fail(function(re) {
                    alert('發生錯誤：' + re.responseText);
                });
            }
        })
    })
</script>
@endsection
