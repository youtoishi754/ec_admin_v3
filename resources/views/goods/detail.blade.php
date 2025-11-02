@extends('layouts.parents')
@section('title', '商品情報一覧-詳細')
@section('content')
  <div class="container">
    <nav aria-label="パンくずリスト">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">商品情報一覧</li>
        <li class="breadcrumb-item active" aria-current="page">詳細</li>
      </ol>
    </nav>
    {{-- 見出し --}}
    <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;">詳細</h3>
    {{-- 詳細 テーブル --}}
    <table class="table table-hover">
      <tr>
        <th width="120">商品番号</th>
        <td>{{ $goods_data->goods_number }}</td>
      </tr>
      <tr>
        <th width="120">商品名</th>
        <td>{{ $goods_data->goods_name }}</td>
      </tr>
      <tr>
        <th width="120">金額</th>
        <td>{{ number_format($goods_data->goods_price) }}円</td>
      </tr>
      <tr>
        <th width="120">個数</th>
        <td>{{ $goods_data->goods_stock }}個</td>
      </tr>
      <tr>
        <th width="120">紹介文</th>
      <td>{!! $goods_data->intro_txt !!}</td>
      <tr>
        <th width="120">状態</th>
        <td>@if( $goods_data->disp_flg == 0) 表示 @else 非表示 @endif</td>
      </tr>  
    </table> 
    <a href="{{route('index')}}">商品情報一覧へ戻る</a>
  </div>
@endsection
