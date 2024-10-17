@extends('front.index')

@section('body')
<h2 class="title">
    <img src="{{ asset('assets/header.jpg') }}" height="10%" width="10%">
    <span>桃園區域網路中心</span>
</h2>

<div class="title_background mb-3" style="background-image: url({{ asset('assets/back4.jpeg') }}); ">
    <h1 class="vote_name fw-bold text-center" >TANet桃園區網路中心第六屆傑出網路管理人員選拔(桃園區網第74次管理會議)</h1>
</div>

<div class="container">
    <div class="notice mb-3">
        <h4 class="fw-bold">候選人簡報（人員名單依收件先後排序）</h4>
        <p>1. <a href="{{ asset('assets/1_啟英高中_李栢松.pdf') }}" target="_blank">啟英高中 李栢松 組長</a></p>
        <p>2. <a href="{{ asset('assets/2_連江縣教網中心_吳貽樺.pdf') }}" target="_blank">連江縣教網中心 吳貽樺 組員</a></p>
        <p>3. <a href="{{ asset('assets/3_長庚大學_吳凱威.pdf') }}" target="_blank">長庚大學 吳凱威 專員</a></p>
        <p>4. <a href="{{ asset('assets/4_國防大學_江彥廷.pdf') }}" target="_blank">國防大學 江彥廷 中尉系統管制官</a></p>
        <p>5. <a href="{{ asset('assets/5_育達高中_張以勤V2.pdf') }}" target="_blank">育達高中 張以勤 組長</a></p>
    </div>
</div>
@endsection
