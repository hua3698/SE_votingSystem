@extends('admin.common')

@section('body')
    <div class="container vote_detail">
        <h2 class="text-center fw-bold mb-5">
            {{ $vote_event->event_name }}
            @if ($vote_event->manual_control === 1 && $vote_event->vote_is_ongoing === 0 && $vote_event->status !== 2)
                <button id="btnActivateVote" type="button" class="btn btn-outline-primary">開放投票</button>
            @elseif ($vote_event->manual_control === 1 && $vote_event->vote_is_ongoing === 1)
                <button id="btnDectivateVote" type="button" class="btn btn-outline-danger">結束投票</button>
            @endif
        </h2>
        <div class="shadow block mb-5">
            <div class="d-flex justify-content-between mb-5">
                <div>
                    <p>
                        @if ($vote_event->manual_control === 1)
                            @if ($vote_event->vote_is_ongoing === 0 && $vote_event->status !== 2)
                                <span class="badge text-bg-secondary">尚未開始</span>
                            @elseif ($vote_event->vote_is_ongoing === 1 && $vote_event->status !== 2)
                                <span class="badge text-bg-success">投票進行中</span>
                                @else 
                                <span class="badge text-bg-danger">已結束</span>
                            @endif
                        @else
                            @if ($vote_event->status === 0)
                                <span class="badge text-bg-secondary">尚未開始</span>
                            @elseif ($vote_event->status === 1)
                                <span class="badge text-bg-success">投票進行中</span>
                            @else 
                                <span class="badge text-bg-danger">已結束</span>
                            @endif
                        @endif
                    </p>
                    <p class="card-text">
                        投票時間：
                        {{ $vote_event->start_time }} ~ {{ $vote_event->end_time }}
                        
                    </p>
                    <p>手動開啟： {{ $vote_event->manual_control ? '是' : '否' }}</p>
                </div>
                <div>
                    <div class="btn-group">
                        <div class="dropdown">
                            <ul class="list-group">
                                <a href="" class="list-group-item list-group-item-action">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                    </svg>
                                    編輯
                                </a>
                                <a href="" class="list-group-item list-group-item-action">查看投票狀況</a>
                            </ul>
                        </div>
                        <div class="threedot">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                            </svg>
                        </div class="threedot">
                    </div>
                </div>
            </div> 
            <div class="col-12 mb-5">
                {{-- <div class="block row">
                    <div class="item col">
                        <div>每人最多可以投幾票</div>
                        <div>{{ $vote_event->max_vote_count }}</div>
                    </div>
                    <div class="item col">
                        <div>最多選出幾名winner</div>
                        <div>{{ $vote_event->number_of_winners }}</div>
                    </div>
                    <div class="item col">
                        <div>設定發放的Qrcode數</div>
                        <div>{{ $vote_event->number_of_qrcodes }}</div>
                    </div>
                </div> --}}
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">每人最多可以投幾票</th>
                            <th scope="col">最多選出幾名winner</th>
                            <th scope="col">設定發放的Qrcode數</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="col">{{ $vote_event->max_vote_count }}</th>
                            <th scope="col">{{ $vote_event->number_of_winners }}</th>
                            <th scope="col">{{ $vote_event->number_of_qrcodes }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p>候選人：</p>
            <div class="col-12 mb-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">候選人名稱</th>
                            <th scope="col">候選人學校</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $key => $cand)
                        <tr>
                            <th scope="row">{{ ($key + 1) }}</th>
                            <td>{{ $cand['name'] }}</td>
                            <td>{{ $cand['school'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="shadow block">
            <div class="qrcode_header my-3">
                <h4 class="me-3">QR code</h4>
                <div>
                    <form id="downloadPdfForm" method="POST" action="{{ route('admin.vote.pdf', ['event_id' => $vote_event->event_id]) }}" target="_blank">
                        @csrf
                        <!-- 其他隱藏輸入欄位可以放這裡 -->
                        <button id="btnQrcodePDF" type="submit" class="btn btn-outline-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-circle" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z"/>
                            </svg>
                            下載PDF
                        </button>
                    </form>
                </div>
            </div>
            <div class="row show_qrcode">
                @foreach ($qrcodes as $key => $qrcode)
                    <div class="col-12 col-md-6 col-lg-3 text-center" style="margin-bottom:3rem;">
                        <p><strong>{{ ($key + 1) }}</strong></p>
                        <img src="{{ $qrcode->qrcode_url }}" alt="QR Code">
                        <p>{{ $qrcode->qrcode_string }}</p>
                    </div>
                @endforeach
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

        $('#btnActivateVote').on('click', function() {
            let activate = confirm('確定開放投票嗎?')

            if (activate) {
                let post_data = {}
                post_data._token = "{{ csrf_token() }}"
                post_data.event_id = {{ $vote_event->event_id }}
                post_data.activate = 1

                $.ajax({
                    type: 'PUT',
                    url: "{{ route('activate.vote') }}",
                    contentType: 'application/json',
                    data: JSON.stringify(post_data),
                }).done(function(re) {
                    console.log(re)
                    alert('設定成功')
                    location.reload()
                }).fail(function(re) {
                    alert('發生錯誤：' + re.responseText);
                });
            }
        })

        $('#btnDectivateVote').on('click', function() {
            let activate = confirm('確定關閉投票嗎?')

            if (activate) {
                let post_data = {}
                post_data._token = "{{ csrf_token() }}"
                post_data.event_id = {{ $vote_event->event_id }}
                post_data.activate = 1

                $.ajax({
                    type: 'PUT',
                    url: "{{ route('deactivate.vote') }}",
                    contentType: 'application/json',
                    data: JSON.stringify(post_data),
                }).done(function(re) {
                    console.log(re)
                    alert('設定成功')
                    location.reload()
                }).fail(function(re) {
                    alert('發生錯誤：' + re.responseText);
                });
            }
        })
    })
</script>
@endsection
