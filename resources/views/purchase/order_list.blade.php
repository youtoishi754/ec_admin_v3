@extends('layouts.parents')
@section('title', '発注書一覧')
@section('content')

<div class="container-fluid">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">発注書一覧</h3>
  
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- 統計ダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-secondary">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-edit"></i> 下書き</h5>
          <h2>{{ number_format($stats['draft_count']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-clock"></i> 承認待ち</h5>
          <h2>{{ number_format($stats['pending_count']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-info">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-paper-plane"></i> 発注済み</h5>
          <h2>{{ number_format($stats['ordered_count']) }}件</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-check-circle"></i> 入荷完了</h5>
          <h2>{{ number_format($stats['received_count']) }}件</h2>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{ route('purchase_order_list') }}" method="GET">
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
                <option value="draft" @if(request()->status == 'draft') selected @endif>下書き</option>
                <option value="pending" @if(request()->status == 'pending') selected @endif>承認待ち</option>
                <option value="ordered" @if(request()->status == 'ordered') selected @endif>発注済み</option>
                <option value="received" @if(request()->status == 'received') selected @endif>入荷完了</option>
                <option value="cancelled" @if(request()->status == 'cancelled') selected @endif>キャンセル</option>
              </select>
            </td>
          </tr>
          <tr>
            <th>発注日（開始）</th>
            <td><input type="date" name="order_date_from" value="{{ request()->order_date_from }}" class="form-control"></td>
            <th>発注日（終了）</th>
            <td><input type="date" name="order_date_to" value="{{ request()->order_date_to }}" class="form-control"></td>
            <th colspan="2"></th>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{ route('purchase_order_list') }}'"><i class="fas fa-undo"></i> リセット</button>
              <span class="ml-3">|</span>
              <a href="{{ route('purchase_order_export_csv', request()->all()) }}" class="btn btn-outline-success ml-2"><i class="fas fa-file-csv"></i> CSV出力</a>
              <a href="{{ route('purchase_order_create') }}" class="btn btn-success float-right"><i class="fas fa-plus"></i> 新規発注書作成</a>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 発注書一覧 --}}
  @if(count($orders) > 0)
  {{ $orders->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="thead-light">
        <tr>
          <th>発注番号</th>
          <th>仕入先</th>
          <th>発注日</th>
          <th>納期予定日</th>
          <th class="text-right">合計金額</th>
          <th>ステータス</th>
          <th>作成日時</th>
          <th width="140">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($orders as $order)
        @php
          $statusClass = '';
          $statusLabel = '';
          switch($order->status) {
            case 'draft': $statusClass = 'badge-secondary'; $statusLabel = '下書き'; break;
            case 'pending': $statusClass = 'badge-warning'; $statusLabel = '承認待ち'; break;
            case 'ordered': $statusClass = 'badge-info'; $statusLabel = '発注済み'; break;
            case 'received': $statusClass = 'badge-success'; $statusLabel = '入荷完了'; break;
            case 'cancelled': $statusClass = 'badge-dark'; $statusLabel = 'キャンセル'; break;
          }
        @endphp
        <tr>
          <td><a href="{{ route('purchase_order_edit', ['id' => $order->id]) }}">{{ $order->order_number }}</a></td>
          <td>{{ $order->supplier_name ?? '-' }}</td>
          <td>{{ $order->order_date }}</td>
          <td>{{ $order->expected_delivery_date ?? '-' }}</td>
          <td class="text-right">¥{{ number_format($order->total_amount) }}</td>
          <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
          <td>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d H:i') }}</td>
          <td>
            <a href="{{ route('purchase_order_pdf', ['id' => $order->id]) }}" class="btn btn-sm btn-outline-danger" title="PDF出力"><i class="fas fa-file-pdf"></i></a>
            <a href="{{ route('purchase_order_edit', ['id' => $order->id]) }}" class="btn btn-sm btn-primary" title="編集"><i class="fas fa-edit"></i></a>
            @if($order->status == 'draft')
            <form action="{{ route('purchase_order_delete', ['id' => $order->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('この発注書を削除してもよろしいですか？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger" title="削除"><i class="fas fa-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $orders->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 発注書がありません。
  </div>
  @endif
</div>

@endsection
