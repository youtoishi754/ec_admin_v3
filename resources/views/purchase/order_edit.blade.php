@extends('layouts.parents')
@section('title', '発注書編集')
@section('content')

<div class="container-fluid">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">発注書編集 - {{ $order->order_number }}</h3>
  
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $statusLabels = [
      'draft' => '下書き',
      'pending' => '承認待ち',
      'ordered' => '発注済み',
      'received' => '入荷完了',
      'cancelled' => 'キャンセル',
    ];
    $statusClass = [
      'draft' => 'badge-secondary',
      'pending' => 'badge-warning',
      'ordered' => 'badge-info',
      'received' => 'badge-success',
      'cancelled' => 'badge-dark',
    ];
  @endphp

  {{-- ステータス表示・変更 --}}
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">
        <i class="fas fa-flag"></i> ステータス: 
        <span class="badge {{ $statusClass[$order->status] ?? 'badge-secondary' }}">{{ $statusLabels[$order->status] ?? $order->status }}</span>
      </h5>
      @if($order->status != 'cancelled' && $order->status != 'received')
      <form action="{{ route('purchase_order_status', ['id' => $order->id]) }}" method="POST" class="form-inline">
        @csrf
        <select name="status" class="form-control mr-2">
          @if($order->status == 'draft')
            <option value="pending">承認待ちにする</option>
            <option value="cancelled">キャンセル</option>
          @elseif($order->status == 'pending')
            <option value="ordered">発注済みにする</option>
            <option value="draft">下書きに戻す</option>
            <option value="cancelled">キャンセル</option>
          @elseif($order->status == 'ordered')
            <option value="received">入荷完了にする</option>
            <option value="cancelled">キャンセル</option>
          @endif
        </select>
        <button type="submit" class="btn btn-primary"><i class="fas fa-sync"></i> ステータス変更</button>
      </form>
      @endif
    </div>
  </div>

  <form action="{{ route('purchase_order_update', ['id' => $order->id]) }}" method="POST" id="orderForm">
    @csrf
    @method('PUT')
    
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-info-circle"></i> 基本情報</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label>発注番号</label>
              <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>仕入先 <span class="text-danger">*</span></label>
              <select name="supplier_id" class="form-control" required @if($order->status != 'draft') disabled @endif>
                @foreach($suppliers as $supplier)
                  <option value="{{ $supplier->id }}" {{ $order->supplier_id == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->supplier_code }} - {{ $supplier->supplier_name }}
                  </option>
                @endforeach
              </select>
              @if($order->status != 'draft')
              <input type="hidden" name="supplier_id" value="{{ $order->supplier_id }}">
              @endif
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>発注日 <span class="text-danger">*</span></label>
              <input type="date" name="order_date" class="form-control" value="{{ $order->order_date }}" required @if($order->status != 'draft') readonly @endif>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              <label>納期予定日</label>
              <input type="date" name="expected_delivery_date" class="form-control" value="{{ $order->expected_delivery_date }}" @if($order->status != 'draft') readonly @endif>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>備考</label>
              <textarea name="notes" class="form-control" rows="2" @if($order->status != 'draft') readonly @endif>{{ $order->notes }}</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list"></i> 発注明細</h5>
        @if($order->status == 'draft')
        <button type="button" class="btn btn-success btn-sm" onclick="addItem()"><i class="fas fa-plus"></i> 商品追加</button>
        @endif
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="itemsTable">
            <thead class="thead-light">
              <tr>
                <th width="300">商品</th>
                <th width="120">数量</th>
                <th width="150">単価</th>
                <th width="150">小計</th>
                @if($order->status == 'draft')
                <th width="50">削除</th>
                @endif
              </tr>
            </thead>
            <tbody id="itemsBody">
              @foreach($orderDetails as $index => $detail)
              <tr class="item-row">
                <td>
                  @if($order->status == 'draft')
                  <select name="items[{{ $index }}][goods_id]" class="form-control goods-select" required>
                    @foreach($goods as $g)
                      <option value="{{ $g->id }}" data-price="{{ $g->goods_price }}" {{ $detail->goods_id == $g->id ? 'selected' : '' }}>
                        {{ $g->goods_number }} - {{ $g->goods_name }}
                      </option>
                    @endforeach
                  </select>
                  @else
                  {{ $detail->goods_number }} - {{ $detail->goods_name }}
                  <input type="hidden" name="items[{{ $index }}][goods_id]" value="{{ $detail->goods_id }}">
                  @endif
                </td>
                <td>
                  <input type="number" name="items[{{ $index }}][quantity]" class="form-control text-right quantity-input" 
                         min="1" value="{{ $detail->quantity }}" required onchange="calculateSubtotal()"
                         @if($order->status != 'draft') readonly @endif>
                </td>
                <td>
                  <input type="number" name="items[{{ $index }}][unit_price]" class="form-control text-right price-input" 
                         min="0" step="1" value="{{ $detail->unit_price }}" required onchange="calculateSubtotal()"
                         @if($order->status != 'draft') readonly @endif>
                </td>
                <td class="text-right subtotal-cell">¥{{ number_format($detail->subtotal) }}</td>
                @if($order->status == 'draft')
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)"><i class="fas fa-trash"></i></button></td>
                @endif
              </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <th colspan="{{ $order->status == 'draft' ? '3' : '3' }}" class="text-right">合計金額:</th>
                <th class="text-right" id="totalAmount">¥{{ number_format($order->total_amount) }}</th>
                @if($order->status == 'draft')
                <th></th>
                @endif
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <div class="text-center mb-4">
      @if($order->status == 'draft')
      <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> 更新</button>
      @endif
      <a href="{{ route('purchase_order_list') }}" class="btn btn-secondary btn-lg"><i class="fas fa-arrow-left"></i> 一覧に戻る</a>
    </div>
  </form>
