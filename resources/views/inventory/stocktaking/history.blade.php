@extends('layouts.parents')
@section('title', '在庫調整履歴')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">在庫調整履歴</h3>
  
  {{-- 戻るボタン --}}
  <div class="mb-3">
    <a class="btn btn-secondary" href="{{ route('inventory_stocktaking') }}">
      <i class="fas fa-arrow-left"></i> 棚卸一覧に戻る
    </a>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('inventory_stocktaking_history')}}" method="GET">
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
            <th>調整日(開始)</th>
            <td><input type="date" name="date_from" value="{{ request()->date_from }}" class="form-control"></td>
          </tr>
          <tr>
            <th>ロット番号</th>
            <td><input type="text" name="lot_number" value="{{ request()->lot_number }}" class="form-control"></td>
            <th>調整担当者</th>
            <td><input type="text" name="performed_by" value="{{ request()->performed_by }}" class="form-control"></td>
            <th>調整日(終了)</th>
            <td><input type="date" name="date_to" value="{{ request()->date_to }}" class="form-control"></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('inventory_stocktaking_history')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 調整履歴テーブル --}}
  @if(count($history) > 0)
  {{ $history->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="thead-light">
        <tr>
          <th>調整日時</th>
          <th>商品番号</th>
          <th>商品名</th>
          <th>倉庫</th>
          <th>ロケーション</th>
          <th>ロット番号</th>
          <th>調整前数量</th>
          <th>調整数量</th>
          <th>調整後数量</th>
          <th>調整担当者</th>
          <th>備考</th>
        </tr>
      </thead>
      <tbody>
        @foreach($history as $movement)
        @php
          $quantityChange = $movement->quantity;
          $quantityBefore = $movement->before_quantity ?? '-';
          $quantityAfter = $movement->after_quantity ?? '-';
        @endphp
        <tr>
          <td>{{\Carbon\Carbon::parse($movement->movement_date)->format('Y/m/d H:i:s')}}</td>
          <td>{{$movement->goods_number}}</td>
          <td>{{$movement->goods_name}}</td>
          <td>{{$movement->warehouse_name}}</td>
          <td>{{$movement->location_code}}</td>
          <td>{{$movement->lot_number ?? '-'}}</td>
          <td class="text-right">{{is_numeric($quantityBefore) ? number_format($quantityBefore) : $quantityBefore}}</td>
          <td class="text-right">
            @if($quantityChange > 0)
              <span class="text-success font-weight-bold">+{{number_format($quantityChange)}}</span>
            @elseif($quantityChange < 0)
              <span class="text-danger font-weight-bold">{{number_format($quantityChange)}}</span>
            @else
              <span class="text-muted">±0</span>
            @endif
          </td>
          <td class="text-right">{{is_numeric($quantityAfter) ? number_format($quantityAfter) : $quantityAfter}}</td>
          <td>{{$movement->performed_by ?? '-'}}</td>
          <td>
            <small>{{$movement->notes ?? '-'}}</small>
          </td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
        <tr class="bg-light font-weight-bold">
          <td colspan="7" class="text-right">合計調整数:</td>
          <td class="text-right">
            @php
              $totalAdjustment = $history->sum('quantity');
            @endphp
            @if($totalAdjustment > 0)
              <span class="text-success font-weight-bold">+{{number_format($totalAdjustment)}}</span>
            @elseif($totalAdjustment < 0)
              <span class="text-danger font-weight-bold">{{number_format($totalAdjustment)}}</span>
            @else
              <span class="text-muted">±0</span>
            @endif
          </td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
    </table>
  </div>

  {{ $history->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 調整履歴データが見つかりませんでした。
  </div>
  @endif
</div>

@endsection
