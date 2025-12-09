@extends('layouts.parents')
@section('title', '発注実績分析')
@section('content')

<div class="container-fluid">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">発注実績分析</h3>
  
  {{-- 期間選択 --}}
  <div class="card mb-4">
    <div class="card-body">
      <form action="{{ route('order_analytics') }}" method="GET" class="form-inline">
        <label class="mr-2">分析期間:</label>
        <select name="period" class="form-control mr-2" onchange="this.form.submit()">
          <option value="month" {{ $period == 'month' ? 'selected' : '' }}>月次</option>
          <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>四半期</option>
          <option value="year" {{ $period == 'year' ? 'selected' : '' }}>年次</option>
        </select>
        <select name="year" class="form-control mr-2" onchange="this.form.submit()">
          @for($y = date('Y'); $y >= date('Y') - 3; $y--)
          <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}年</option>
          @endfor
        </select>
        @if($period == 'month')
        <select name="month" class="form-control mr-2" onchange="this.form.submit()">
          @for($m = 1; $m <= 12; $m++)
          <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $m }}月</option>
          @endfor
        </select>
        @endif
        <span class="text-muted ml-3">対象期間: {{ $startDate }} 〜 {{ $endDate }}</span>
      </form>
    </div>
  </div>

  {{-- サマリーダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-primary">
        <div class="card-body text-center">
          <h6><i class="fas fa-file-invoice"></i> 総発注件数</h6>
          <h2>{{ number_format($summary['total_orders']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success">
        <div class="card-body text-center">
          <h6><i class="fas fa-yen-sign"></i> 発注総額</h6>
          <h2>¥{{ number_format($summary['total_amount']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-info">
        <div class="card-body text-center">
          <h6><i class="fas fa-check-circle"></i> 入荷完了</h6>
          <h2>{{ number_format($summary['received_orders']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-secondary">
        <div class="card-body text-center">
          <h6><i class="fas fa-calculator"></i> 平均発注額</h6>
          <h2>¥{{ number_format($summary['avg_order_amount'] ?? 0) }}</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- 仕入先別発注実績 --}}
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-building"></i> 仕入先別発注実績（上位10件）</h5>
        </div>
        <div class="card-body">
          @if(count($supplierStats) > 0)
          <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th>仕入先</th>
                <th class="text-right">発注回数</th>
                <th class="text-right">発注金額</th>
              </tr>
            </thead>
            <tbody>
              @foreach($supplierStats as $stat)
              <tr>
                <td>{{ $stat->supplier_name ?? '（未設定）' }}</td>
                <td class="text-right">{{ number_format($stat->order_count) }}</td>
                <td class="text-right">¥{{ number_format($stat->total_amount) }}</td>
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

    {{-- 商品別発注実績 --}}
    <div class="col-md-6">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-box"></i> 商品別発注実績（上位10件）</h5>
        </div>
        <div class="card-body">
          @if(count($goodsStats) > 0)
          <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th>商品</th>
                <th class="text-right">数量</th>
                <th class="text-right">金額</th>
              </tr>
            </thead>
            <tbody>
              @foreach($goodsStats as $stat)
              <tr>
                <td>{{ $stat->goods_number }} - {{ $stat->goods_name }}</td>
                <td class="text-right">{{ number_format($stat->total_quantity) }}</td>
                <td class="text-right">¥{{ number_format($stat->total_amount) }}</td>
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
    {{-- 月別推移 --}}
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-chart-line"></i> 月別発注推移（過去12ヶ月）</h5>
        </div>
        <div class="card-body">
          <table class="table table-bordered table-sm">
            <thead class="thead-light">
              <tr>
                <th>月</th>
                <th class="text-right">発注件数</th>
                <th class="text-right">発注金額</th>
                <th>金額グラフ</th>
              </tr>
            </thead>
            <tbody>
              @php
                $maxAmount = max(array_column($monthlyTrend, 'total_amount'));
              @endphp
              @foreach($monthlyTrend as $trend)
              <tr>
                <td>{{ $trend['label'] }}</td>
                <td class="text-right">{{ number_format($trend['order_count']) }}</td>
                <td class="text-right">¥{{ number_format($trend['total_amount']) }}</td>
                <td>
                  <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-primary" style="width: {{ $maxAmount > 0 ? ($trend['total_amount'] / $maxAmount * 100) : 0 }}%"></div>
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ステータス別・リードタイム --}}
    <div class="col-md-4">
      {{-- ステータス別集計 --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-chart-pie"></i> ステータス別集計</h5>
        </div>
        <div class="card-body">
          @php
            $statusLabels = ['draft' => '下書き', 'pending' => '承認待ち', 'ordered' => '発注済み', 'received' => '入荷完了', 'cancelled' => 'キャンセル'];
            $statusColors = ['draft' => 'secondary', 'pending' => 'warning', 'ordered' => 'info', 'received' => 'success', 'cancelled' => 'dark'];
          @endphp
          @foreach($statusLabels as $status => $label)
          @php $stat = $statusStats[$status] ?? null; @endphp
          <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="badge badge-{{ $statusColors[$status] }}">{{ $label }}</span>
            <span>
              {{ $stat ? number_format($stat->count) : 0 }}件 / 
              ¥{{ $stat ? number_format($stat->amount) : 0 }}
            </span>
          </div>
          @endforeach
        </div>
      </div>

      {{-- リードタイム分析 --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-clock"></i> リードタイム分析</h5>
        </div>
        <div class="card-body">
          @if($leadTimeStats && $leadTimeStats->avg_lead_time)
          <table class="table table-bordered table-sm">
            <tr>
              <th>平均リードタイム</th>
              <td class="text-right">{{ number_format($leadTimeStats->avg_lead_time, 1) }}日</td>
            </tr>
            <tr>
              <th>最短リードタイム</th>
              <td class="text-right">{{ number_format($leadTimeStats->min_lead_time) }}日</td>
            </tr>
            <tr>
              <th>最長リードタイム</th>
              <td class="text-right">{{ number_format($leadTimeStats->max_lead_time) }}日</td>
            </tr>
          </table>
          @else
          <p class="text-muted mb-0">データがありません</p>
          @endif
        </div>
      </div>

      {{-- エクスポート --}}
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-download"></i> データエクスポート</h5>
        </div>
        <div class="card-body">
          <a href="{{ route('order_analytics_export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success btn-block">
            <i class="fas fa-file-csv"></i> CSVダウンロード
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
