@extends('admin.common')

@section('body')
    <div class="container vote_detail">
        <h2 class="text-center fw-bold mb-5">{{ $vote_event->event_name }}</h2>
        <div class="shadow block mb-5">
            <div class="mb-5">
                <p class="card-text">投票時間：</p>
                <p class="card-text fw-bold">
                    {{ $vote_event->start_time }} ~ {{ $vote_event->end_time }}
                    @if ($vote_event->status === 1)
                        <span class="badge text-bg-success">投票進行中</span>
                    @elseif ($vote_event->status === 2)
                        <span class="badge text-bg-secondary">尚未開始</span>
                        @else 
                        <span class="badge text-bg-danger">已結束</span>
                    @endif
                </p>          
            </div> 
            <div class="col-12 col-lg-6 mb-5">
                <p>一些數字</p>
                {{-- <div class="block row">
                    <div class="item col">
                        <div>每人最多可以投幾票</div>
                        <div>{{ $vote_event->max_vote_count }}</div>
                    </div>
                    <div class="item col">
                        <div>最多選出幾名winner</div>
                        <div>{{ $vote_event->number_of_winners }}</div>
                    </div>
                    <div class="item col">
                        <div>設定發放的Qrcode數</div>
                        <div>{{ $vote_event->number_of_qrcodes }}</div>
                    </div>
                </div> --}}
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">每人最多可以投幾票</th>
                            <th scope="col">最多選出幾名winner</th>
                            <th scope="col">設定發放的Qrcode數</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="col">{{ $vote_event->max_vote_count }}</th>
                            <th scope="col">{{ $vote_event->number_of_winners }}</th>
                            <th scope="col">{{ $vote_event->number_of_qrcodes }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p>候選人：</p>
            <div class="col-12 col-lg-6 mb-3">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">候選人名稱</th>
                            <th scope="col">候選人學校</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $key => $cand)
                        <tr>
                            <th scope="row">{{ ($key + 1) }}</th>
                            <td>{{ $cand['name'] }}</td>
                            <td>{{ $cand['school'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="shadow block">
            <div class="qrcode_header my-3">
                <h4 class="me-3">QR code</h4>
                <div>
                    <form id="downloadPdfForm" method="POST" action="{{ route('admin.vote.pdf', ['event_id' => $vote_event->event_id]) }}" target="_blank">
                        @csrf
                        <!-- 其他隱藏輸入欄位可以放這裡 -->
                        <button id="btnQrcodePDF" type="submit" class="btn btn-outline-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-circle" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z"/>
                            </svg>
                            下載PDF
                        </button>
                    </form>
                </div>
            </div>
            <div class="row">
                @foreach ($qrcodes as $qrcode)
            <div class="col-3 text-center" style="margin-bottom:3rem;">
                <p>{{ $qrcode->qrcode_string }}</p>
                <img src="{{ $qrcode->qrcode_url }}" alt="QR Code">
            </div>
            @endforeach
            </div>
        </div>
    </div>
@endsection

@section('script_js')
<script>
    $(function() {

    })
</script>
@endsection
