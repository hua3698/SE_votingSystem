@extends('admin.common')

@section('body')
    <div class="container">
        <div class="all_vote">
            <div class="title d-flex justify-content-between mb-3">
                <h4>所有投票 ({{ $total }})</h4>
                <button id="add_new_vote" class="btn-success">建立新的投票</button>
            </div>
            <div class="vote_group row">
                @foreach($vote_event as $event)
                    <div class="col col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">{{ $event->event_name }}</h5>
                                @if ($event->status === 1)
                                    <span class="badge text-bg-success">投票進行中</span>
                                @elseif ($event->status === 2)
                                    <span class="badge text-bg-secondary">尚未開始</span>
                                    @else 
                                    <span class="badge text-bg-secondary">已結束</span>
                                @endif
                                <p class="card-text">開放時間： {{ $event->start_time }} ~ {{ $event->end_time }}</p>
                                <a href="{{ route('admin.vote.get', ['event_id' => $event->event_id]) }}" class="btn btn-primary">查看詳細內容</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('script_js')
<script>
    $(function() {
        $('#add_new_vote').on('click', function() {
            window.location.href="{{ url('admin/createvote') }}"
        })
    })
</script>
@endsection