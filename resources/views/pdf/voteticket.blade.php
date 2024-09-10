<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Qr Code</title>
    <style>
        @font-face {
            font-family: 'kaiu';
            src: url('{{ storage_path('fonts/kaiu.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        body {
            font-family: 'kaiu', DejaVu Sans, sans-serif;
        }
        
        .page-break {  /* 控制頁面分隔的關鍵在這裡 */
            page-break-after: always;
        }

        .text-center {
            text-align: center;
        }

        .top {
            min-height: 300px;
            border-bottom: 1px solid;
        }

        .top>div {
            float: left;
        }

        .candidate {
            height: 100px;
            margin-bottom: 2rem;
            display: flex;
        }

        .candidate>div {
            border: 1px solid;
            height: inherit;
        }

        .container {
            max-width: 90%;
            margin: 0 auto;
        }

        .vote {
            width: 15%;
        }

        .number {
            font-size: 3rem;
            width: 15%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .intro {
            width: 70%;
        }

        .intro span {
            font-size: 1.5rem;
            display: inline-block;
            margin: 20px;
        }

        table {
            width: 100%; /* 設置表格寬度為 100% */
            border-collapse: collapse; /* 合併相鄰的邊框 */
        }

        th, td {
            border: 1px solid #000; /* 設置單元格邊框為 1 像素實線 */
            padding: 8px; /* 單元格內部的邊距 */
            text-align: left; /* 左對齊單元格內容 */
        }

        th {
            background-color: #f2f2f2; /* 表頭背景色 */
            font-weight: bold; /* 表頭字體加粗 */
        }

        tbody tr:hover {
            background-color: #f5f5f5; /* 滑鼠懸停時的背景色 */
        }
    </style>
</head>
<body>
    @foreach ($qrcodes as $qrcode)
    <div class="page-break">
        <h2 class="text-center">{{ $voteEvent->event_name }}</h2>
        <div class="top container">
            <div>
                <h4>注意事項</h4>
                <p>1. Bootstrap’s form controls expand on our Rebooted form styles with classes. Use these classes to opt into their customized displays 
                    for a more consistent rendering across browsers and devices.</p>
            </div>
            <div>
                <img src="{{ $qrcode->qrcode_url }}" alt="">
            </div>
        </div>
        <div class="container">
            <h4>候選人</h4>
            @foreach ($candidates as $key => $cand)
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">{{ ($key + 1) }}</th>
                            <th scope="col">
                                <div>
                                    <span>{{ $cand->name }}</span>
                                    <span>{{ $cand->school }}</span>
                                </div>
                            </th>
                        </tr>
                    </tbody>
                </table>
            @endforeach
    </div>
    @endforeach
</body>
</html>