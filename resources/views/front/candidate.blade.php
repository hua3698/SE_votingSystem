@extends('front.common')

@section('body')
<style>
    
    .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0;
            min-height: calc(100vh - 150px);
        }

        .notice h4 {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .candidate-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 20px; /* 每個候選人之間的間距 */
        }

        .candidate-card {
            flex: 1 1 calc(33.33% - 20px); /* 每個卡片佔三分之一的寬度，並減去間距 */
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            background-color: #cbd9bb;
            text-align: left;
            transition: transform 0.3s ease;
        }

        .candidate-card:hover {
            transform: scale(1.05); /* 當鼠標懸停時，放大卡片 */
        }

        .candidate-card img {
            width: 200px; /* 固定圖片寬度為 200px */
            height: 200px; /* 固定圖片高度為 200px，形成正方形 */
            object-fit: cover; /* 確保圖片充滿容器，比例不變 */
            border-radius: 8px;
            margin-bottom: 15px;
            display: block; /* 確保圖片在容器中水平置中 */
            margin-left: auto; /* 讓圖片置中 */
            margin-right: auto; /* 讓圖片置中 */
        }

        .candidate-name {
            font-size: 1.25rem;
            margin-bottom: 10px;
            font-weight: bold;
            text-align: center;
        }

        .info-box {
            border: 2px solid #ffffff; /* 設置框線 */
            padding: 20px;
            margin-bottom: 10px;
            border-radius: 10px;
            background-color: #CDEAC0;
        }

        .candidate-card p {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #415c31;
        }

        @media (max-width: 768px) {
            .candidate-card {
                flex: 1 1 calc(50% - 20px); /* 平板設備時每行顯示兩個候選人 */
            }
        }

        @media (max-width: 480px) {
            .candidate-card {
                flex: 1 1 100%; /* 手機設備時每行顯示一個候選人 */
            }
        }
</style>
    <div class="container">
        <div class="notice mb-3">
            <h4 class="fw-bold">候選人介紹</h4>
        </div>

        <div class="candidate-wrapper">
            @foreach ($candidates as $key => $candidate)
            <div class="candidate-card">
                <img src="{{ asset('assets/' . $key. '.jpg') }}" alt="候選人 {{ $candidate->cand_id }}" class="img-fluid mb-3">
                <h5 class="candidate-name">{{ $candidate->name }}</h5>
                <div class="info-box">
                    <p><strong>特長：</strong>{{ $candidate->specialty ?? '無資料' }}</p>         
                    <p><strong>政見發表：</strong>{{ $candidate->manifesto ?? '無資料' }}</p>               
                    <p><strong>過去成就：</strong>{{ $candidate->achievements ?? '無資料' }}</p>
                    <p><strong>競選口號：「{{ $candidate->slogan ?? '無資料' }}」</strong></p>
                </div>
            </div>
            @endforeach
            
        </div>
    </div>
@endsection
