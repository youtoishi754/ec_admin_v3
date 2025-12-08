@extends('layouts.parents')
@section('title', '入庫登録')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">入庫登録</h3>
  
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

  @if($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <form action="{{route('stock_in_store')}}" method="POST">
    @csrf
    <table class="table table_border_radius">
      <thead>
        <tr>
          <th colspan="2">入庫情報入力</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>商品 <span class="text-danger">*</span></th>
          <td>
            <select name="goods_id" class="form-control" required id="goods_id">
              <option value="">-- 商品を選択 --</option>
              @foreach($goods as $item)
                <option value="{{$item->id}}" {{ old('goods_id') == $item->id ? 'selected' : '' }}>
                  {{$item->goods_number}} - {{$item->goods_name}}
                </option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th>倉庫 <span class="text-danger">*</span></th>
          <td>
            <select name="warehouse_id" class="form-control" required id="warehouse_id">
              <option value="">-- 倉庫を選択 --</option>
              @foreach($warehouses as $warehouse)
                <option value="{{$warehouse->id}}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                  {{$warehouse->warehouse_code}} - {{$warehouse->warehouse_name}}
                </option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th>ロケーション</th>
          <td>
            <select name="location_id" class="form-control" id="location_id">
              <option value="">-- ロケーション未指定 --</option>
              @foreach($locations as $warehouseId => $warehouseLocations)
                @foreach($warehouseLocations as $location)
                  <option value="{{$location->id}}" data-warehouse="{{$warehouseId}}" style="display:none;" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                    {{$location->location_code}}
                  </option>
                @endforeach
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th>数量 <span class="text-danger">*</span></th>
          <td>
            <input type="number" name="quantity" class="form-control" required min="1" value="{{ old('quantity', 1) }}">
          </td>
        </tr>
        <tr>
          <th>ロット番号</th>
          <td>
            <input type="text" name="lot_number" class="form-control" value="{{ old('lot_number') }}" placeholder="ロット番号（任意）">
          </td>
        </tr>
        <tr>
          <th>シリアル番号</th>
          <td>
            <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}" placeholder="シリアル番号（任意）">
          </td>
        </tr>
        <tr>
          <th>製造日</th>
          <td>
            <input type="date" name="manufacturing_date" class="form-control" value="{{ old('manufacturing_date') }}">
          </td>
        </tr>
        <tr>
          <th>有効期限</th>
          <td>
            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}">
          </td>
        </tr>
        <tr>
          <th>入庫日時 <span class="text-danger">*</span></th>
          <td>
            <input type="datetime-local" name="movement_date" class="form-control" required value="{{ old('movement_date', now()->format('Y-m-d\TH:i')) }}">
          </td>
        </tr>
        <tr>
          <th>備考</th>
          <td>
            <textarea name="notes" class="form-control" rows="3" placeholder="備考（任意）">{{ old('notes') }}</textarea>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="2" class="t_foot">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> 入庫登録</button>
            <a href="{{route('stock_movement_history')}}" class="btn btn-secondary"><i class="fas fa-history"></i> 履歴を見る</a>
          </th>
        </tr>
      </tfoot>
    </table>
  </form>
</div>

<script>
// 倉庫選択時にロケーションをフィルタリング
document.getElementById('warehouse_id').addEventListener('change', function() {
  const warehouseId = this.value;
  const locationSelect = document.getElementById('location_id');
  const options = locationSelect.querySelectorAll('option');
  
  // 全てのオプションを非表示
  options.forEach(option => {
    if (option.value === '') {
      option.style.display = '';
    } else if (option.dataset.warehouse === warehouseId) {
      option.style.display = '';
    } else {
      option.style.display = 'none';
    }
  });
  
  // 選択をリセット
  locationSelect.value = '';
});

// ページロード時に倉庫が選択されている場合、ロケーションをフィルタリング
window.addEventListener('DOMContentLoaded', function() {
  const warehouseSelect = document.getElementById('warehouse_id');
  if (warehouseSelect.value) {
    warehouseSelect.dispatchEvent(new Event('change'));
  }
});
</script>

@endsection
