@extends('layouts.parents')
@section('title', '棚卸実施')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">棚卸実施</h3>
  
  <form action="{{route('inventory_stocktaking_store')}}" method="POST">
    @csrf
    <input type="hidden" name="inventory_id" value="{{$inventory->id}}">
    
    <table class="table table-bordered">
      <tbody>
        <tr>
          <th class="bg-light" style="width: 200px;">商品番号</th>
          <td>{{$inventory->goods_number}}</td>
        </tr>
        <tr>
          <th class="bg-light">商品名</th>
          <td>{{$inventory->goods_name}}</td>
        </tr>
        <tr>
          <th class="bg-light">倉庫</th>
          <td>{{$inventory->warehouse_name}}</td>
        </tr>
        <tr>
          <th class="bg-light">ロケーション</th>
          <td>{{$inventory->location_code}}</td>
        </tr>
        <tr>
          <th class="bg-light">ロット番号</th>
          <td>{{$inventory->lot_number ?? '-'}}</td>
        </tr>
        <tr>
          <th class="bg-light">シリアル番号</th>
          <td>{{$inventory->serial_number ?? '-'}}</td>
        </tr>
        <tr>
          <th class="bg-light">帳簿在庫数</th>
          <td><strong class="text-primary">{{number_format($inventory->quantity)}}</strong></td>
        </tr>
        <tr>
          <th class="bg-light">引当済数</th>
          <td>{{number_format($inventory->reserved_quantity)}}</td>
        </tr>
        <tr>
          <th class="bg-light">有効在庫数</th>
          <td>{{number_format($inventory->quantity - $inventory->reserved_quantity)}}</td>
        </tr>
        <tr>
          <th class="bg-light">最終棚卸日</th>
          <td>
            @if($inventory->last_counted_at)
              {{\Carbon\Carbon::parse($inventory->last_counted_at)->format('Y/m/d H:i')}}
            @else
              <span class="text-danger">未実施</span>
            @endif
          </td>
        </tr>
        <tr class="table-info">
          <th class="bg-info text-white">実棚数量 <span class="text-danger">*</span></th>
          <td>
            <input type="number" name="counted_quantity" value="{{old('counted_quantity', $inventory->quantity)}}" class="form-control form-control-lg" required min="0" style="font-size: 1.5em; font-weight: bold;">
            <small class="form-text text-muted">実際にカウントした数量を入力してください</small>
          </td>
        </tr>
        <tr>
          <th class="bg-light">棚卸担当者</th>
          <td>
            <input type="text" name="counted_by" value="{{old('counted_by', auth()->user()->name ?? '')}}" class="form-control" maxlength="100">
          </td>
        </tr>
        <tr>
          <th class="bg-light">備考</th>
          <td>
            <textarea name="notes" class="form-control" rows="3" placeholder="差異がある場合は理由を記入してください">{{old('notes')}}</textarea>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="alert alert-warning">
      <i class="fas fa-exclamation-triangle"></i> <strong>注意事項</strong>
      <ul class="mb-0">
        <li>実棚数量が帳簿在庫数と異なる場合、在庫が自動的に調整されます</li>
        <li>調整履歴は在庫移動履歴に記録されます</li>
        <li>実行後の取消はできません</li>
      </ul>
    </div>

    <div class="text-center mt-3">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-check"></i> 棚卸実施
      </button>
      <a href="{{route('inventory_stocktaking')}}" class="btn btn-secondary btn-lg">
        <i class="fas fa-times"></i> キャンセル
      </a>
    </div>
  </form>
</div>

<script>
// 差異を自動計算して表示
document.querySelector('input[name="counted_quantity"]').addEventListener('input', function() {
  const bookQuantity = {{$inventory->quantity}};
  const countedQuantity = parseInt(this.value) || 0;
  const difference = countedQuantity - bookQuantity;
  
  // 差異表示の行が既にあれば削除
  const existingRow = document.getElementById('difference-row');
  if (existingRow) {
    existingRow.remove();
  }
  
  // 差異がある場合は表示
  if (difference !== 0) {
    const row = document.createElement('tr');
    row.id = 'difference-row';
    row.className = difference > 0 ? 'table-success' : 'table-danger';
    row.innerHTML = `
      <th class="bg-light">差異</th>
      <td>
        <strong style="font-size: 1.3em;" class="${difference > 0 ? 'text-success' : 'text-danger'}">
          ${difference > 0 ? '+' : ''}${difference.toLocaleString()}
        </strong>
        <span class="ml-2">(${difference > 0 ? '過剰' : '不足'})</span>
      </td>
    `;
    this.closest('tr').insertAdjacentElement('afterend', row);
  }
});
</script>

@endsection
