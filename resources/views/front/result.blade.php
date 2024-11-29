@extends('front.common')

@section('body')
    @if ($status !== 'error')
        <style>
            .activity-container {
                min-height: calc(100vh - 150px);
            }
        </style>
        <div class="activity-container">
            <div class="title_content">
                <h1 class="vote_name fw-bold text-center" >投票結果</h1>
                <h3 class="vote_name fw-bold text-center" >投票主題：{{ $event_name }}</h1>
            </div>
        </div>
        {{-- <div class="container">
            <div class="text-center fs-3 my-3">
                <p>已完成投票！</p>
            </div>
            <div class="vote_record mb-4">
                <p class="fs-5 text-secondary text-center">投票紀錄</p>
                <div>
                    @foreach ($records as $key => $record)
                        <ul class="list-group list-group-horizontal mb-2">
                            <li class="list-group-item">{{ $record->cand_number }}號</li>
                            <li class="list-group-item">
                                <p>{{ $record->cand_school }}</p>
                                <p class="fw-bold">{{ $record->cand_name }}</p>
                            </li>
                            <li class="list-group-item">{{ $record->vote_time }}</li>
                        </ul>
                    @endforeach
                </div>
            </div>
        </div> --}}
    @endif
@endsection
