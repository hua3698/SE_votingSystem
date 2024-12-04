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
            <h5 class="text-center mb-4">由排名前3名獲獎</h5>
            {{-- <h5 class="text-center mb-4">由排名前 {{ $vote_event->number_of_winners }} 名獲獎</h5> --}}
            <div style="width:60%;">
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
    @endif
@endsection
