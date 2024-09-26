@extends('front.index')

@section('body')
    <div class="container">
        <h2 class="text-center mb-3">
            <img src="{{ asset('assets/header.jpg') }}" height="10%" width="10%">
            <span style="color: #8CD790">桃園</span>
            <span style="color: #30A9DE">區域網路中心</span>
        </h2>

        @if ($status === 'error')
            <div class="">
                <div class="not_available">
                    {{ $error_msg }}
                </div>
            </div>
        @else
            <div>
                <h1 class="vote_name fw-bold">{{ $vote_event->event_name }}</h1>
                <div class="notice mb-3">
                    <p class="fw-bold">注意事項：</p>
                    <p>1. 一人最多投 <span class="text-danger fw-bold">{{ $vote_event->max_vote_count }}</span> 票</p>
                    <p>2. 送出後無法再修改或重新投票，請謹慎操作!</p>
                </div>
                <div class="can_vote">
                    @csrf
                    <div class="candidates">
                        @foreach ($candidates as $key => $cand)
                            <div class="cand">
                                <div class="circle_div col-3">
                                    <div class="form-check vote_check">
                                        <input name="cand[]" class="form-check-input" type="checkbox" value="{{ $cand->cand_id }}">
                                    </div>
                                </div>
                                <div class="no">{{ $key + 1 }}.</div>
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
                            <h5 class="modal-title">再次確認</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Modal body text goes here.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                            <button id="btnConfirmToVote" type="button" class="btn btn-primary">確定送出</button>
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

                        $('.cand input[type=checkbox]').each(function() {
                            if($(this).prop('checked') === true) {
                                checkedCount++
                                candidates_id.push($(this).val())

                                let candidateSchool = $(this).closest('.cand').find('.intro p').text();
                                let candidateName = $(this).closest('.cand').find('.intro strong').text();
                                cand_info += '<p class="fs-5"><strong>' + candidateSchool + ' ' + candidateName + '</strong></p>';
                            }
                        })

                        if(checkedCount > {{ $vote_event->max_vote_count ?? 0 }}) {
                            alert('最多勾選 {{ $vote_event->max_vote_count }} 位候選人!')
                            return;
                        } else if (checkedCount < 1) {
                            alert('至少勾選1位，至多3位候選人')
                            return;
                        } else {
                            $('.modal-body').html('確定要投給：' + cand_info);
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
