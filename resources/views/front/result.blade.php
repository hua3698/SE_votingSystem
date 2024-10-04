@extends('front.index')

@section('body')
    <h2 class="title shadow-sm">
        <img src="{{ asset('assets/header.jpg') }}" height="10%" width="10%">
        <span>桃園區域網路中心</span>
    </h2>
    <div class="container">
        @if ($status !== 'error')
            
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
            <div class="vote_record">
                <p class="fs-5 text-secondary text-center">再次提醒</p>
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
                        <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                    </svg>
                    送出後已無法修改或重新投票</p>
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dot" viewBox="0 0 16 16">
                        <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3"/>
                    </svg>
                    待主持人宣布投票結束後，才會公布最終結果
                </p>
            </div>
        @endif
    </div>
@endsection
