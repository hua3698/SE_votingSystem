@extends('admin.common')

@section('body')
    <div class="container vote_detail">
        <div class="row">
            <h2 class="mb-3">{{ $vote_event->event_name }}</h2>

            <p class="card-text">開放投票時間： {{ $vote_event->start_time }} ~ {{ $vote_event->end_time }}</p>
            <p>選項：</p>
            @foreach ($candidates as $cand)
                <p>{{ $cand->candidates_name }}</p>
            @endforeach

            <div class="underline"></div>

            <h4>QR code</h4>
            @foreach ($qrcodes as $qrcode)
            <div class="col-3 text-center" style="margin-bottom:3rem;">
                <p>{{ $qrcode->qrcode_string }}</p>
                <img src="{{ $qrcode->qrcode_url }}" alt="QR Code">
            </div>
            @endforeach
            <div class="underline"></div>

            <h4>投票結果</h4>

        </div>
    </div>
@endsection

@section('script_js')
@endsection
