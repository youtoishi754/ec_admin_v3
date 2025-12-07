@extends('layouts.parents')
@section('title', '商品情報一覧-詳細')
@section('content')
  <div class="container">
    <nav aria-label="パンくずリスト">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('index')}}">商品情報一覧</a></li>
        <li class="breadcrumb-item active" aria-current="page">詳細</li>
      </ol>
    </nav>

    {{-- 見出し --}}
    <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;">
      <i class="fas fa-box"></i> 商品詳細情報
    </h3>

    {{-- 成功メッセージ --}}
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    {{-- エラーメッセージ --}}
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    @endif

    {{-- 詳細 テーブル --}}
    <table class="table table-hover table-bordered">
      <tr>
        <th width="180" class="table-active">商品番号</th>
        <td><strong>{{ $goods_data->goods_number }}</strong></td>
      </tr>
      <tr>
        <th class="table-active">商品名</th>
        <td><span class="h5">{{ $goods_data->goods_name }}</span></td>
      </tr>
      @if($goods_data->image_path)
      <tr>
        <th class="table-active">商品画像</th>
        <td>
          <div class="product-image-container">
            <img src="{{ asset($goods_data->image_path) }}" alt="{{ $goods_data->goods_name }}" class="img-thumbnail" style="max-width: 400px; max-height: 400px; object-fit: contain;">
          </div>
          <small class="text-muted d-block mt-2">
            <i class="fas fa-info-circle"></i> 画像パス: {{ $goods_data->image_path }}
          </small>
        </td>
      </tr>
      @endif
      <tr>
        <th class="table-active">金額</th>
        <td>
          <span class="h4 text-primary">{{ number_format($goods_data->goods_price) }}円</span>
          <small class="text-muted">(税率: {{ $goods_data->tax_rate }}%)</small>
        </td>
      </tr>
      <tr>
        <th class="table-active">在庫数</th>
        <td>
          <span class="badge badge-{{ $goods_data->goods_stock > 0 ? 'success' : 'danger' }} badge-lg" style="font-size: 1.2em; padding: 8px 16px;">
            {{ number_format($goods_data->goods_stock) }}個
          </span>
          @if($goods_data->goods_stock == 0)
            <span class="text-danger ml-2"><i class="fas fa-exclamation-triangle"></i> 在庫切れ</span>
          @elseif($goods_data->goods_stock <= 10)
            <span class="text-warning ml-2"><i class="fas fa-exclamation-circle"></i> 在庫少</span>
          @endif
        </td>
      </tr>
      @if($goods_data->category_id)
      <tr>
        <th class="table-active">カテゴリID</th>
        <td>{{ $goods_data->category_id }}</td>
      </tr>
      @endif
      <tr>
        <th class="table-active">紹介文</th>
        <td style="white-space: pre-wrap; word-wrap: break-word; word-break: break-all;">{!! nl2br(e($goods_data->intro_txt)) !!}</td>
      </tr>
      @if($goods_data->goods_detail)
      <tr>
        <th class="table-active">商品詳細</th>
        <td>{!! nl2br(e($goods_data->goods_detail)) !!}</td>
      </tr>
      @endif
      <tr>
        <th class="table-active">表示状態</th>
        <td>
          @if($goods_data->disp_flg == 1) 
            <span class="badge badge-success"><i class="fas fa-eye"></i> 表示中</span>
          @else 
            <span class="badge badge-secondary"><i class="fas fa-eye-slash"></i> 非表示</span>
          @endif
        </td>
      </tr>
      @if($goods_data->sales_start_at || $goods_data->sales_end_at)
      <tr>
        <th class="table-active">販売期間</th>
        <td>
          @if($goods_data->sales_start_at)
            開始: {{ date('Y年m月d日 H:i', strtotime($goods_data->sales_start_at)) }}
          @endif
          @if($goods_data->sales_start_at && $goods_data->sales_end_at)
            <br>
          @endif
          @if($goods_data->sales_end_at)
            終了: {{ date('Y年m月d日 H:i', strtotime($goods_data->sales_end_at)) }}
          @endif
        </td>
      </tr>
      @endif
      <tr>
        <th class="table-active">登録日時</th>
        <td>{{ date('Y年m月d日 H:i:s', strtotime($goods_data->ins_date)) }}</td>
      </tr>
      <tr>
        <th class="table-active">更新日時</th>
        <td>{{ date('Y年m月d日 H:i:s', strtotime($goods_data->up_date)) }}</td>
      </tr>
    </table>

    {{-- アクションボタン --}}
    <div class="mt-4 mb-4">
      <a class="btn btn-secondary" href="{{route('index')}}">
        <i class="fas fa-arrow-left"></i> 商品情報一覧へ戻る
      </a>
      <a class="btn btn-info ml-2" href="{{route('goods_edit')}}?un_id={{$goods_data->un_id}}">
        <i class="fas fa-edit"></i> 編集
      </a>
      <a class="btn btn-danger ml-2" href="{{route('goods_delete')}}?un_id={{$goods_data->un_id}}">
        <i class="fas fa-trash"></i> 削除
      </a>
    </div>
  </div>
@endsection
