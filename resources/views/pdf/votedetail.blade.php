<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>投票明細</title>
    <style>
        table {
            width: 80%;
            margin: 10px auto;
            border-collapse: collapse;
        }

        table thead {
            background: #c8d6eb;
        }

        table th, td {
            border: 1px solid;
        }

        td {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
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
</body>
</html>