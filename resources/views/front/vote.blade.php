@extends('front.common')

@section('body')
    @if ($status === 'error')
        <div class="container">
            <div class="not_available">
                {{ $error_msg }}
            </div>
        </div>
    @else
        <div class="title_background mb-3" style="background-image: url({{ asset('assets/back4.jpeg') }}); ">
            <h1 class="vote_name fw-bold text-center" >{{ $vote_event->event_name }}</h1>
        </div>

        <div class="container">
            <div class="notice">
                <p class="fw-bold">◆ 說明</p>
                <p>１、。</p>
            </div>
            <div class="notice mb-3">
                <p class="fw-bold">◆ 注意事項</p>
                <p>１、</p>
            </div>
            <div class="notice mb-3">
                <p class="fw-bold"><a href="{{ route('vote.candidate', ['event_id' => $vote_event->event_id]) }}">點擊前往查看候選人介紹</a></p>
            </div>
            <div class="can_vote">
                <h4 class="fw-bold">請勾選想要投票的候選人</h4>
                @csrf
                <div class="candidates">
                    @foreach ($candidates as $cand)
                        <div class="cand shadow-sm">
                            <div class="circle_div col-3">
                                <div class="form-check vote_check">
                                    <input name="cand[]" class="form-check-input" type="checkbox" value="{{ $cand->cand_id }}">
                                </div>
                            </div>
                            <div class="no">{{ $cand['number'] }}號</div>
                            <div class="intro">
                                <div>
                                    <p style="font-size: 1.2rem">{{ $cand['school'] }}</p>
                                    <strong>{{ $cand['name'] }}</strong>
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
                            let candidateSchool = $(this).closest('.cand').find('.intro p').text();
                            let candidateName = $(this).closest('.cand').find('.intro strong').text();
                            cand_info += '<p class="fs-5 mb-2">' + check_icon +
                                '<strong class="ms-2">' + candidateNum + ' ' + candidateSchool + ' ' + candidateName + '</strong></p>';
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
                    $('#btnConfirmToVote').attr('disabled', true)

                    let event_id = "{{ $vote_event->event_id ?? '' }}"
                    let qrcode = "{{ $qrcode_string ?? '' }}"
                    let candidates_id = []
                    $('.cand input[type=checkbox]').each(function() {
                        if($(this).prop('checked') === true) {
                            candidates_id.push($(this).val())
                        }
                    })

                    let post_data = {};
                    post_data._token = "{{ csrf_token() }}"
                    post_data.event_id = event_id
                    post_data.qrcode_string = qrcode
                    post_data.candidates = candidates_id

                    const resultAlert = Swal.mixin({
                        timer: 3000,
                        timerProgressBar: true,
                        text: "畫面將於3秒後跳轉到投票結果頁面",
                        didClose: () => {
                            location.href = '{{ route("vote.result", ["event_id" => ":event_id", "qrcode_string" => ":qrcode"]) }}'
                                .replace(':event_id', event_id)
                                .replace(':qrcode', qrcode);
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