</div>

@if($order->status == 'draft')
<script>
var itemIndex = {{ count($orderDetails) }};
var goodsOptions = `<option value="">-- 商品を選択 --</option>
@foreach($goods as $g)
<option value="{{ $g->id }}" data-price="{{ $g->goods_price }}">{{ $g->goods_number }} - {{ $g->goods_name }}</option>
@endforeach`;

function addItem() {
  var html = `<tr class="item-row">
    <td><select name="items[${itemIndex}][goods_id]" class="form-control goods-select" required onchange="updatePrice(this)">${goodsOptions}</select></td>
    <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control text-right quantity-input" min="1" value="1" required onchange="calculateSubtotal()"></td>
    <td><input type="number" name="items[${itemIndex}][unit_price]" class="form-control text-right price-input" min="0" step="1" value="0" required onchange="calculateSubtotal()"></td>
    <td class="text-right subtotal-cell">¥0</td>
    <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)"><i class="fas fa-trash"></i></button></td>
  </tr>`;
  document.getElementById('itemsBody').insertAdjacentHTML('beforeend', html);
  itemIndex++;
}

function removeItem(btn) {
  var rows = document.querySelectorAll('.item-row');
  if (rows.length > 1) {
    btn.closest('tr').remove();
    calculateSubtotal();
  } else {
    alert('最低1つの商品が必要です。');
  }
}

function updatePrice(select) {
  var option = select.options[select.selectedIndex];
  var price = option.dataset.price || 0;
  select.closest('tr').querySelector('.price-input').value = price;
  calculateSubtotal();
}

function calculateSubtotal() {
  var total = 0;
  document.querySelectorAll('.item-row').forEach(function(row) {
    var quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
    var price = parseFloat(row.querySelector('.price-input').value) || 0;
    var subtotal = quantity * price;
    row.querySelector('.subtotal-cell').textContent = '¥' + subtotal.toLocaleString();
    total += subtotal;
  });
  document.getElementById('totalAmount').textContent = '¥' + total.toLocaleString();
}
</script>
@endif

@endsection
