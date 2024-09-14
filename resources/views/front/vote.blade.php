@extends('front.index')

@section('body')
    <div class="container">
        <h2 class="text-center">
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
                <p>1. 一人最多投3票</p>
                <p>2. 送出後無法再修改或重新投票，請謹慎操作!</p>
                <h1 class="vote_name">{{ $vote_event->event_name }}</h1>
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
                                <div class="intro">
                                    <div>
                                        <p>{{ $cand['school'] }}</p>
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
        @endif
        

        {{-- <div class="voted">
            <p>已經投過票囉!</p>
            <p>查看投票紀錄</p>
        </div> --}}

    </div>

    <script>
        $(function() {
            $('.cand').on('click', function(e) {
                if(!$(e.target).is('input[type=checkbox]')) {
                    console.log('a')
                    $(this).find('input[type=checkbox]').prop('checked', function(i, val) {
                        return !val;
                    });
                }
            })

            $('#btnVote').on('click', function() {
                let checkedCount = 0
                let candidates = []

                $('.cand input[type=checkbox]').each(function() {
                    if($(this).prop('checked') === true) {
                        checkedCount++
                        candidates.push($(this).val())
                    }
                })

                if(checkedCount > 3) {
                    alert('最多勾選3個!')
                    return;
                }

                let post_data = {};
                post_data._token = "{{ csrf_token() }}"
                post_data.event_id = "{{ $vote_event->event_id ?? '' }}"
                post_data.qrcode_string = "{{ $qrcode_string ?? '' }}"
                post_data.candidates = candidates

                $.ajax({
                    type: 'POST',
                    url: "{{ route('vote') }}",
                    contentType: 'application/json',
                    data: JSON.stringify(post_data),
                }).done(function(re) {
                    console.log(re)
                    alert('投票成功')
                }).fail(function(re) {
                    alert('發生錯誤：' + re.responseText);
                });
            })
        })
    </script>

@endsection
