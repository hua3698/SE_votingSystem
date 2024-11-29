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
                        <th scope="col">姓名</th>
                        <th class="right" scope="col">總得票數</th>
                        <th></th>
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
                            <td>{{ $rank->name }}</td>
                            <td class="right">{{ $rank->total }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="shadow block mb-5">
        <div class="d-flex justify-content-center">
            <h3 class="text-center fw-bold mb-5 me-3">投票明細</h3>
        </div>
        <div>
            <table class="table table-bordered detail_table">
                <thead>
                    <tr>
                        <th scope="col" class="text-center">#</th>
                        <th scope="col">會員編號</th>
                        <th scope="col">會員名稱</th>
                        <th scope="col">投票明細</th>
                        <th scope="col">投票時間</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $key => $record)
                        <tr>
                            <th class="text-center">{{ $key + 1}}</th>
                            <td>{{ $record['user_id'] }}</td>
                            <td>{{ $record['user_name'] }}</td>
                            <td class="vote_detail_td">
                                @foreach ($record['vote'] as $vote)
                                    <p>
                                        <span style="padding: 5px; background: #ddd;">{{ $vote['number'] }}號</span>
                                        <span>{{ $vote['name'] }}</span>
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
