@extends('admin.common')

@section('body')
<div class="container admin_container">
    <h2 class="text-center fw-bold mb-5">
        {{ $vote_event->event_name }}
    </h2>
    <div class="shadow block mb-3">
        <h5 class="text-center mb-3">
            頁面更新時間：
            <span id="update_time">{{ $system_time }}</span>
            <span class="fs-6">(投票期間每20秒更新一次)</span>
        </h5>
        <div class="top mb-5">
            <h2>共 <span id="count_vote" class="text-danger">{{ count($qrcodes) }}</span> 位已完成投票</h2>
        </div>
        <div class="px-3 row justify-content-center mb-3">
            <div class="col-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th>QR Code序號</th>
                            <th>投了幾位</th>
                            <th>投票時間</th>
                        </tr>
                    </thead>
                    <tbody class="tbody">
                        @foreach ($qrcodes as $key => $qrcode)
                            <tr>
                                <td>{{ $qrcode->qrcode_string }}</td>
                                <td class="text-primary fs-4">
                                    <svg width="20" height="20" fill="currentColor" class="bi bi-ticket" viewBox="0 0 16 16">
                                        <path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6zM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5z"/>
                                    </svg>
                                    {{ $qrcode->total_votes }}
                                </td>
                                <td>{{ $qrcode->updated_at }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="spinner-border d-none text-center" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script_js')
    @if ($vote_event->status === 1)
        <script>
            $(function() {
                function fetchData() {
                    $('.tbody').html('');

                    $.ajax({
                        type: 'POST',
                        url: "{{ route('vote.check.post', ['event_id' => $vote_event->event_id]) }}",
                        contentType: 'application/json',
                        data: JSON.stringify({
                            _token: "{{ csrf_token() }}"
                        }),
                        success: function(response) {
                            let rows = '';
                            response.qrcodes.forEach(function(qrcode, key) {
                                rows += '<tr>' +
                                            '<td>' + qrcode.qrcode_string + '</td>' +
                                            '<td class="text-primary fs-4">' + 
                                                '<svg width="20" height="20" fill="currentColor" class="bi bi-ticket" viewBox="0 0 16 16">' +
                                                    '<path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6zM1.5 4a.5.5 0 0 0-.5.5v1.05a2.5 2.5 0 0 1 0 4.9v1.05a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-1.05a2.5 2.5 0 0 1 0-4.9V4.5a.5.5 0 0 0-.5-.5z"/>' +
                                                '</svg> ' + 
                                                qrcode.total_votes + 
                                            '</td>' +
                                            '<td>' + formatTime(qrcode.updated_at) + '</td>' +
                                        '</tr>';
                            });

                            $('.tbody').append(rows);
                            $('#update_time').html(formatTime(response.system_time))
                            countAnimate(response.qrcodes.length)
                        },
                        error: function(err) {
                            console.error('發生錯誤：' + err.responseText);
                        }
                    });
                }

                function countAnimate(number) {
                    let currentVal = parseInt($('#count_vote').text());
                    let targetVal = number
                    let interval = 100; // 動畫速度，每次增加的間隔時間 (毫秒)

                    let countUp = setInterval(function() {
                        if (currentVal < targetVal) {
                        currentVal++;
                        $('#count_vote').text(currentVal);
                        } else {
                            clearInterval(countUp); // 結束動畫
                        }
                    }, interval);
                }

                function formatTime(time) {
                    let date = new Date(time);
                    date.setHours(date.getUTCHours() + 8);

                    // 轉換時間格式 YYYY-MM-DD HH:MM:SS
                    let formattedDate = date.getFullYear() + '-' +
                        ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                        ('0' + date.getDate()).slice(-2) + ' ' +
                        ('0' + date.getHours()).slice(-2) + ':' +
                        ('0' + date.getMinutes()).slice(-2) + ':' +
                        ('0' + date.getSeconds()).slice(-2);

                    return formattedDate;
                }

                function showVotingTable() {
                    $('table').hide()
                    $('.spinner-border').removeClass('d-none')

                    setTimeout(function() {
                        fetchData();
                        $('table').show();
                        $('.spinner-border').addClass('d-none');
                    }, 2000);
                }

                $('#btnDectivateVote').on('click', function() {
                    let activate = confirm('確定要結束投票嗎?')

                    if (activate) {
                        let post_data = {}
                        post_data._token = "{{ csrf_token() }}"
                        post_data.event_id = {{ $vote_event->event_id }}
                        post_data.activate = 1

                        $.ajax({
                            type: 'PUT',
                            url: "{{ route('deactivate.vote') }}",
                            contentType: 'application/json',
                            data: JSON.stringify(post_data),
                        }).done(function(re) {
                            console.log(re)
                            alert('設定成功')
                            location.href = "{{ route('admin.vote.result', ['event_id' => $vote_event->event_id]) }}"
                        }).fail(function(re) {
                            alert('發生錯誤：' + re.responseText);
                        });
                    }
                })

                setInterval(function() {
                    showVotingTable();
                }, 20000); // 單位是毫秒 (千分之一秒)
            })
        </script>
    @endif
@endsection



