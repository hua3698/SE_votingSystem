@extends('admin.common')

@section('body')
<div class="container admin_container">
    <h2 class="text-center fw-bold mb-5">
        {{ $vote_event->event_name }}
    </h2>
    <div class="shadow block mb-5">
        <h3 class="text-center fw-bold mb-2">開票結果</h3>
        <h5 class="text-center mb-4">由排名前 {{ $vote_event->number_of_winners }} 名獲獎</h5>
        <div>
            <table class="table table-striped result_table">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col" class="text-center">名次</th>
                        <th scope="col">學校</th>
                        <th scope="col">姓名</th>
                        <th scope="col">總得票數</th>
                    </tr>
                </thead>
                <tbody class="fs-4">
                    @foreach($rank as $key => $rank)
                        <tr>
                            <td class="medal">
                                @if ($rank->rank <= $vote_event->number_of_winners)
                                    <img src="{{ asset('assets/medal.png') }}" alt="">
                                @endif
                            </td>
                            <td class="text-center" style="width: 10%">
                                <span class="rank_{{ $rank->rank }}">{{ $rank->rank }}</span>
                            </td>
                            <td>{{ $rank->school }}</td>
                            <td>{{ $rank->name }}</td>
                            <td>{{ $rank->total }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="shadow block mb-5">
        <div class="d-flex justify-content-center">
            <h3 class="text-center fw-bold mb-5 me-3">投票明細</h3>
            <form method="GET" action="{{ route('export.detail', ['event_id' => $vote_event->event_id]) }}" target="_blank">
                @csrf
                <button id="btnQrcodePDF" type="submit" class="btn btn-outline-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-circle" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8m15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293z"/>
                    </svg>
                    匯出結果
                </button>
            </form>
        </div>
        <div>
            <table class="table table-bordered detail_table">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">#</th>
                        <th scope="col">QR Code序號</th>
                        <th scope="col">投票明細</th>
                        <th scope="col">時間</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $record)
                        <tr>
                            <th class="text-center">{{ $key + 1}}</th>
                            <td>{{ $record['qrcode_string'] }}</td>
                            <td class="vote_detail_td">
                                @foreach ($record['vote'] as $vote)
                                    <p>
                                        <span>{{ $vote['number'] }}號</span>
                                        <span>{{ $vote['name'] }}</span>
                                        <span>{{ $vote['school'] }}</span>
                                    </p>
                                @endforeach
                            </td>
                            <td>{{ $record['updated_at'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('script_js')

@endsection
