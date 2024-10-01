@extends('front.index')

@section('body')
    <h2 class="title shadow-sm">
        <img src="{{ asset('assets/header.jpg') }}" height="10%" width="10%">
        <span>桃園區域網路中心</span>
    </h2>
    <div class="container">
        @if ($status === 'error')
            <div class="">
                <div class="not_available">
                    {{ $error_msg }}
                </div>
            </div>
        @else
            <div>
                <h1 class="vote_name fw-bold text-center">{{ $vote_event->event_name }}</h1>
                <div class="notice mb-3">
                    <p class="fw-bold">注意事項：</p>
                    <p>１、一人最少１票，最多 <span class="text-danger fw-bold">{{ $vote_event->max_vote_count }}</span> 票</p>
                    <p>２、送出後無法再修改或重新投票，請謹慎操作!</p>
                    <p>３、自2024年首次採取線上投票，請掃描QR code進行投票。重複執行掃瞄將顯示前次投票的結果。</p>
                    <p>４、若遇網路或系統問題無法進行電子投票，將由主席宣布後，改採紙本投票。本張視同紙本選票，每所學校限領一張，圈選後請對折放入投票箱。</p>
                </div>
                <div class="can_vote">
                    <h4 class="fw-bold">候選人：</h4>
                    @csrf
                    <div class="candidates">
                        @foreach ($candidates as $key => $cand)
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
                        <button id="btnVote" data-bs-toggle="modal">送出投票</button>
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
                            <p>送出後無法再修改或重新投票，請確認以下是否為您要投票的候選人</p>
                            <div class="check_cand mt-3 px-2"></div>
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
                    let candidates_id = []

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

                        $('.cand input[type=checkbox]').each(function() {
                            if($(this).prop('checked') === true) {
                                checkedCount++
                                candidates_id.push($(this).val())
                                let candidateNum = $(this).closest('.cand').find('.no').text();
                                let candidateSchool = $(this).closest('.cand').find('.intro p').text();
                                let candidateName = $(this).closest('.cand').find('.intro strong').text();
                                cand_info += '<p class="fs-5 mb-2">' + check_icon +
                                    '<strong class="ms-2">' + candidateNum + ' ' + candidateName + ' ' + candidateSchool + '</strong></p>';
                            }
                        })

                        if(checkedCount > {{ $vote_event->max_vote_count ?? 0 }}) {
                            alert('最多勾選 {{ $vote_event->max_vote_count }} 位候選人!')
                            return;
                        } else if (checkedCount < 1) {
                            alert('至少勾選1位，至多3位候選人')
                            return;
                        } else {
                            $('.check_cand').html(cand_info);
                            $('#checkModal').modal('show');
                        }
                    })

                    $('#btnConfirmToVote').on('click', function() {
                        let event_id = "{{ $vote_event->event_id ?? '' }}"
                        let qrcode = "{{ $qrcode_string ?? '' }}"

                        let post_data = {};
                        post_data._token = "{{ csrf_token() }}"
                        post_data.event_id = event_id
                        post_data.qrcode_string = qrcode
                        post_data.candidates = candidates_id

                        $.ajax({
                            type: 'POST',
                            url: "{{ route('vote') }}",
                            contentType: 'application/json',
                            data: JSON.stringify(post_data),
                        }).done(function(re) {
                            alert('投票成功')
                            location.href = '{{ route("vote.result", ["event_id" => ":event_id", "qrcode_string" => ":qrcode"]) }}'
                                .replace(':event_id', event_id)
                                .replace(':qrcode', qrcode);
                        }).fail(function(re) {
                            console.log(re)
                            alert('發生錯誤：' + re.responseText);
                        });
                    })
                })
            </script>
        @endif
    </div>
@endsection
