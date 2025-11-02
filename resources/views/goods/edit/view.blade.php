@extends('layouts.parents')
@section('title', 'EC管理システム-編集登録-確認画面')
@section('content')

  <div class="container">

  <nav aria-label="パンくずリスト">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">商品情報一覧</li>
    <li class="breadcrumb-item">編集登録</li>
    <li class="breadcrumb-item active" aria-current="page">確認画面</li>
  </ol>
</nav>
{{-- 見出し --}}
<h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;">商品情報確認</h3>
 {{-- エラー表示 --}}
 @if(count($errors) > 0)
 <ul>
     @foreach ($errors->all() as $error)
         <li style="color:#FF0000;">{{ $error }}</li>
     @endforeach
 </ul>
@endif
{{-- 商品情報入力フォーム --}}
 <form action="{{route('goods_edit_do')}}" method="post">
 <input type="hidden" name="_token" value="{{csrf_token()}}">
 <input type="hidden" name="un_id" value="{{ request()->un_id }}">
 <input type="hidden" name="goods_number" value="{{ request()->goods_number }}">
 <input type="hidden" name="first_flg" value="1">
 <input type="hidden" name="goods_name" value="{{ request()->goods_name }}">
 <input type="hidden" name="goods_price" value="{{ request()->goods_price }}">
 <input type="hidden" name="goods_stock" value="{{ request()->goods_stock }}">
 <input type="hidden" name="intro_txt" value="{{ request()->intro_txt }}">
 <input type="hidden" name="disp_flg" value="{{ request()->disp_flg }}">
  <table class="table table-hover">
  <tr>
    <th>商品番号</th>
    <td>{{ request()->goods_number }}</td>
  </tr>
  <tr>
    <th>商品名</th>
    <td>{{ request()->goods_name }}</td>
  </tr>
  <tr>
    <th>金額</th>
    <td>{{ number_format(request()->goods_price) }}円</td>
  </tr>
  <tr>
    <th>個数</th>
    <td>{{ request()->goods_stock }}個</td>
  </tr>
  <tr>
    <th>紹介文</th>
    <td>{!! nl2br(e(request()->intro_txt))  !!}</td>
  </tr>
  <tr>
    <th>状態</th>
    <td>@if( request()->disp_flg == 0) 表示 @else 非表示 @endif</td>
  </tr>
</table> 
<div class="text-center">
<button type="submit" onClick="submitAction('{{route('goods_edit')}}','get')" class="d-inline-block">商品情報入力へ戻る</button> 
  <button type="submit" class="d-inline-block">登録を完了する</button> 
</div>
</form>
</div>

  @endsection
