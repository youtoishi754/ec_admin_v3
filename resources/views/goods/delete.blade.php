@extends('layouts.parents')
@section('title', 'EC管理システム-削除確認')
@section('content')
  <div class="container">
    <nav aria-label="パンくずリスト">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">商品情報一覧</li>
        <li class="breadcrumb-item active" aria-current="page">削除確認</li>
      </ol>
    </nav>
    {{-- 見出し --}}
    <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;">商品情報確認</h3>
    {{-- 商品情報確認フォーム --}}
    <form action="{{route('goods_delete_do')}}" method="post">
      <input type="hidden" name="_token" value="{{csrf_token()}}">
      <input type="hidden" name="un_id" value="{{ $goods_data->un_id }}">
      <table class="table table-hover">
        <tr>
          <th>商品番号</th>
          <td>{{ $goods_data->goods_number }}</td>
        </tr>
        <tr>
          <th>商品名</th>
          <td>{{ $goods_data->goods_name }}</td>
        </tr>
        <tr>
          <th>金額</th>
          <td>{{ number_format($goods_data->goods_price) }}円</td>
        </tr>
        <tr>
          <th>個数</th>
          <td>{{ $goods_data->goods_stock }}個</td>
        </tr>
        <tr>
          <th>紹介文</th>
          <td>{!! nl2br(e($goods_data->intro_txt))  !!}</td>
        </tr>
        <tr>
          <th>状態</th>
          <td>@if( $goods_data->disp_flg == 0) 表示 @else 非表示 @endif</td>
        </tr>
      </table> 
      <div class="text-center">
        <button type="submit" class="d-inline-block">登録を削除する</button> 
      </div>
    </form>
  </div>
@endsection
