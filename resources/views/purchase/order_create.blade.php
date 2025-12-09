@extends('layouts.parents')
@section('title', '発注書作成')
@section('content')

<div class="container-fluid">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">発注書作成</h3>
  
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

  <form action="{{ route('purchase_order_store') }}" method="POST" id="orderForm">
    @csrf
    
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-info-circle"></i> 基本情報</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>仕入先 <span class="text-danger">*</span></label>
              <select name="supplier_id" class="form-control" required>
                <option value="">-- 選択してください --</option>
                @foreach($suppliers as $supplier)
                  <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->supplier_code }} - {{ $supplier->supplier_name }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>発注日 <span class="text-danger">*</span></label>
              <input type="date" name="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}" required>
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>納期予定日</label>
              <input type="date" name="expected_delivery_date" class="form-control" value="{{ old('expected_delivery_date') }}">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>備考</label>
              <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list"></i> 発注明細</h5>
        <button type="button" class="btn btn-success btn-sm" onclick="addItem()"><i class="fas fa-plus"></i> 商品追加</button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="itemsTable">
            <thead class="thead-light">
              <tr>
                <th width="300">商品</th>
                <th width="100">現在庫</th>
                <th width="120">数量</th>
                <th width="150">単価</th>
                <th width="150">小計</th>
                <th width="50">削除</th>
              </tr>
            </thead>
            <tbody id="itemsBody">
              <tr class="item-row">
                <td>
                  <select name="items[0][goods_id]" class="form-control goods-select" required onchange="updateStock(this, 0)">
                    <option value="">-- 商品を選択 --</option>
                    @foreach($goods as $g)
                      <option value="{{ $g->id }}" data-price="{{ $g->goods_price }}" data-stock="{{ $g->current_stock }}">
                        {{ $g->goods_number }} - {{ $g->goods_name }}
                      </option>
                    @endforeach
                  </select>
                </td>
                <td class="text-right stock-cell">-</td>
                <td><input type="number" name="items[0][quantity]" class="form-control text-right quantity-input" min="1" value="1" required onchange="calculateSubtotal(0)"></td>
                <td><input type="number" name="items[0][unit_price]" class="form-control text-right price-input" min="0" step="1" value="0" required onchange="calculateSubtotal(0)"></td>
                <td class="text-right subtotal-cell">¥0</td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm" onclick="removeItem(this)"><i class="fas fa-trash"></i></button></td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <th colspan="4" class="text-right">合計金額:</th>
                <th class="text-right" id="totalAmount">¥0</th>
                <th></th>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <div class="text-center mb-4">
      <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> 発注書を保存</button>
      <a href="{{ route('purchase_order_list') }}" class="btn btn-secondary btn-lg"><i class="fas fa-times"></i> キャンセル</a>
    </div>
  </form>
</div>

<script>
var itemIndex = 1;
var goodsOptions = `<option value="">-- 商品を選択 --</option>
@foreach($goods as $g)
<option value="{{ $g->id }}" data-price="{{ $g->goods_price }}" data-stock="{{ $g->current_stock }}">{{ $g->goods_number }} - {{ $g->goods_name }}</option>
@endforeach`;

function addItem() {
  var html = `<tr class="item-row">
    <td><select name="items[${itemIndex}][goods_id]" class="form-control goods-select" required onchange="updateStock(this, ${itemIndex})">${goodsOptions}</select></td>
    <td class="text-right stock-cell">-</td>
    <td><input type="number" name="items[${itemIndex}][quantity]" class="form-control text-right quantity-input" min="1" value="1" required onchange="calculateSubtotal(${itemIndex})"></td>
    <td><input type="number" name="items[${itemIndex}][unit_price]" class="form-control text-right price-input" min="0" step="1" value="0" required onchange="calculateSubtotal(${itemIndex})"></td>
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
    calculateTotal();
  } else {
    alert('最低1つの商品が必要です。');
  }
}

function updateStock(select, index) {
  var option = select.options[select.selectedIndex];
  var stock = option.dataset.stock || '-';
  var price = option.dataset.price || 0;
  var row = select.closest('tr');
  row.querySelector('.stock-cell').textContent = stock != '-' ? parseInt(stock).toLocaleString() : '-';
  row.querySelector('.price-input').value = price;
  calculateSubtotal(index);
}

function calculateSubtotal(index) {
  var rows = document.querySelectorAll('.item-row');
  rows.forEach(function(row) {
    var quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
    var price = parseFloat(row.querySelector('.price-input').value) || 0;
    var subtotal = quantity * price;
    row.querySelector('.subtotal-cell').textContent = '¥' + subtotal.toLocaleString();
  });
  calculateTotal();
}

function calculateTotal() {
  var total = 0;
  document.querySelectorAll('.subtotal-cell').forEach(function(cell) {
    var value = cell.textContent.replace(/[¥,]/g, '');
    total += parseInt(value) || 0;
  });
  document.getElementById('totalAmount').textContent = '¥' + total.toLocaleString();
}
</script>

@endsection
