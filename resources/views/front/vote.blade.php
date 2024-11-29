@extends('front.common')

@section('body')
    @if ($status === 'error')
        <div class="container">
            <div class="not_available">
                {{ $error_msg }}
            </div>
        </div>
    @else
        <link href="{{ asset('css/front.css') }}" rel="stylesheet">
        <div class="activity-container">
            <div class="content-wrapper">
                <div class="title_background " >
                    <div class="deadline">截止日期：{{ $vote_event->end_time }}</div>
                    <div class="title_content">
                        <img src="{{ asset('assets/a' . $vote_event->event_id . '.png') }}" alt="" class="activity-image">
                    </div>
                    <div class="vote-title-container">
                        <img src="{{ asset('assets/vote.icon.png') }}" alt="投票圖示">
                        <h1 class="vote_name fw-bold">{{ $vote_event->event_name }}</h1>
                    </div>
                    <div class="modal-body">
                        <div class="notice">
                            <p class="fw-bold">◆ 說明</p>
                            <p>1. 每位投票者僅能進行一次投票，請謹慎選擇。</p>
                            <p>2. 投票資料將全程保密，僅用於計票分析。</p>
                            <p>3. 投票截止時間為 {{ $vote_event->end_time }}。</p>
                            <p>4. 點擊「送出投票」按鈕後，將無法進行修改。</p>
                        </div>
                        <div class="notice">
                            <p class="fw-bold">◆ 注意事項</p>
                            <p>1. 每人僅限投票一次，重複投票無效。</p>
                            <p>2. 投票結果將於 {{ $vote_event->end_time }} 公佈。</p>
                            <p>3. 如果遇到技術問題，請聯繫負責人（聯繫方式：example@domain.com）。</p>
                        </div>
                    </div>
                </div>
                <div class="vote_container">
                    <div class="can_vote">
                        <div class="vote_header">
                            <h4 class="fw-bold">請勾選想要投票的候選人</h4>
                            <div class="notice">
                                <p class="fw-bold"><a href="{{ route('vote.candidate', ['id' => $vote_event->event_id]) }}">點擊前往查看候選人介紹</a></p>
                            </div>
                        </div>
                        <div class="candidates">
                            @foreach ($candidates as $key => $cand)
                                <div class="cand shadow-sm">
                                    <img class="jiikawa" src="{{ asset('assets/' . $key . '.jpg') }}" alt="">
                                    <div class="no">{{ $cand['number'] }}號</div>
                                    <div class="intro"><strong>{{ $cand['name'] }}</strong></div>
                                    <div class="circle_div col-3">
                                        <div class="form-check vote_check">
                                            <input name="cand[]" class="form-check-input" type="checkbox" value="{{ $cand->cand_id }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="end">
                            <button id="btnVote">送出投票</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="checkModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>送出後無法重新投票，請確認以下您所選名單再送出。最多可圈選{{ $vote_event->max_vote_count }}位</p>
                            <div class="check_cand my-3 px-2"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                            <button id="btnConfirmToVote" type="button" class="btn btn-success">確定送出</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(function() {
                $('.cand').on('click', function(e) {
                    if(!$(e.target).is('input[type=checkbox]')) {
                        $(this).find('input[type=checkbox]').prop('checked', function(i, val) {
                            return !val;
                        });
                    }
                })

                $('#btnVote').on('click', function() {
                    let checkedCount = 0
                    let cand_info = ''
                    let check_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">' +
                                        '<path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>' +
                                        '<path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>' +
                                    '</svg>';

                    let candidates_id = []
                    $('.cand input[type=checkbox]').each(function() {
                        if($(this).prop('checked') === true) {
                            checkedCount++
                            candidates_id.push($(this).val())
                            let candidateNum = $(this).closest('.cand').find('.no').text();
                            let candidateName = $(this).closest('.cand').find('.intro strong').text();
                            cand_info += '<p class="fs-5 mb-2">' + check_icon +
                                '<strong class="ms-2">' + candidateNum + ' ' + candidateName + '</strong></p>';
                        }
                    })

                    const noticeAlert = Swal.mixin({
                        icon: 'warning',
                    });

                    if(checkedCount > {{ $vote_event->max_vote_count ?? 0 }}) {
                        noticeAlert.fire({
                            title: "最多勾選 {{ $vote_event->max_vote_count }} 位候選人!",
                        });
                    } else if (checkedCount < 1) {
                        noticeAlert.fire({
                            title: "請至少勾選1位，至多 {{ $vote_event->max_vote_count }} 位候選人",
                        });
                    } else {
                        $('.check_cand').html(cand_info);
                        $('#checkModal').modal('show');
                    }
                })

                $('#btnConfirmToVote').on('click', function() {
                    $('#checkModal').modal('hide');

                    let event_id = "{{ $vote_event->event_id ?? '' }}"
                    let candidates_id = []
                    $('.cand input[type=checkbox]').each(function() {
                        if($(this).prop('checked') === true) {
                            candidates_id.push($(this).val())
                        }
                    })

                    let post_data = {};
                    post_data._token = "{{ csrf_token() }}"
                    post_data.event_id = event_id
                    post_data.candidates = candidates_id

                    const resultAlert = Swal.mixin({
                        timer: 3000,
                        timerProgressBar: true,
                        text: "畫面將於3秒後跳轉到投票結果頁面",
                        didClose: () => {
                            location.href = '{{ route("vote.result", ["event_id" => ":event_id"]) }}'
                                .replace(':event_id', event_id)
                        }
                    });

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('vote') }}",
                        contentType: 'application/json',
                        data: JSON.stringify(post_data),
                    }).done(function(re) {
                        resultAlert.fire({
                            icon: "success",
                            title: "投票成功！",
                        });
                    }).fail(function(re) {
                        let error_status = JSON.parse(re.responseText).status
                        if(error_status === 'voted') {
                            resultAlert.fire({
                                title: "已經投過票囉！",
                                icon: "warning",
                            });
                        } else {
                            console.log(re);
                        }
                    })
                })
            })
        </script>
    @endif
@endsection
