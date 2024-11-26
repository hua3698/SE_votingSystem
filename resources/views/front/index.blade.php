@extends('front.common')

@section('body')
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
            <button class="search-button">搜尋</button>
        </div>
    </div>
    <div class="activity-container">
        <div class="activity-grid">
            @foreach($votes as $key => $vote)
                <div class="activity-card" data-event="{{ $vote->event_id }}" data-status="{{ $vote->status }}">
                    <img
                        src="{{ asset('assets/a' . ($key + 1) . '.png') }}"
                        alt="活動圖片"
                        class="activity-image"
                    />
                    <h3 class="activity-title">{{ $vote->event_name }}</h3>
                    <p class="activity-date">{{ $vote->start_time }}</p>
                    <div class="activity-info">
                        <p class="activity-participants">參與人數<br />72 人</p>
                        <p class="activity-days">剩餘天數<br />{{ $vote->remain_date }}</p>
                    </div>
                    <div class="vote-now">立即投票</div>
                </div>
            @endforeach
        </div>
        <div class="pagination">
            <button class="page-button">1</button>
            <button class="page-button">2</button>
            <button class="page-button">3</button>
            <button class="page-button">最後頁</button>
            <button class="page-button">下一頁</button>
        </div>
    </div>

    <script>
        $(function() {
            $('.filter').on('click', function() {
                $('.filter').removeClass('active');
                $(this).addClass('active');

                const filter = $(this).data('filter');

                $('.activity-card').each(function () {
                    const status = $(this).data('status');

                    console.log(status)

                    if (filter === 'all' || filter === status) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            })

            $('.activity-card').on('click', function() {
                let event_id = $(this).data('event')
                location.href = '{{ route("front.vote", ":event_id") }}'.replace(':event_id', event_id);
            })
        })
    </script>
@endsection