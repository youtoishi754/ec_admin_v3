@extends('layouts.parents')
@section('title', 'シリアル番号一覧')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">シリアル番号一覧</h3>
  
  {{-- 戻るボタン --}}
  <div class="mb-3">
    <a class="btn btn-secondary" href="{{ route('inventory_lot') }}">
      <i class="fas fa-arrow-left"></i> ロット一覧に戻る
    </a>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('inventory_serial')}}" method="GET">
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
            <th>ロット番号</th>
            <td><input type="text" name="lot_number" value="{{ request()->lot_number }}" class="form-control"></td>
            <th>シリアル番号</th>
            <td><input type="text" name="serial_number" value="{{ request()->serial_number }}" class="form-control"></td>
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
            <th>状態</th>
            <td>
              <select name="status" class="form-control">
                <option value="">全て</option>
                <option value="in_stock" @if(request()->status == "in_stock") selected @endif>在庫中</option>
                <option value="shipped" @if(request()->status == "shipped") selected @endif>出荷済</option>
                <option value="returned" @if(request()->status == "returned") selected @endif>返品</option>
                <option value="defective" @if(request()->status == "defective") selected @endif>不良品</option>
              </select>
            </td>
            <th></th>
            <td></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('inventory_serial')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- シリアル番号一覧テーブル --}}
  @if(count($serials) > 0)
  {{ $serials->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

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
          <th>状態</th>
          <th>最終更新日</th>
        </tr>
      </thead>
      <tbody>
        @foreach($serials as $serial)
        <tr>
          <td>{{$serial->goods_number}}</td>
          <td>{{$serial->goods_name}}</td>
          <td>{{$serial->lot_number}}</td>
          <td><strong class="text-primary">{{$serial->serial_number}}</strong></td>
          <td>{{$serial->warehouse_name}}</td>
          <td>{{$serial->location_code}}</td>
          <td>
            @if($serial->expiry_date)
              @php
                $expiryDate = \Carbon\Carbon::parse($serial->expiry_date);
                $daysUntilExpiry = now()->diffInDays($expiryDate, false);
              @endphp
              @if($daysUntilExpiry < 0)
                <span class="text-danger font-weight-bold">{{$expiryDate->format('Y/m/d')}}</span>
              @elseif($daysUntilExpiry <= 7)
                <span class="text-danger">{{$expiryDate->format('Y/m/d')}}</span>
              @elseif($daysUntilExpiry <= 30)
                <span class="text-warning">{{$expiryDate->format('Y/m/d')}}</span>
              @else
                {{$expiryDate->format('Y/m/d')}}
              @endif
            @else
              -
            @endif
          </td>
          <td>
            @if($serial->status == 'in_stock')
              <span class="badge badge-success">在庫中</span>
            @elseif($serial->status == 'shipped')
              <span class="badge badge-info">出荷済</span>
            @elseif($serial->status == 'returned')
              <span class="badge badge-warning">返品</span>
            @else
              <span class="badge badge-danger">不良品</span>
            @endif
          </td>
          <td>{{$serial->updated_at ? \Carbon\Carbon::parse($serial->updated_at)->format('Y/m/d H:i') : '-'}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $serials->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> シリアル番号データが見つかりませんでした。
  </div>
  @endif
</div>

@endsection
