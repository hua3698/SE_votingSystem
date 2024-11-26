@extends('front.common')

@section('body')
<style>
    
    .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 0;
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

            <!-- 候選人 1 -->
            <div class="candidate-card">
                <img src="{{ asset('assets/chiikwa.jpg') }}" alt="候選人 1" class="img-fluid mb-3">
                <h5 class="candidate-name">吉伊卡哇（ちいかわ）</h5>
                <div class="info-box">
                    <p><strong>特長：</strong>耐心、友善且樂於助人</p>         
                    <p><strong>政見發表：</strong>關心每一位學生的幸福與安全，推動更和平的校園環境。</p>               
                    <p><strong>過去成就：</strong>幫助需要幫助的朋友渡過難關，必要時刻非常勇敢。</p>
                    <p><strong>競選口號：「小小的努力，改變世界！」</strong></p>
                </div>
            </div>

            <!-- 候選人 2 -->
            <div class="candidate-card">
                <img src="{{ asset('assets/cat.jpg') }}" alt="候選人 2" class="img-fluid mb-3">
                <h5 class="candidate-name">小八貓（ハチワレ）</h5>
                <div class="info-box">
                    <p><strong>特長：</strong>樂觀有創意，擅長解決困難問題</p> 
                    <p><strong>政見發表：</strong>強調學習與生活之間平衡，主張減少考試項目，增加學生自由的時間。</p>
                    <p><strong>過去成就：</strong>陪伴好友吉伊準備考試，永遠保持樂觀正向。</p>
                    <p><strong>競選口號：「一定會有辦法的！」</strong></p>
                </div>
            </div>

            <!-- 候選人 3 -->
            <div class="candidate-card">
                <img src="{{ asset('assets/usagi.jpg') }}" alt="候選人 3" class="img-fluid mb-3">
                <h5 class="candidate-name">烏薩奇（うさぎ）</h5>
                <div class="info-box">
                    <p><strong>特長：</strong>深藏不漏、除草證三級證照</p>
                    <p><strong>政見發表：</strong>校園美食多樣化，新增每日下午茶。</p>
                    <p><strong>過去成就：</strong>烏薩奇在每次活動中總能成為焦點，讓學生們感受到更多的活力。</p>
                    <p><strong>競選口號：「咿呀哈」</strong></p>
                </div>
            </div>

            <!-- 候選人 4 -->
            <div class="candidate-card">
                <img src="{{ asset('assets/momo.jpg') }}" alt="候選人 4" class="img-fluid mb-3">
                <h5 class="candidate-name">小桃（モモンガ）</h5>
                <div class="info-box">
                    <p><strong>特長：</strong>理直氣壯、裝可愛</p> 
                    <p><strong>政見發表：</strong>承諾讓每一位學校成員都能感受到被重視，創造一個彼此關注和誇獎的環境。</p>
                    <p><strong>過去成就：</strong>擅長利用可愛的外表和討人喜歡的行為來吸引關注，懂得在人際互動中展現自己獨特的魅力</p>
                    <p><strong>競選口號：</strong>「突嚕哩啦」</p>
                </div>
            </div>

            <!-- 候選人 5 -->
            <div class="candidate-card">
                <img src="{{ asset('assets/Liz.jpg') }}""alt="候選人 5" class="img-fluid mb-3">
                <h5 class="candidate-name">栗子饅頭（くりまんじゅう）</h5>
                <div class="info-box">
                    <p><strong>特長：</strong>照顧後輩、料理技術高超。</p>
                    <p><strong>政見發表：</strong>推廣學校美食文化，創立更多社交聚會。</p>
                    <p><strong>過去成就：</strong>曾舉辦多次美食交流會，受到好評，擁有很多喝酒的技巧，也時常烹飪食物配酒。多次宿醉，並有各種應對宿醉的方式。</p>
                    <p><strong>競選口號：</strong>「哈——」</p>
                </div>
            </div>

            <!-- 候選人 6 -->
            <div class="candidate-card">
                <img src="{{ asset('assets/lion.jpg') }}"" alt="候選人 6" class="img-fluid mb-3">
                <h5 class="candidate-name">獅薩（シーサー）</h5>
                <div class="info-box">
                    <p><strong>特長：</strong>聰明上進、拉麵店打工職人</p>
                    <p><strong>政見發表：</strong>開設料理拉麵專門課程，宣導沖繩美食與文化知識。</p>
                    <p><strong>過去成就：</strong>成功考取「超級打工」資格，成為郎拉麵店的學徒，會吃一些沖繩當地特色點心，性格很熱心腸，經常請朋友們品嘗美食。</p> 
                    <p><strong>競選口號：</strong>「一起吃拉麵吧」</p>
                </div>
            </div>

            
        </div>
    </div>
@endsection
