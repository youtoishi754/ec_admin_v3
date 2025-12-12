@extends('layouts.parents')
@section('title', '発注状況追跡')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">発注状況追跡</h3>
  
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- 統計ダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-2">
      <div class="card text-white bg-warning">
        <div class="card-body text-center">
          <h6><i class="fas fa-clock"></i> 承認待ち</h6>
          <h3>{{ number_format($stats['pending_count']) }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-white bg-info">
        <div class="card-body text-center">
          <h6><i class="fas fa-paper-plane"></i> 発注済み</h6>
          <h3>{{ number_format($stats['ordered_count']) }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-white bg-danger">
        <div class="card-body text-center">
          <h6><i class="fas fa-exclamation-triangle"></i> 納期遅延</h6>
          <h3>{{ number_format($stats['overdue_count']) }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card text-white bg-primary">
        <div class="card-body text-center">
          <h6><i class="fas fa-calendar-day"></i> 本日納期</h6>
          <h3>{{ number_format($stats['due_today_count']) }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-success">
        <div class="card-body text-center">
          <h6><i class="fas fa-yen-sign"></i> 発注中合計金額</h6>
          <h3>¥{{ number_format($stats['total_amount']) }}</h3>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{ route('purchase_tracking') }}" method="GET">
      <table class="table table_border_radius">
        <thead>
          <tr>
            <th colspan="6">検索条件</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>発注番号</th>
            <td><input type="text" name="order_number" value="{{ request()->order_number }}" class="form-control"></td>
            <th>仕入先</th>
            <td>
              <select name="supplier_id" class="form-control">
                <option value="">全て</option>
                @foreach($suppliers as $supplier)
                  <option value="{{ $supplier->id }}" @if(request()->supplier_id == $supplier->id) selected @endif>
                    {{ $supplier->supplier_name }}
                  </option>
                @endforeach
              </select>
            </td>
            <th>ステータス</th>
            <td>
              <select name="status" class="form-control">
                <option value="">全て</option>
                <option value="pending" @if(request()->status == 'pending') selected @endif>承認待ち</option>
                <option value="ordered" @if(request()->status == 'ordered') selected @endif>発注済み</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>納期状況</th>
            <td>
              <select name="delivery_status" class="form-control">
                <option value="">全て</option>
                <option value="overdue" @if(request()->delivery_status == 'overdue') selected @endif>納期遅延</option>
                <option value="due_today" @if(request()->delivery_status == 'due_today') selected @endif>本日納期</option>
                <option value="due_this_week" @if(request()->delivery_status == 'due_this_week') selected @endif>今週納期</option>
              </select>
            </td>
            <th colspan="4"></th>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{ route('purchase_tracking') }}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 発注状況一覧 --}}
  @if(count($orders) > 0)
  {{ $orders->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  @foreach($orders as $order)
  @php
    $isOverdue = $order->expected_delivery_date && $order->expected_delivery_date < date('Y-m-d');
    $isDueToday = $order->expected_delivery_date == date('Y-m-d');
  @endphp
  <div class="card mb-3 {{ $isOverdue ? 'border-danger' : ($isDueToday ? 'border-warning' : '') }}">
    <div class="card-header d-flex justify-content-between align-items-center {{ $isOverdue ? 'bg-danger text-white' : ($isDueToday ? 'bg-warning' : '') }}">
      <div>
        <strong>{{ $order->order_number }}</strong>
        <span class="badge {{ $order->status == 'pending' ? 'badge-warning' : 'badge-info' }} ml-2">
          {{ $order->status == 'pending' ? '承認待ち' : '発注済み' }}
        </span>
        @if($isOverdue)
        <span class="badge badge-light ml-2"><i class="fas fa-exclamation-triangle"></i> 納期遅延</span>
        @elseif($isDueToday)
        <span class="badge badge-dark ml-2"><i class="fas fa-clock"></i> 本日納期</span>
        @endif
      </div>
      <div>
        <a href="{{ route('purchase_tracking_detail', ['id' => $order->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> 詳細</a>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-3">
          <p class="mb-1"><strong>仕入先:</strong> {{ $order->supplier_name }}</p>
          <p class="mb-1"><small class="text-muted">{{ $order->contact_phone ?? '' }}</small></p>
        </div>
        <div class="col-md-2">
          <p class="mb-1"><strong>発注日:</strong></p>
          <p class="mb-0">{{ $order->order_date }}</p>
        </div>
        <div class="col-md-2">
          <p class="mb-1"><strong>納期予定:</strong></p>
          <p class="mb-0 {{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">
            {{ $order->expected_delivery_date ?? '未設定' }}
          </p>
        </div>
        <div class="col-md-2">
          <p class="mb-1"><strong>合計金額:</strong></p>
          <p class="mb-0">¥{{ number_format($order->total_amount) }}</p>
        </div>
        <div class="col-md-3">
          <p class="mb-1"><strong>商品:</strong></p>
          @foreach($order->details->take(3) as $detail)
          <small>・{{ $detail->goods_name }} x{{ $detail->quantity }}</small><br>
          @endforeach
          @if($order->details->count() > 3)
          <small class="text-muted">他{{ $order->details->count() - 3 }}件...</small>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endforeach

  {{ $orders->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 追跡対象の発注はありません。
  </div>
  @endif
</div>

@endsection
