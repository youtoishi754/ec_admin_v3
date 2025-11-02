@extends('layouts.parents')
@section('title', 'EC管理システム-新規登録')
@section('content')
<div class="container">
  <nav aria-label="パンくずリスト">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">商品情報一覧</li>
      <li class="breadcrumb-item active" aria-current="page">新規登録</li>
    </ol>
  </nav>
  {{-- 見出し --}}
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;">商品情報入力</h3>
    {{-- エラー表示 --}}
    @if(count($errors) > 0)
      <ul>
        @foreach ($errors->all() as $error)
            <li style="color:#FF0000;">{{ $error }}</li>
        @endforeach
      </ul>
    @endif
  {{-- 商品情報入力フォーム --}}
  <form action="{{route('goods_add_view')}}" method="post" class="goods-form">
    <input type="hidden" name="_token" value="{{csrf_token()}}"> 
    <table class="table table-hover">
      <tr>
        <th>商品番号</th>
        <td><input type="text" name="goods_number" value="{{ request()->goods_number }}"></td>
      </tr>
      <tr>
        <th>商品名</th>
        <td><input type="text" name="goods_name" value="{{ request()->goods_name }}"></td>
      </tr>
      <tr>
        <th>金額</th>
        <td><input type="text" name="goods_price" value="{{ request()->goods_price }}"></td>
      </tr>
      <tr>
        <th>個数</th>
        <td><input type="text" name="goods_stock" value="{{ request()->goods_stock }}"></td>
      </tr>
      <tr>
        <th>紹介文</th>
      <td><textarea class="form-control" name="intro_txt" rows="8">{!! nl2br(e(request()->intro_txt)) !!}</textarea>
      </tr>
      <tr>
        <th>表示</th>
        <td>
          <input type="radio" name="disp_flg" value="0" id="true_flg" @if(request()->disp_flg == 0) checked=checked @endif><label for="true_flg">表示</label>
          <input type="radio" name="disp_flg" value="1" id="false_flg" @if(request()->disp_flg == 1) checked=checked @endif><label for="false_flg">非表示</label>
        </td>
      </tr>
    </table> 
    <div class="text-center">
        <button type="submit">確認画面へ</button> 
    </div>
  </form>
</div>
@endsection
