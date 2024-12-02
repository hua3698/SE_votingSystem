@extends('front.common')

@section('body')
    <style>
        a {
            color: #000;
            text-decoration: none;
        }
    </style>
    <div class="filter-bar">
        <div class="filter-buttons">
            <button data-filter="all" class="filter active">全部</button>
            <button data-filter="1" class="filter">進行中</button>
            <button data-filter="0" class="filter">尚未開始</button>
            <button data-filter="2" class="filter">已結束</button>
        </div>
        <div class="filter-tools">
            <input
                type="text"
                class="search-input"
                placeholder="搜尋投票活動..."
            />
            <button id="btnSearch" class="search-button">搜尋</button>
        </div>
    </div>
    <div class="activity-container">
        <div class="activity-grid">
            @foreach($votes as $key => $vote)
                <div class="activity-card" data-event="{{ $vote->event_id }}" data-status="{{ $vote->status }}">
                    <img
                        src="{{ asset('assets/a' . ($vote->event_id) . '.png') }}"
                        alt="活動圖片"
                        class="activity-image"
                    />
                    <h3 class="activity-title">{{ $vote->event_name }}</h3>
                    <p class="activity-date">{{ $vote->start_time }}</p>
                    <p class="activity-date">{{ $vote->end_time }}</p>
                    <div class="activity-info">
                        <p class="activity-participants">參與人數<br />{{ $vote->total_participants }} 人</p>
                        <p class="activity-days">剩餘天數<br />{{ $vote->remain_date }}</p>
                    </div>
                    @if ($vote->remain_date > 0 && $vote->status == 1)
                        <div class="vote-now">立即投票</div>
                    @endif
                </div>
            @endforeach
        </div>
        {{-- <div class="pagination">
            @if ($current_page != 1)
                <a href="{{ route('index', ['page'=>$current_page -1]) }}">
                    <button class="page-button">&laquo;</button>
                </a>
            @endif
            @for ($i = 1; $i <= $last_page; $i++) 
                <a href="{{ route('index', ['page'=>$i]) }}">
                    <button class="page-button">{{ $i }}</button>
                </a>
            @endfor
            @if ($current_page != $last_page)
                <a href="{{ route('index', ['page'=>$current_page +1]) }}" >
                    <button class="page-button">&raquo;</button>
                </a>
            @endif
            <a href="{{ route('index', ['page'=>$last_page]) }}">
                <button class="page-button">最後頁</button>
            </a>
        </div> --}}
    </div>

    <script>
        $(function() {
            $('.filter').on('click', function() {
                $('.filter').removeClass('active');
                $(this).addClass('active');

                const filter = $(this).data('filter');

                $('.activity-card').each(function () {
                    const status = $(this).data('status');
                    if (filter === 'all' || filter === status) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            })

            $('.activity-card').on('click', function() {
                const event_id = $(this).closest('.activity-card').data('event')
                const status = $(this).data('status');
                if(status != 2) {
                    location.href = '{{ route("front.vote", ":event_id") }}'.replace(':event_id', event_id);
                } else {
                    location.href = '{{ route("vote.result", ":event_id") }}'.replace(':event_id', event_id);
                }
            })

            $('#btnSearch').on('click', function() {
                const keyword = $('.search-input').val()

                if(!keyword) {
                    $('.filter').click()
                } else {
                    let post_data = {};
                    post_data._token = "{{ csrf_token() }}"
                    post_data.keyword = keyword

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('search.vote') }}",
                        contentType: 'application/json',
                        data: JSON.stringify(post_data),
                    }).done(function(re) {
                        console.log(re)
                        $('.activity-card').hide();
                        re.forEach(item => {
                            $(`.activity-card[data-event="${item.event_id}"]`).show();
                        });
                    }).fail(function(re) {
                        alert('error')
                    })
                }
            })
        })
    </script>
@endsection