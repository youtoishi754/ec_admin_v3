@extends('layouts.parents')
@section('title', '有効期限管理')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">有効期限管理</h3>

  {{-- 統計カード --}}
  <div class="row mb-3">
    <div class="col-md-3">
      <div class="card text-white bg-danger">
        <div class="card-body">
          <h5 class="card-title">期限切れ</h5>
          <h2>{{$stats['expired']}}</h2>
          <p class="card-text">アイテム</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h5 class="card-title">7日以内</h5>
          <h2>{{$stats['critical_7days']}}</h2>
          <p class="card-text">アイテム</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-info">
        <div class="card-body">
          <h5 class="card-title">30日以内</h5>
          <h2>{{$stats['warning_30days']}}</h2>
          <p class="card-text">アイテム</p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h5 class="card-title">有効期限付き商品</h5>
          <h2>{{$stats['total_with_expiry']}}</h2>
          <p class="card-text">アイテム</p>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('inventory_expiry')}}" method="GET">
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
            <th>有効期限状態</th>
            <td>
              <select name="expiry_status" class="form-control">
                <option value="">全て</option>
                <option value="expired" @if(request()->expiry_status == "expired") selected @endif>期限切れ</option>
                <option value="critical" @if(request()->expiry_status == "critical") selected @endif>7日以内</option>
                <option value="warning" @if(request()->expiry_status == "warning") selected @endif>8-30日</option>
                <option value="normal" @if(request()->expiry_status == "normal") selected @endif>30日超</option>
              </select>
            </td>
          </tr>
          <tr>
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
            <th>ロット番号</th>
            <td><input type="text" name="lot_number" value="{{ request()->lot_number }}" class="form-control"></td>
            <th></th>
            <td></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('inventory_expiry')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 有効期限一覧テーブル --}}
  @if(count($inventories) > 0)
  {{ $inventories->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="thead-light">
        <tr>
          <th>商品番号</th>
          <th>商品名</th>
          <th>ロット番号</th>
          <th>シリアル番号</th>
          <th>倉庫</th>
          <th>ロケーション</th>
          <th>有効期限</th>
          <th>残日数</th>
          <th>在庫数</th>
          <th>状態</th>
        </tr>
      </thead>
      <tbody>
        @foreach($inventories as $item)
        <tr @if($item->days_until_expiry < 0) class="table-danger" @elseif($item->days_until_expiry <= 7) class="table-warning" @endif>
          <td>{{$item->goods_number}}</td>
          <td>{{$item->goods_name}}</td>
          <td>{{$item->lot_number}}</td>
          <td>{{$item->serial_number ?? '-'}}</td>
          <td>{{$item->warehouse_name}}</td>
          <td>{{$item->location_code}}</td>
          <td>
            @if($item->days_until_expiry < 0)
              <span class="text-danger font-weight-bold">{{\Carbon\Carbon::parse($item->expiry_date)->format('Y/m/d')}}</span>
            @elseif($item->days_until_expiry <= 7)
              <span class="text-danger">{{\Carbon\Carbon::parse($item->expiry_date)->format('Y/m/d')}}</span>
            @elseif($item->days_until_expiry <= 30)
              <span class="text-warning">{{\Carbon\Carbon::parse($item->expiry_date)->format('Y/m/d')}}</span>
            @else
              {{\Carbon\Carbon::parse($item->expiry_date)->format('Y/m/d')}}
            @endif
          </td>
          <td class="text-right">
            @if($item->days_until_expiry < 0)
              <span class="text-danger font-weight-bold">-{{abs($item->days_until_expiry)}}日</span>
            @elseif($item->days_until_expiry <= 7)
              <span class="text-danger font-weight-bold">{{$item->days_until_expiry}}日</span>
            @elseif($item->days_until_expiry <= 30)
              <span class="text-warning">{{$item->days_until_expiry}}日</span>
            @else
              {{$item->days_until_expiry}}日
            @endif
          </td>
          <td class="text-right">{{number_format($item->quantity)}}</td>
          <td>
            @if($item->days_until_expiry < 0)
              <span class="badge badge-danger">期限切れ</span>
            @elseif($item->days_until_expiry <= 7)
              <span class="badge badge-danger">緊急</span>
            @elseif($item->days_until_expiry <= 30)
              <span class="badge badge-warning">注意</span>
            @else
              <span class="badge badge-success">正常</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $inventories->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 有効期限データが見つかりませんでした。
  </div>
  @endif
</div>

@endsection
