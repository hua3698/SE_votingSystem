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

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .top {
            min-height: 300px;
            border-bottom: 1px solid;
            padding: 1rem;
        }

        .top img {
            width: 200px;
        }

        .down {
            padding: 1rem;
        }
    </style>
</head>
<body>
    @foreach ($qrcodes as $qrcode)
    <div class="page-break">
        <h2 class="text-center">{{ $voteEvent->event_name }}</h2>
        <div class="top">
            <table>
                <tr>
                    <td width="70%" style="vertical-align: baseline;">
                        <h4>注意事項</h4>
                        <p>1. Bootstrap’s form controls expand on our Rebooted form styles with classes. Use these classes to opt into their customized displays 
                            for a more consistent rendering across browsers and devices.
                        </p>
                    </td>
                    <td style="text-align: center;">
                        <img src="{{ $qrcode->qrcode_url }}" alt="">
                    </td>
                </tr>
            </table>
        </div>
        <div class="down">
            <h4>候選人</h4>
            <div style="padding: 0 20px;">
                <table class="table" style="font-size: 1.3rem;">
                    <tbody>
                        @foreach ($candidates as $key => $cand)
                        <tr style="height: 80px;">
                            <th width="15%"></th>
                            <th width="15%" style="text-align: center;">{{ ($key + 1) }}.</th>
                            <th>
                                <div>
                                    <span>{{ $cand->school }}</span>
                                    <span>{{ $cand->name }}</span>
                                </div>
                            </th>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endforeach
</body>
</html>