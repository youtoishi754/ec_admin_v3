@extends('layouts.parents')
@section('title', '在庫棚卸')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">在庫棚卸一覧</h3>
  
  {{-- 統計ダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-boxes"></i> 総在庫アイテム数</h5>
          <h2>{{ number_format($stats['total_items']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-check-circle"></i> 本日の棚卸</h5>
          <h2>{{ number_format($stats['total_stocktaking_today']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-info">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-history"></i> 累計調整回数</h5>
          <h2>{{ number_format($stats['total_adjustments']) }}</h2>
        </div>
      </div>
    </div>
  </div>

  {{-- 棚卸実施ボタン --}}
  <div class="mb-3">
    <a class="btn btn-info" href="{{ route('inventory_stocktaking_history') }}">
      <i class="fas fa-history"></i> 調整履歴を見る
    </a>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('inventory_stocktaking')}}" method="GET">
      <table class="table table_border_radius">
        <thead>
          <tr>
            <th colspan="6">検索条件</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>商品番号</th>
            <td><input type="text" name="goods_number" value="{{ request()->goods_number }}" class="form-control"></td>
            <th>商品名</th>
            <td><input type="text" name="goods_name" value="{{ request()->goods_name }}" class="form-control"></td>
            <th>倉庫</th>
            <td>
              <select name="warehouse_id" class="form-control">
                <option value="">全て</option>
                @foreach($warehouses as $warehouse)
                  <option value="{{$warehouse->id}}" @if(request()->warehouse_id == $warehouse->id) selected @endif>
                    {{$warehouse->warehouse_name}}
                  </option>
                @endforeach
              </select>
            </td>
          </tr>
          <tr>
            <th>ロケーション</th>
            <td><input type="text" name="location_code" value="{{ request()->location_code }}" class="form-control"></td>
            <th>ロット番号</th>
            <td colspan="3"><input type="text" name="lot_number" value="{{ request()->lot_number }}" class="form-control"></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('inventory_stocktaking')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 棚卸一覧テーブル --}}
  @if(count($inventories) > 0)
  {{ $inventories->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="thead-light">
        <tr>
          <th>商品番号</th>
          <th>商品名</th>
          <th>倉庫</th>
          <th>ロケーション</th>
          <th>ロット番号</th>
          <th>シリアル番号</th>
          <th>帳簿在庫数</th>
          <th>引当済数</th>
          <th>有効在庫数</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($inventories as $inventory)
        <tr>
          <td>{{$inventory->goods_number}}</td>
          <td>{{$inventory->goods_name}}</td>
          <td>{{$inventory->warehouse_name}}</td>
          <td>{{$inventory->location_code ?? '-'}}</td>
          <td>{{$inventory->lot_number ?? '-'}}</td>
          <td>{{$inventory->serial_number ?? '-'}}</td>
          <td class="text-right">{{number_format($inventory->system_quantity)}}</td>
          <td class="text-right">{{number_format($inventory->reserved_quantity ?? 0)}}</td>
          <td class="text-right">{{number_format($inventory->system_quantity - ($inventory->reserved_quantity ?? 0))}}</td>
          <td>
            <a href="{{route('inventory_stocktaking_create', ['inventory_id' => $inventory->id])}}" class="btn btn-sm btn-primary">
              <i class="fas fa-clipboard-check"></i> 棚卸実施
            </a>
          </td>
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
