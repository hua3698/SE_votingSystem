@extends('front.index')

@section('body')
<h2 class="title">
    <img src="{{ asset('assets/ncu.avif') }}" height="10%" width="10%">
    <span>軟體工程第十一組 - 投票管理系統</span>
</h2>

<div class="title_background mb-3" style="background-image: url({{ asset('assets/back4.jpeg') }}); ">
    <h1 class="vote_name fw-bold text-center" >國立泱泱大學第九屆校長遴選</h1>
</div>

<div class="container">
    <div class="notice mb-3">
        <h4 class="fw-bold">候選人簡報（人員名單依收件先後排序）</h4>
    </div>
</div>
@endsection
