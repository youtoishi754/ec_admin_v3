@extends('layouts.parents')
@section('title', 'ロット管理')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">ロット一覧</h3>
  
  {{-- 統計ダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-barcode"></i> 総ロット数</h5>
          <h2>{{ number_format($stats['total_lots']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-calendar-times"></i> 期限接近</h5>
          <h2>{{ number_format($stats['expiring_soon']) }}</h2>
          <small>30日以内</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-danger">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-exclamation-circle"></i> 期限切れ</h5>
          <h2>{{ number_format($stats['expired']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-info">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-hashtag"></i> シリアル管理</h5>
          <a href="{{ route('inventory_serial') }}" class="btn btn-light btn-sm mt-2">
            <i class="fas fa-arrow-right"></i> シリアル一覧へ
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('inventory_lot')}}" method="GET">
      <table class="table table_border_radius">
        <thead>
          <tr>
            <th colspan="4">検索条件</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>商品番号</th>
            <td><input type="text" name="goods_number" value="{{ request()->goods_number }}" class="form-control"></td>
            <th>ロット番号</th>
            <td><input type="text" name="lot_number" value="{{ request()->lot_number }}" class="form-control"></td>
          </tr>
          <tr>
            <th>有効期限アラート</th>
            <td colspan="3">
              <select name="expiry_alert" class="form-control">
                <option value="">全て</option>
                <option value="7" @if(request()->expiry_alert == "7") selected @endif>7日以内</option>
                <option value="30" @if(request()->expiry_alert == "30") selected @endif>30日以内</option>
                <option value="90" @if(request()->expiry_alert == "90") selected @endif>90日以内</option>
              </select>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('inventory_lot')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- ロット一覧テーブル --}}
  @if(count($lots) > 0)
  {{ $lots->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <table class="table table-hover table-bordered">
      <thead class="thead-light">
        <tr>
          <th>商品番号</th>
          <th>商品名</th>
          <th>カテゴリ</th>
          <th>ロット番号</th>
          <th>製造日</th>
          <th>有効期限</th>
          <th>在庫数量</th>
          <th>利用可能数</th>
        </tr>
      </thead>
      <tbody>
        @foreach($lots as $lot)
        <tr @if($lot->expiry_date && $lot->expiry_date < now()) class="table-danger" @endif>
          <td>{{$lot->goods_number}}</td>
          <td>{{$lot->goods_name}}</td>
          <td>{{$lot->category_name ?? '-'}}</td>
          <td><strong>{{$lot->lot_number}}</strong></td>
          <td>{{$lot->manufacturing_date ? \Carbon\Carbon::parse($lot->manufacturing_date)->format('Y/m/d') : '-'}}</td>
          <td>
            @if($lot->expiry_date)
              @php
                $expiryDate = \Carbon\Carbon::parse($lot->expiry_date);
                $daysUntilExpiry = now()->diffInDays($expiryDate, false);
              @endphp
              @if($daysUntilExpiry < 0)
                <span class="text-danger font-weight-bold">{{$expiryDate->format('Y/m/d')}} (期限切れ)</span>
              @elseif($daysUntilExpiry <= 7)
                <span class="text-danger">{{$expiryDate->format('Y/m/d')}} (残り{{$daysUntilExpiry}}日)</span>
              @elseif($daysUntilExpiry <= 30)
                <span class="text-warning">{{$expiryDate->format('Y/m/d')}} (残り{{$daysUntilExpiry}}日)</span>
              @else
                {{$expiryDate->format('Y/m/d')}} (残り{{$daysUntilExpiry}}日)
              @endif
            @else
              -
            @endif
          </td>
          <td class="text-right">
            <span class="badge badge-success">{{number_format($lot->total_quantity)}}</span>
          </td>
          <td class="text-right">
            <span class="badge badge-info">{{number_format($lot->available_quantity)}}</span>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

  {{ $lots->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> ロットデータが見つかりませんでした。
  </div>
  @endif
</div>

@endsection
