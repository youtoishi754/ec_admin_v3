@extends('layouts.parents')
@section('title', '発注提案')
@section('content')

<div class="container-fluid">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">発注提案</h3>
  
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- 統計ダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-danger">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-exclamation-circle"></i> 緊急（欠品）</h5>
          <h2>{{ number_format($stats['critical_count']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-exclamation-triangle"></i> 高（低在庫）</h5>
          <h2>{{ number_format($stats['high_count']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-info">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-info-circle"></i> 中（発注点以下）</h5>
          <h2>{{ number_format($stats['medium_count']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-lightbulb"></i> 発注提案総数</h5>
          <h2>{{ number_format($stats['total_suggestions']) }}件</h2>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{ route('order_suggestion') }}" method="GET">
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
            <th>カテゴリ</th>
            <td>
              <select name="category_id" class="form-control">
                <option value="">全て</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" @if(request()->category_id == $category->id) selected @endif>
                    {{ $category->category_name }}
                  </option>
                @endforeach
              </select>
            </td>
          </tr>
          <tr>
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
            <th>緊急度</th>
            <td>
              <select name="urgency" class="form-control">
                <option value="">全て</option>
                <option value="critical" @if(request()->urgency == 'critical') selected @endif>緊急（欠品）</option>
                <option value="high" @if(request()->urgency == 'high') selected @endif>高（低在庫）</option>
                <option value="medium" @if(request()->urgency == 'medium') selected @endif>中（発注点以下）</option>
              </select>
            </td>
            <th colspan="2"></th>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{ route('order_suggestion') }}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 発注提案一覧 --}}
  @if(count($suggestions) > 0)
  {{ $suggestions->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <form action="{{ route('order_suggestion_create') }}" method="POST" id="orderForm">
    @csrf
    <div class="mb-3">
      <select name="supplier_id" class="form-control d-inline-block" style="width: auto;" required>
        <option value="">-- 仕入先を選択 --</option>
        @foreach($suppliers as $supplier)
          <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
        @endforeach
      </select>
      <button type="submit" class="btn btn-success"><i class="fas fa-file-invoice"></i> 選択した商品で発注書作成</button>
    </div>

    <div class="table-responsive">
      <table class="table table-hover table-bordered">
        <thead class="thead-light">
          <tr>
            <th width="40">
              <input type="checkbox" id="selectAll" onclick="toggleAll(this)">
            </th>
            <th>商品番号</th>
            <th>商品名</th>
            <th>カテゴリ</th>
            <th>仕入先</th>
            <th class="text-right">現在庫数</th>
            <th class="text-right">利用可能数</th>
            <th class="text-right">最低在庫数</th>
            <th class="text-right">発注点</th>
            <th>緊急度</th>
            <th class="text-right">推奨発注数</th>
          </tr>
        </thead>
        <tbody>
          @foreach($suggestions as $item)
          @php
            $urgencyClass = '';
            $urgencyLabel = '';
            if ($item->available_stock <= 0) {
              $urgencyClass = 'table-danger';
              $urgencyLabel = '<span class="badge badge-danger">緊急</span>';
            } elseif ($item->available_stock <= $item->min_stock_level) {
              $urgencyClass = 'table-warning';
              $urgencyLabel = '<span class="badge badge-warning">高</span>';
            } else {
              $urgencyClass = 'table-info';
              $urgencyLabel = '<span class="badge badge-info">中</span>';
            }
          @endphp
          <tr class="{{ $urgencyClass }}">
            <td class="text-center">
              <input type="checkbox" name="goods_ids[]" value="{{ $item->id }}" class="item-checkbox">
              <input type="hidden" name="quantities[{{ $loop->index }}]" value="{{ max($item->suggested_quantity, $item->reorder_quantity ?? 10) }}" class="quantity-input">
            </td>
            <td>{{ $item->goods_number }}</td>
            <td>{{ $item->goods_name }}</td>
            <td>{{ $item->category_name ?? '-' }}</td>
            <td>{{ $item->supplier_name ?? '-' }}</td>
            <td class="text-right">{{ number_format($item->current_stock) }}</td>
            <td class="text-right">{{ number_format($item->available_stock) }}</td>
            <td class="text-right">{{ number_format($item->min_stock_level ?? 0) }}</td>
            <td class="text-right">{{ number_format($item->reorder_point ?? 0) }}</td>
            <td>{!! $urgencyLabel !!}</td>
            <td class="text-right">
              <input type="number" value="{{ max($item->suggested_quantity, $item->reorder_quantity ?? 10) }}" 
                     min="1" class="form-control form-control-sm text-right" style="width: 80px; display: inline-block;"
                     onchange="updateQuantity(this, {{ $loop->index }})">
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </form>

  {{ $suggestions->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 発注が必要な商品はありません。
  </div>
  @endif
</div>

<script>
function toggleAll(checkbox) {
  document.querySelectorAll('.item-checkbox').forEach(function(cb) {
    cb.checked = checkbox.checked;
  });
}

function updateQuantity(input, index) {
  document.querySelector('input[name="quantities[' + index + ']"]').value = input.value;
}
</script>

@endsection
