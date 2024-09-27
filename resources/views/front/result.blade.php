@extends('front.index')

@section('body')
    <div class="container">
        <h2 class="text-center mb-5">
            <img src="{{ asset('assets/header.jpg') }}" height="10%" width="10%">
            <span>桃園區域網路中心</span>
        </h2>
        @if ($status !== 'error')
            <div>
                <div class="finished shadow-sm text-center fs-3 mb-5">
                    <p style="color: #198754">
                        <svg width="30" height="30" fill="currentColor" class="bi bi-check2-circle" viewBox="0 0 16 16">
                            <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0"/>
                            <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0z"/>
                        </svg>
                    </p>
                    <p>您已經完成投票囉!</p>
                </div>
                <div>
                    <p class="fs-6">QR Code代號<span class="text-primary">{{ $qrcode_string }}</span></p>
                    <p class="fs-6">投票紀錄</p>
                    <div>
                        @foreach ($records as $key => $record)
                            <ul class="list-group list-group-horizontal">
                                <li class="list-group-item">{{ $record->cand_number }}號</li>
                                <li class="list-group-item">{{ $record->cand_school }}</li>
                                <li class="list-group-item">{{ $record->cand_name }}</li>
                                <li class="list-group-item">{{ $record->vote_time }}</li>
                            </ul>
                        @endforeach
                    </div>
                </div>
            </div>
            @section('script_js')
            <script>
            </script>
            @endsection
        @endif
    </div>
@endsection
