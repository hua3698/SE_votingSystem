<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $voteEvent->event_name }}_QRcode</title>
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
            word-break: break-all
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
            min-height: 250px;
            border-bottom: 1px solid;
            padding: 1rem;
        }

        .down {
            padding: 1rem;
        }
    </style>
</head>
<body>
    @foreach ($qrcodes as $qrcode)
    <div class="page-break">
        <h1 class="text-center">{{ $voteEvent->event_name }}</h1>
        <div class="top">
            <table>
                <tr>
                    <td width="70%" style="vertical-align: baseline;">
                        <h3>注意事項</h3>
                        <p>１、每票最多圈選 <span class="text-danger fw-bold">{{ $voteEvent->max_vote_count }}</span> 人</p>
                        <p>２、自2024年首次採取線上投票，請掃描QR code進行投票。重複執行掃瞄將顯示前次投票的結果。</p>
                        <p>３、若遇網路或系統問題無法進行電子投票，將由主席宣布後，改採紙本投票。本張視同紙本選票，每所學校限領一張，圈選後請對折放入投票箱。</p>
                    </td>
                    <td width="30%" style="text-align: center;">
                        <img src="{{ $qrcode->qrcode_url }}" alt="">
                        <p style="margin: 0">{{ config('app.url') }}vote/{{ $voteEvent->event_id }}/{{ $qrcode->qrcode_string }}</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="down">
            <h3>候選人</h3>
            <div style="padding: 0 20px;">
                <table class="table" style="font-size: 1.5rem;">
                    <tbody>
                        @foreach ($candidates as $cand)
                        <tr style="height: 90px;">
                            <th width="15%"></th>
                            <th width="15%" style="text-align: center;">{{ $cand['number'] }}.</th>
                            <th>
                                <div>
                                    <span>{{ $cand->school }}</span>
                                    <span><strong>{{ $cand->name }}</strong></span>
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