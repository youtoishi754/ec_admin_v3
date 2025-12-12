@extends('layouts.parents')
@section('title', '発注詳細')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">発注詳細 - {{ $order->order_number }}</h3>
  
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @php
    $statusLabels = ['draft' => '下書き', 'pending' => '承認待ち', 'ordered' => '発注済み', 'received' => '入荷完了', 'cancelled' => 'キャンセル'];
    $statusClass = ['draft' => 'secondary', 'pending' => 'warning', 'ordered' => 'info', 'received' => 'success', 'cancelled' => 'dark'];
    $isOverdue = $order->expected_delivery_date && $order->expected_delivery_date < date('Y-m-d') && $order->status == 'ordered';
  @endphp

  <div class="row">
    {{-- 発注情報 --}}
    <div class="col-md-8">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-file-invoice"></i> 発注情報</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-sm">
                <tr>
                  <th width="120">発注番号</th>
                  <td>{{ $order->order_number }}</td>
                </tr>
                <tr>
                  <th>ステータス</th>
                  <td>
                    <span class="badge badge-{{ $statusClass[$order->status] ?? 'secondary' }}">
                      {{ $statusLabels[$order->status] ?? $order->status }}
                    </span>
                    @if($isOverdue)
                    <span class="badge badge-danger ml-1"><i class="fas fa-exclamation-triangle"></i> 納期遅延</span>
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>発注日</th>
                  <td>{{ $order->order_date }}</td>
                </tr>
                <tr>
                  <th>納期予定日</th>
                  <td class="{{ $isOverdue ? 'text-danger font-weight-bold' : '' }}">
                    {{ $order->expected_delivery_date ?? '未設定' }}
                  </td>
                </tr>
                <tr>
                  <th>合計金額</th>
                  <td class="font-weight-bold">¥{{ number_format($order->total_amount) }}</td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <table class="table table-sm">
                <tr>
                  <th width="120">発注確定日</th>
                  <td>{{ $order->ordered_date ?? '-' }}</td>
                </tr>
                <tr>
                  <th>入荷日</th>
                  <td>{{ $order->received_date ?? '-' }}</td>
                </tr>
                <tr>
                  <th>備考</th>
                  <td>{{ $order->notes ?? '-' }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- 発注明細 --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-list"></i> 発注明細</h5>
        </div>
        <div class="card-body">
          <table class="table table-bordered">
            <thead class="thead-light">
              <tr>
                <th>商品</th>
                <th class="text-right" width="100">数量</th>
                <th class="text-right" width="120">単価</th>
                <th class="text-right" width="120">小計</th>
                @if($order->status == 'ordered')
                <th class="text-right" width="100">入荷済</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($details as $detail)
              <tr>
                <td>
                  @if($detail->image_path)
                  <img src="{{ asset($detail->image_path) }}" alt="" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                  @endif
                  <strong>{{ $detail->goods_number }}</strong><br>
                  <small>{{ $detail->goods_name }}</small>
                </td>
                <td class="text-right">{{ number_format($detail->quantity) }}</td>
                <td class="text-right">¥{{ number_format($detail->unit_price) }}</td>
                <td class="text-right">¥{{ number_format($detail->subtotal) }}</td>
                @if($order->status == 'ordered')
                <td class="text-right">
                  {{ number_format($detail->received_quantity ?? 0) }}
                  @if(($detail->received_quantity ?? 0) < $detail->quantity)
                  <br><small class="text-warning">残: {{ $detail->quantity - ($detail->received_quantity ?? 0) }}</small>
                  @endif
                </td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th colspan="{{ $order->status == 'ordered' ? '3' : '3' }}" class="text-right">合計:</th>
                <th class="text-right">¥{{ number_format($order->total_amount) }}</th>
                @if($order->status == 'ordered')
                <th></th>
                @endif
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    {{-- 仕入先情報・アクション --}}
    <div class="col-md-4">
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-building"></i> 仕入先情報</h5>
        </div>
        <div class="card-body">
          <h5>{{ $order->supplier_name }}</h5>
          <p class="text-muted mb-2">{{ $order->supplier_code }}</p>
          @if($order->contact_email)
          <p class="mb-1"><i class="fas fa-envelope"></i> {{ $order->contact_email }}</p>
          @endif
          @if($order->contact_phone)
          <p class="mb-1"><i class="fas fa-phone"></i> {{ $order->contact_phone }}</p>
          @endif
          @if($order->address)
          <p class="mb-0"><i class="fas fa-map-marker-alt"></i> {{ $order->address }}</p>
          @endif
        </div>
      </div>

      {{-- アクションボタン --}}
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0"><i class="fas fa-cogs"></i> アクション</h5>
        </div>
        <div class="card-body">
          <a href="{{ route('purchase_order_edit', ['id' => $order->id]) }}" class="btn btn-primary btn-block mb-2">
            <i class="fas fa-edit"></i> 発注書を編集
          </a>
          
          @if($order->status == 'ordered')
          <button type="button" class="btn btn-success btn-block mb-2" data-toggle="modal" data-target="#receiveModal">
            <i class="fas fa-truck-loading"></i> 入荷処理
          </button>
          @endif

          <a href="{{ route('purchase_tracking') }}" class="btn btn-secondary btn-block">
            <i class="fas fa-arrow-left"></i> 一覧に戻る
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- 入荷処理モーダル --}}
@if($order->status == 'ordered')
<div class="modal fade" id="receiveModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form action="{{ route('purchase_receive', ['id' => $order->id]) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-truck-loading"></i> 入荷処理</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>入荷倉庫 <span class="text-danger">*</span></label>
            <select name="warehouse_id" class="form-control" required>
              <option value="">-- 選択してください --</option>
              {{-- 倉庫リストはコントローラーから取得する必要があります --}}
              <option value="1">メイン倉庫</option>
            </select>
          </div>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>商品</th>
                <th class="text-right">発注数</th>
                <th class="text-right">入荷済</th>
                <th class="text-right">今回入荷数</th>
              </tr>
            </thead>
            <tbody>
              @foreach($details as $index => $detail)
              <tr>
                <td>
                  {{ $detail->goods_number }} - {{ $detail->goods_name }}
                  <input type="hidden" name="received_items[{{ $index }}][detail_id]" value="{{ $detail->id }}">
                </td>
                <td class="text-right">{{ $detail->quantity }}</td>
                <td class="text-right">{{ $detail->received_quantity ?? 0 }}</td>
                <td>
                  <input type="number" name="received_items[{{ $index }}][received_quantity]" 
                         class="form-control text-right" min="0" 
                         max="{{ $detail->quantity - ($detail->received_quantity ?? 0) }}"
                         value="{{ $detail->quantity - ($detail->received_quantity ?? 0) }}">
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
          <div class="form-group">
            <label>備考</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
          <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> 入荷確定</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif

@endsection
