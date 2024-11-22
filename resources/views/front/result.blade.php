@extends('front.common')

@section('body')
    <div class="container">
        @if ($status !== 'error')
            <div class="vote_record my-3">
                <p class="fs-2 fw-bold text-center">{{ $event_name }}</p>
            </div>
            <div class="finished shadow-sm text-center fs-3 my-3">
                <p class="my-3" style="color: #198754">
                    <svg width="30" height="30" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                        <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
                        <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                    </svg>
                </p>
                <p>已完成投票！</p>
            </div>
            <div class="vote_name text-center mb-4">
                <p class="fs-5 text-secondary">QR Code序號</p>
                <p class="fs-3">{{ $qrcode_string }}</p>
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
        @endif
    </div>
@endsection
