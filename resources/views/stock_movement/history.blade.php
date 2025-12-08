@extends('layouts.parents')
@section('title', '入出庫履歴')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">入出庫履歴</h3>
  
  {{-- 統計ダッシュボード（過去30日間） --}}
  <div class="row mb-4">
    <div class="col-md-2">
      <div class="card text-white bg-primary">
        <div class="card-body text-center p-2">
          <h6 class="card-title mb-1"><i class="fas fa-arrow-down"></i> 入庫</h6>
          <h4 class="mb-0">{{ number_format($stats['total_in']) }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-white bg-danger">
        <div class="card-body text-center p-2">
          <h6 class="card-title mb-1"><i class="fas fa-arrow-up"></i> 出庫</h6>
          <h4 class="mb-0">{{ number_format($stats['total_out']) }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-white bg-info">
        <div class="card-body text-center p-2">
          <h6 class="card-title mb-1"><i class="fas fa-random"></i> 移動</h6>
          <h4 class="mb-0">{{ number_format($stats['total_transfer']) }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-white bg-warning">
        <div class="card-body text-center p-2">
          <h6 class="card-title mb-1"><i class="fas fa-undo"></i> 返品</h6>
          <h4 class="mb-0">{{ number_format($stats['total_return']) }}</h4>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-white bg-secondary">
        <div class="card-body text-center p-2">
          <h6 class="card-title mb-1"><i class="fas fa-edit"></i> 調整</h6>
          <h4 class="mb-0">{{ number_format($stats['total_adjust']) }}</h4>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('stock_movement_history')}}" method="GET">
      <table class="table table_border_radius">
        <thead>
          <tr><th colspan="6">検索条件</th></tr>
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
            <th>入出庫区分</th>
            <td>
              <select name="movement_type" class="form-control">
                <option value="">全て</option>
                <option value="in" @if(request()->movement_type == "in") selected @endif>入庫</option>
                <option value="out" @if(request()->movement_type == "out") selected @endif>出庫</option>
                <option value="transfer" @if(request()->movement_type == "transfer") selected @endif>移動</option>
                <option value="return" @if(request()->movement_type == "return") selected @endif>返品</option>
                <option value="adjust" @if(request()->movement_type == "adjust") selected @endif>調整</option>
              </select>
            </td>
            <th>日付（開始）</th>
            <td><input type="date" name="start_date" value="{{ request()->start_date }}" class="form-control"></td>
            <th>日付（終了）</th>
            <td><input type="date" name="end_date" value="{{ request()->end_date }}" class="form-control"></td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('stock_movement_history')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 履歴一覧テーブル --}}
  @if(count($history) > 0)
  {{ $history->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <table class="table table-hover table-bordered">
    <thead class="thead-light">
      <tr>
        <th>日時</th>
        <th>区分</th>
        <th>商品番号</th>
        <th>商品名</th>
        <th>倉庫</th>
        <th>ロケーション</th>
        <th>数量</th>
        <th>変更前</th>
        <th>変更後</th>
        <th>備考</th>
      </tr>
    </thead>
    <tbody>
      @foreach($history as $record)
      <tr>
        <td>{{ \Carbon\Carbon::parse($record->movement_date)->format('Y/m/d H:i') }}</td>
        <td>
          @if($record->movement_type == 'in')
            <span class="badge badge-primary"><i class="fas fa-arrow-down"></i> 入庫</span>
          @elseif($record->movement_type == 'out')
            <span class="badge badge-danger"><i class="fas fa-arrow-up"></i> 出庫</span>
          @elseif($record->movement_type == 'transfer')
            <span class="badge badge-info"><i class="fas fa-random"></i> 移動</span>
          @elseif($record->movement_type == 'return')
            <span class="badge badge-warning"><i class="fas fa-undo"></i> 返品</span>
          @elseif($record->movement_type == 'adjust')
            <span class="badge badge-secondary"><i class="fas fa-edit"></i> 調整</span>
          @else
            <span class="badge badge-light">{{ $record->movement_type }}</span>
          @endif
        </td>
        <td>{{$record->goods_number}}</td>
        <td>{{$record->goods_name}}</td>
        <td>{{$record->warehouse_code}}<br><small>{{$record->warehouse_name}}</small></td>
        <td>{{ $record->location_code ? $record->location_code : '-' }}</td>
        <td class="text-right">
          @if($record->quantity >= 0)
            <span class="text-primary">+{{number_format($record->quantity)}}</span>
          @else
            <span class="text-danger">{{number_format($record->quantity)}}</span>
          @endif
        </td>
        <td class="text-right">{{number_format($record->before_quantity)}}</td>
        <td class="text-right">{{number_format($record->after_quantity)}}</td>
        <td><small>{{$record->notes}}</small></td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{ $history->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 入出庫履歴が見つかりませんでした。
  </div>
  @endif
</div>

@endsection
