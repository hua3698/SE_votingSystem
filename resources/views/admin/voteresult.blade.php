@extends('admin.common')

@section('body')
<div class="container vote_detail">
    <h2 class="text-center fw-bold mb-5">
        {{ $vote_event->event_name }}
    </h2>
    <div class="shadow block mb-5">
        <h3 class="text-center fw-bold mb-5">開票結果</h3>
        <div>
            <table class="table result_table">
                <thead>
                    <tr>
                        <th scope="col">名次</th>
                        <th scope="col">學校</th>
                        <th scope="col">姓名</th>
                        <th scope="col">總得票數</th>
                    </tr>
                </thead>
                <tbody class="fs-4">
                    @foreach($rank as $key => $rank)
                        <tr>
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
</div>

@endsection

@section('script_js')

@endsection
