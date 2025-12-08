@extends('layouts.parents')
@section('title', 'リアルタイム在庫管理')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">リアルタイム在庫一覧</h3>
  
  {{-- 統計ダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-boxes"></i> 総アイテム数</h5>
          <h2>{{ number_format($stats['total_items']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-danger">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-exclamation-circle"></i> 欠品</h5>
          <h2>{{ number_format($stats['out_of_stock']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-exclamation-triangle"></i> 低在庫</h5>
          <h2>{{ number_format($stats['low_stock']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-cubes"></i> 総在庫数</h5>
          <h2>{{ number_format($stats['total_quantity']) }}</h2>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('inventory')}}">
      <table class="table table_border_radius">
        <thead>
          <tr>
            <th colspan="6">検索条件</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>商品番号</th>
            <td colspan="2"><input type="text" name="goods_number" value="{{ request()->goods_number }}" class="form-control"></td>
            <th>商品名</th>
            <td colspan="2"><input type="text" name="goods_name" value="{{ request()->goods_name }}" class="form-control"></td>
          </tr>
          <tr>
            <th>倉庫</th>
            <td colspan="2">
              <select name="warehouse_id" class="form-control">
                <option value="">全て</option>
                @foreach($warehouses as $warehouse)
                  <option value="{{$warehouse->id}}" @if(request()->warehouse_id == $warehouse->id) selected @endif>
                    {{$warehouse->warehouse_name}} ({{$warehouse->warehouse_code}})
                  </option>
                @endforeach
              </select>
            </td>
            <th>ロケーション</th>
            <td colspan="2">
              <select name="location_id" class="form-control">
                <option value="">全て</option>
                @foreach($locations as $location)
                  <option value="{{$location->id}}" @if(request()->location_id == $location->id) selected @endif>
                    {{$location->warehouse_name}} - {{$location->location_code}}
                  </option>
                @endforeach
              </select>
            </td>
          </tr>
          <tr>
            <th>ロット番号</th>
            <td colspan="2"><input type="text" name="lot_number" value="{{ request()->lot_number }}" class="form-control"></td>
            <th>在庫状態</th>
            <td colspan="2">
              <select name="stock_status" class="form-control">
                <option value="">全て</option>
                <option value="out_of_stock" @if(request()->stock_status == "out_of_stock") selected @endif>欠品</option>
                <option value="low_stock" @if(request()->stock_status == "low_stock") selected @endif>低在庫</option>
                <option value="normal" @if(request()->stock_status == "normal") selected @endif>正常</option>
              </select>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('inventory')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 在庫一覧テーブル --}}
  @if(count($inventories) > 0)
  {{ $inventories->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="thead-light">
        <tr>
          <th>商品画像</th>
          <th>商品番号</th>
          <th>商品名</th>
          <th>カテゴリ</th>
          <th>倉庫</th>
          <th>ロケーション</th>
          <th>ロット番号</th>
          <th>シリアル番号</th>
          <th>在庫数</th>
          <th>引当数</th>
          <th>利用可能数</th>
          <th>有効期限</th>
          <th>入荷日</th>
        </tr>
      </thead>
      <tbody>
        @foreach($inventories as $inventory)
        <tr>
          <td class="text-center">
            @if($inventory->image_path)
              <img src="{{ asset($inventory->image_path) }}" alt="商品画像" style="width: 50px; height: 50px; object-fit: cover;">
            @else
              <i class="fas fa-image fa-2x text-muted"></i>
            @endif
          </td>
          <td>{{$inventory->goods_number}}</td>
          <td>{{$inventory->goods_name}}</td>
          <td>{{$inventory->category_name ?? '-'}}</td>
          <td>{{$inventory->warehouse_name}}</td>
          <td>{{$inventory->location_code}}</td>
          <td>{{$inventory->lot_number ?? '-'}}</td>
          <td>{{$inventory->serial_number ?? '-'}}</td>
          <td class="text-right">
            <span class="badge badge-{{ $inventory->quantity > 0 ? 'success' : 'danger' }}">
              {{number_format($inventory->quantity)}}
            </span>
          </td>
          <td class="text-right">{{number_format($inventory->reserved_quantity)}}</td>
          <td class="text-right">
            @if($inventory->available_quantity <= 0)
              <span class="badge badge-danger">{{number_format($inventory->available_quantity)}}</span>
            @elseif($inventory->min_stock_level && $inventory->available_quantity <= $inventory->min_stock_level)
              <span class="badge badge-warning">{{number_format($inventory->available_quantity)}}</span>
            @else
              <span class="badge badge-success">{{number_format($inventory->available_quantity)}}</span>
            @endif
          </td>
          <td>
            @if($inventory->expiry_date)
              @php
                $days = \Carbon\Carbon::parse($inventory->expiry_date)->diffInDays(now(), false);
              @endphp
              @if($days >= 0)
                <span class="text-danger"><i class="fas fa-exclamation-circle"></i> 期限切れ</span>
              @elseif(abs($days) <= 7)
                <span class="text-danger">{{$inventory->expiry_date}}</span>
              @elseif(abs($days) <= 30)
                <span class="text-warning">{{$inventory->expiry_date}}</span>
              @else
                {{$inventory->expiry_date}}
              @endif
            @else
              -
            @endif
          </td>
          <td>{{$inventory->received_date ?? '-'}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $inventories->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 在庫データが見つかりませんでした。
  </div>
  @endif
</div>

@endsection
