@extends('layouts.parents')
@section('title', '在庫管理ダッシュボード')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">
    <i class="fas fa-tachometer-alt"></i> 在庫管理ダッシュボード
  </h3>

  {{-- 主要KPI --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-primary text-white">
        <div class="card-body text-center">
          <h6><i class="fas fa-boxes"></i> 総在庫数</h6>
          <h2>{{ number_format($kpi['total_quantity']) }}</h2>
          <small>{{ $kpi['total_products'] }}商品</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body text-center">
          <h6><i class="fas fa-yen-sign"></i> 総在庫金額</h6>
          <h2>¥{{ number_format($kpi['total_value']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body text-center">
          <h6><i class="fas fa-arrow-down"></i> 本日入荷</h6>
          <h2>{{ number_format($kpi['today_inbound']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-secondary text-white">
        <div class="card-body text-center">
          <h6><i class="fas fa-arrow-up"></i> 本日出荷</h6>
          <h2>{{ number_format($kpi['today_outbound']) }}件</h2>
        </div>
      </div>
    </div>
  </div>

  {{-- 警告KPI --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card {{ $kpi['out_of_stock'] > 0 ? 'bg-danger' : 'bg-light' }} {{ $kpi['out_of_stock'] > 0 ? 'text-white' : '' }}">
        <div class="card-body text-center">
          <h6><i class="fas fa-exclamation-circle"></i> 在庫切れ</h6>
          <h2>{{ number_format($kpi['out_of_stock']) }}商品</h2>
          @if($kpi['out_of_stock'] > 0)
          <a href="{{ route('order_suggestion') }}" class="btn btn-sm btn-light">発注提案へ</a>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card {{ $kpi['low_stock'] > 0 ? 'bg-warning' : 'bg-light' }}">
        <div class="card-body text-center">
          <h6><i class="fas fa-exclamation-triangle"></i> 発注点以下</h6>
          <h2>{{ number_format($kpi['low_stock']) }}商品</h2>
          @if($kpi['low_stock'] > 0)
          <a href="{{ route('order_suggestion') }}" class="btn btn-sm btn-dark">発注提案へ</a>
          @endif
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card {{ $kpi['over_stock'] > 0 ? 'bg-info' : 'bg-light' }} {{ $kpi['over_stock'] > 0 ? 'text-white' : '' }}">
        <div class="card-body text-center">
          <h6><i class="fas fa-layer-group"></i> 過剰在庫</h6>
          <h2>{{ number_format($kpi['over_stock']) }}商品</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card {{ $expiredLots > 0 ? 'bg-danger' : 'bg-light' }} {{ $expiredLots > 0 ? 'text-white' : '' }}">
        <div class="card-body text-center">
          <h6><i class="fas fa-calendar-times"></i> 期限切れ</h6>
          <h2>{{ number_format($expiredLots) }}件</h2>
          @if($expiredLots > 0)
          <a href="{{ route('inventory_expiry') }}" class="btn btn-sm btn-light">確認する</a>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- 入出庫トレンド --}}
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-chart-line"></i> 入出庫トレンド（過去7日間）</h5>
        </div>
        <div class="card-body">
          <canvas id="movementTrendChart" height="200"></canvas>
        </div>
      </div>
    </div>

    {{-- クイックアクション --}}
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-bolt"></i> クイックアクション</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <a href="{{ route('stock_in') }}" class="btn btn-success btn-block mb-2">
              <i class="fas fa-arrow-down"></i> 入庫登録
            </a>
            <a href="{{ route('stock_out') }}" class="btn btn-warning btn-block mb-2">
              <i class="fas fa-arrow-up"></i> 出庫登録
            </a>
            <a href="{{ route('inventory') }}" class="btn btn-info btn-block mb-2">
              <i class="fas fa-search"></i> 在庫検索
            </a>
            <a href="{{ route('order_suggestion') }}" class="btn btn-primary btn-block mb-2">
              <i class="fas fa-lightbulb"></i> 発注提案 
              @if($orderSuggestions > 0)
              <span class="badge badge-light">{{ $orderSuggestions }}</span>
              @endif
            </a>
            <a href="{{ route('purchase_order_list') }}" class="btn btn-secondary btn-block">
              <i class="fas fa-file-invoice"></i> 発注書一覧
              @if($pendingOrders > 0)
              <span class="badge badge-warning">{{ $pendingOrders }}</span>
              @endif
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- カテゴリ別在庫構成 --}}
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-chart-pie"></i> カテゴリ別在庫構成</h5>
        </div>
        <div class="card-body">
          <canvas id="categoryChart" height="250"></canvas>
        </div>
      </div>
    </div>

    {{-- 倉庫別在庫状況 --}}
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-warehouse"></i> 倉庫別在庫状況</h5>
        </div>
        <div class="card-body">
          @if(count($warehouseStock) > 0)
          <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th>倉庫</th>
                <th class="text-right">数量</th>
                <th class="text-right">金額</th>
              </tr>
            </thead>
            <tbody>
              @foreach($warehouseStock as $warehouse)
              <tr>
                <td>{{ $warehouse->warehouse_name ?? '(未設定)' }}</td>
                <td class="text-right">{{ number_format($warehouse->total_quantity) }}</td>
                <td class="text-right">¥{{ number_format($warehouse->total_value) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <p class="text-muted mb-0">データがありません</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- アラート一覧 --}}
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-bell text-danger"></i> 未解決アラート</h5>
          <a href="{{ route('inventory_alert') }}" class="btn btn-sm btn-outline-primary">すべて見る</a>
        </div>
        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
          @if(count($alerts) > 0)
          <ul class="list-group list-group-flush">
            @foreach($alerts as $alert)
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                @php
                  $alertLabels = [
                    'low_stock' => '在庫僅少',
                    'out_of_stock' => '在庫切れ',
                    'excess' => '過剰在庫',
                    'expiry_warning' => '期限注意',
                    'expiry_critical' => '期限危険'
                  ];
                  $alertLabel = $alertLabels[$alert->alert_type] ?? $alert->alert_type;
                @endphp
                <span class="badge badge-{{ in_array($alert->alert_type, ['out_of_stock', 'expiry_critical']) ? 'danger' : (in_array($alert->alert_type, ['low_stock', 'expiry_warning']) ? 'warning' : 'info') }}">
                  {{ $alertLabel }}
                </span>
                <strong>{{ $alert->goods_number }}</strong> {{ $alert->goods_name }}
                <br><small class="text-muted">現在庫: {{ number_format($alert->current_quantity) }} / 閾値: {{ number_format($alert->threshold_quantity ?? 0) }}</small>
              </div>
              <small class="text-muted">{{ \Carbon\Carbon::parse($alert->created_at)->format('m/d H:i') }}</small>
            </li>
            @endforeach
          </ul>
          @else
          <p class="text-muted mb-0 text-center">未解決のアラートはありません</p>
          @endif
        </div>
      </div>
    </div>

    {{-- 有効期限切れ間近 --}}
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-clock text-warning"></i> 有効期限切れ間近（7日以内）</h5>
          <a href="{{ route('inventory_expiry') }}" class="btn btn-sm btn-outline-primary">すべて見る</a>
        </div>
        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
          @if(count($expiringLots) > 0)
          <table class="table table-sm table-bordered">
            <thead class="thead-light">
              <tr>
                <th>商品</th>
                <th>ロット</th>
                <th>期限</th>
                <th class="text-right">数量</th>
              </tr>
            </thead>
            <tbody>
              @foreach($expiringLots as $lot)
              <tr class="{{ \Carbon\Carbon::parse($lot->expiry_date)->isToday() ? 'table-danger' : 'table-warning' }}">
                <td>{{ $lot->goods_name }}</td>
                <td>{{ $lot->lot_number }}</td>
                <td>{{ \Carbon\Carbon::parse($lot->expiry_date)->format('Y/m/d') }}</td>
                <td class="text-right">{{ number_format($lot->quantity) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <p class="text-muted mb-0 text-center">期限切れ間近の在庫はありません</p>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- 最近の入荷 --}}
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-arrow-circle-down text-success"></i> 最近の入荷</h5>
          <a href="{{ route('stock_movement_history') }}?movement_type=in" class="btn btn-sm btn-outline-primary">すべて見る</a>
        </div>
        <div class="card-body">
          @if(count($recentInbound) > 0)
          <table class="table table-sm table-bordered">
            <thead class="thead-light">
              <tr>
                <th>日時</th>
                <th>商品</th>
                <th>倉庫</th>
                <th class="text-right">数量</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentInbound as $movement)
              <tr>
                <td>{{ \Carbon\Carbon::parse($movement->movement_date)->format('m/d H:i') }}</td>
                <td>{{ $movement->goods_name }}</td>
                <td>{{ $movement->warehouse_name }}</td>
                <td class="text-right text-success">+{{ number_format($movement->quantity) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <p class="text-muted mb-0 text-center">最近の入荷はありません</p>
          @endif
        </div>
      </div>
    </div>

    {{-- 最近の出荷 --}}
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0"><i class="fas fa-arrow-circle-up text-warning"></i> 最近の出荷</h5>
          <a href="{{ route('stock_movement_history') }}?movement_type=out" class="btn btn-sm btn-outline-primary">すべて見る</a>
        </div>
        <div class="card-body">
          @if(count($recentOutbound) > 0)
          <table class="table table-sm table-bordered">
            <thead class="thead-light">
              <tr>
                <th>日時</th>
                <th>商品</th>
                <th>倉庫</th>
                <th class="text-right">数量</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentOutbound as $movement)
              <tr>
                <td>{{ \Carbon\Carbon::parse($movement->movement_date)->format('m/d H:i') }}</td>
                <td>{{ $movement->goods_name }}</td>
                <td>{{ $movement->warehouse_name }}</td>
                <td class="text-right text-danger">-{{ number_format($movement->quantity) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <p class="text-muted mb-0 text-center">最近の出荷はありません</p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // 入出庫トレンドチャート
  const movementTrendData = @json($movementTrend);
  const movementCtx = document.getElementById('movementTrendChart').getContext('2d');
  new Chart(movementCtx, {
    type: 'line',
    data: {
      labels: movementTrendData.map(d => d.date),
      datasets: [
        {
          label: '入荷',
          data: movementTrendData.map(d => d.inbound),
          borderColor: '#28a745',
          backgroundColor: 'rgba(40, 167, 69, 0.1)',
          fill: true,
          tension: 0.3
        },
        {
          label: '出荷',
          data: movementTrendData.map(d => d.outbound),
          borderColor: '#ffc107',
          backgroundColor: 'rgba(255, 193, 7, 0.1)',
          fill: true,
          tension: 0.3
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // カテゴリ別在庫構成チャート
  const categoryData = @json($categoryStock);
  if (categoryData.length > 0) {
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
      type: 'doughnut',
      data: {
        labels: categoryData.map(d => d.category_name || '(未分類)'),
        datasets: [{
          data: categoryData.map(d => d.total_value),
          backgroundColor: [
            '#007bff', '#28a745', '#ffc107', '#dc3545', 
            '#17a2b8', '#6c757d', '#6f42c1', '#e83e8c'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right'
          }
        }
      }
    });
  }
});
</script>
@endsection
