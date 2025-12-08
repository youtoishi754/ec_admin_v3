@extends('layouts.parents')
@section('title', '在庫アラート管理')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">在庫アラート一覧</h3>
  
  {{-- 成功・エラーメッセージ --}}
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif

  {{-- 今すぐチェックボタン --}}
  <div class="mb-3">
    <form action="{{route('inventory_alert_check_now')}}" method="POST" style="display:inline;">
      @csrf
      <button type="submit" class="btn btn-info">
        <i class="fas fa-sync"></i> 在庫アラートを今すぐチェック
      </button>
    </form>
  </div>
  
  {{-- 統計ダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-danger">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-times-circle"></i> 欠品</h5>
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
      <div class="card text-white bg-info">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-calendar-times"></i> 期限警告</h5>
          <h2>{{ number_format($stats['expiry_warning'] + $stats['expiry_critical']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-secondary">
        <div class="card-body">
          <h5 class="card-title">合計</h5>
          <h2>{{ number_format($stats['total']) }}</h2>
          <small>過剰在庫: {{ number_format($stats['excess']) }}</small>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('inventory_alert')}}" method="GET">
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
            <th>アラート種別</th>
            <td>
              <select name="alert_type" class="form-control">
                <option value="">全て</option>
                <option value="low_stock" @if(request()->alert_type == "low_stock") selected @endif>低在庫</option>
                <option value="out_of_stock" @if(request()->alert_type == "out_of_stock") selected @endif>欠品</option>
                <option value="expiry_warning" @if(request()->alert_type == "expiry_warning") selected @endif>期限警告</option>
                <option value="expiry_critical" @if(request()->alert_type == "expiry_critical") selected @endif>期限緊急</option>
                <option value="excess" @if(request()->alert_type == "excess") selected @endif>過剰在庫</option>
              </select>
            </td>
          </tr>
          <tr>
            <th colspan="6">
              <label>
                <input type="checkbox" name="show_resolved" value="1" @if(request()->show_resolved) checked @endif>
                解決済みも表示
              </label>
            </th>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('inventory_alert')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- アラート一覧テーブル --}}
  @if(count($alerts) > 0)
  {{ $alerts->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <form action="{{route('inventory_alert_bulk_resolve')}}" method="POST" id="bulkResolveForm">
    @csrf
    <div class="mb-3">
      <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> 選択したアラートを解決</button>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>種別</th>
            <th>商品番号</th>
            <th>商品名</th>
            <th>倉庫</th>
            <th>現在数量</th>
            <th>閾値</th>
            <th>発生日時</th>
            <th>状態</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          @foreach($alerts as $alert)
          <tr class="@if($alert->is_resolved) table-secondary @endif">
            <td>
              @if(!$alert->is_resolved)
              <input type="checkbox" name="alert_ids[]" value="{{$alert->id}}" class="alert-checkbox">
              @endif
            </td>
            <td>
              @if($alert->alert_type == 'low_stock')
                <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> 低在庫</span>
              @elseif($alert->alert_type == 'out_of_stock')
                <span class="badge badge-danger"><i class="fas fa-times-circle"></i> 欠品</span>
              @elseif($alert->alert_type == 'expiry_warning')
                <span class="badge badge-warning"><i class="fas fa-calendar-times"></i> 期限警告</span>
              @elseif($alert->alert_type == 'expiry_critical')
                <span class="badge badge-danger"><i class="fas fa-calendar-times"></i> 期限緊急</span>
              @else
                <span class="badge badge-info"><i class="fas fa-boxes"></i> 過剰在庫</span>
              @endif
            </td>
            <td>{{$alert->goods_number}}</td>
            <td>{{$alert->goods_name}}</td>
            <td>{{$alert->warehouse_name}}</td>
            <td class="text-right">{{number_format($alert->current_quantity)}}</td>
            <td class="text-right">{{$alert->threshold_quantity ? number_format($alert->threshold_quantity) : '-'}}</td>
            <td>{{ \Carbon\Carbon::parse($alert->alert_date)->format('Y-m-d H:i') }}</td>
            <td>
              @if($alert->is_resolved)
                <span class="badge badge-secondary">解決済み</span><br>
                <small>{{ \Carbon\Carbon::parse($alert->resolved_at)->format('Y-m-d H:i') }}</small>
              @else
                <span class="badge badge-primary">未解決</span>
              @endif
            </td>
            <td>
              @if(!$alert->is_resolved)
              <form action="{{route('inventory_alert_resolve', $alert->id)}}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('このアラートを解決済みにしますか？')">
                  <i class="fas fa-check"></i> 解決
                </button>
              </form>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </form>

  {{ $alerts->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> アラートデータが見つかりませんでした。
  </div>
  @endif
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
  const checkboxes = document.querySelectorAll('.alert-checkbox');
  checkboxes.forEach(checkbox => checkbox.checked = this.checked);
});

document.getElementById('bulkResolveForm').addEventListener('submit', function(e) {
  const checked = document.querySelectorAll('.alert-checkbox:checked').length;
  if (checked === 0) {
    e.preventDefault();
    alert('解決するアラートを選択してください。');
    return false;
  }
  return confirm(checked + '件のアラートを解決済みにしますか？');
});
</script>

@endsection
