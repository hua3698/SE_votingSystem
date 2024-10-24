@extends('admin.common')

@section('body')
    <div class="container mb-3">
        <div class="all_vote">
            <div class="title d-flex justify-content-between mb-3">
                <h4>所有投票 ({{ $total }})</h4>
                <button id="add_new_vote" class="btn-success">建立新的投票</button>
            </div>
            <div class="vote_group row">
                @foreach($vote_event as $event)
                    <div class="col col-md-6">
                        <div class="card" data-event="{{ $event->event_id }}">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <span class="me-3">{{ $event->event_name }}</span>
                                    @if ($event->manual_control == 1)
                                        @if ($event->vote_is_ongoing == 0)
                                            <span class="badge text-bg-secondary">尚未開始</span>
                                        @elseif ($event->vote_is_ongoing == 1)
                                            <span class="badge text-bg-success">投票進行中</span>
                                            @else 
                                            <span class="badge text-bg-danger">已結束</span>
                                        @endif
                                    @else
                                        @if ($event->status == 0)
                                            <span class="badge text-bg-secondary">尚未開始</span>
                                        @elseif ($event->status == 1)
                                            <span class="badge text-bg-success">投票進行中</span>
                                        @else 
                                            <span class="badge text-bg-danger">已結束</span>
                                        @endif
                                    @endif
                                </h5>
                                <p class="mb-0">開始時間 <span class="text-primary">{{ $event->start_time }}</span></p>
                                <p class="mb-0">結束時間 <span class="text-primary">{{ $event->end_time }}</span></p>
                                {{-- <a href="{{ route('admin.vote.get', ['event_id' => $event->event_id]) }}" class="btn btn-primary">查看詳細內容</a> --}}
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
            window.location.href="{{ url('outstand/createvote') }}"
        })

        $('.card').on('click', function() {
            let event_id = $(this).data('event')
            location.href = '{{ route("admin.vote.get", ":event_id") }}'.replace(':event_id', event_id);
        })
    })
</script>
@endsection