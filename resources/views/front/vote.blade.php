@extends('front.index')

@section('body')
    @if ($status === 'error')
        <div class="container">
            <div class="not_available">
                {{ $error_msg }}
            </div>
        </div>
    @else
        <h2 class="title">
            <img src="{{ asset('assets/header.jpg') }}" height="10%" width="10%">
            <span>桃園區域網路中心</span>
        </h2>
        <div class="title_background mb-3" style="background-image: url({{ asset('assets/back4.jpeg') }}); ">
            <h1 class="vote_name fw-bold text-center" >{{ $vote_event->event_name }}</h1>
        </div>

        <div class="container">
            <div class="notice">
                <p class="fw-bold">說明</p>
                <p>本次投票依據【TANet 桃園區域網路中心傑出網路管理人員選拔實施要點】辦理</p>
                <p>投票方式由每單位一票，每票最多圈選三名，超過三名之選票以廢票計。</p>
            </div>
            <div class="notice mb-3">
                <p class="fw-bold">注意事項</p>
                <p>１、每張QR code序號最少１票，最多可投 <span class="text-danger fw-bold">{{ $vote_event->max_vote_count }}</span> 票</p>
                <p>２、送出後無法再修改或重新投票，請謹慎操作!</p>
                <p>３、自2024年首次改採線上投票，請掃描QR code進行投票。投票後可再執行QR code掃瞄查詢該選票投票內容。</p>
                <p>４、若遇網路或系統問題無法進行電子投票，將由主席宣布後，改採紙本投票。本張視同紙本選票，各單位限領一張，圈選後請對折放入投票箱。</p>
                <p>５、候選人以收件先後排序列出。</p>
            </div>
            <div class="notice mb-3">
                <p class="fw-bold">候選人簡報（人員名單依收件先後排序）</p>
                <p>1. <a href="{{ asset('assets/1_啟英高中_李栢松.pdf') }}" target="_blank">啟英高中 李栢松 組長</a></p>
                <p>2. <a href="{{ asset('assets/2_連江縣教網中心_吳貽樺.pdf') }}" target="_blank">連江縣教網中心 吳貽樺 組員</a></p>
                <p>3. <a href="{{ asset('assets/3_長庚大學_吳凱威.pdf') }}" target="_blank">長庚大學 吳凱威 專員</a></p>
                <p>4. <a href="{{ asset('assets/4_國防大學_江彥廷.pdf') }}" target="_blank">國防大學 江彥廷 中尉系統管制官</a></p>
                <p>5. <a href="{{ asset('assets/5_育達高中_張以勤.pdf') }}" target="_blank">育達高中 張以勤 組長</a></p>
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
                        <p>送出後無法重新投票，請確認以下您所選名單再送出</p>
                        <div class="check_cand my-3 px-2"></div>
                        <p id="max_candidiate">ps.最多可選{{ $vote_event->max_vote_count }}位候選人</p>
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
                        if(checkedCount == {{ $vote_event->max_vote_count ?? 0 }}) $('#max_candidiate').hide()
                        else $('#max_candidiate').show()
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
