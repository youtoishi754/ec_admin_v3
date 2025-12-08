@extends('layouts.parents')
@section('title', 'ロット編集')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">ロット編集</h3>
  
  <form action="{{route('inventory_lot_update', $lot->id)}}" method="POST">
    @csrf
    <table class="table table-bordered">
      <tbody>
        <tr>
          <th class="bg-light" style="width: 200px;">商品番号 <span class="text-danger">*</span></th>
          <td>
            <input type="text" name="goods_number" value="{{old('goods_number', $lot->goods_number)}}" class="form-control" required maxlength="50" placeholder="商品番号を入力">
          </td>
        </tr>
        <tr>
          <th class="bg-light">ロット番号 <span class="text-danger">*</span></th>
          <td>
            <input type="text" name="lot_number" value="{{old('lot_number', $lot->lot_number)}}" class="form-control" required maxlength="50" placeholder="例: LOT20250115-001">
          </td>
        </tr>
        <tr>
          <th class="bg-light">製造日</th>
          <td>
            <input type="date" name="production_date" value="{{old('production_date', $lot->production_date)}}" class="form-control">
          </td>
        </tr>
        <tr>
          <th class="bg-light">有効期限</th>
          <td>
            <input type="date" name="expiry_date" value="{{old('expiry_date', $lot->expiry_date)}}" class="form-control">
          </td>
        </tr>
        <tr>
          <th class="bg-light">仕入先名</th>
          <td>
            <input type="text" name="supplier_name" value="{{old('supplier_name', $lot->supplier_name)}}" class="form-control" maxlength="100" placeholder="仕入先名を入力">
          </td>
        </tr>
        <tr>
          <th class="bg-light">仕入先コード</th>
          <td>
            <input type="text" name="supplier_code" value="{{old('supplier_code', $lot->supplier_code)}}" class="form-control" maxlength="50" placeholder="仕入先コードを入力">
          </td>
        </tr>
        <tr>
          <th class="bg-light">検品状態 <span class="text-danger">*</span></th>
          <td>
            <select name="inspection_status" class="form-control" required>
              <option value="pending" @if(old('inspection_status', $lot->inspection_status) == 'pending') selected @endif>検品待ち</option>
              <option value="passed" @if(old('inspection_status', $lot->inspection_status) == 'passed') selected @endif>合格</option>
              <option value="failed" @if(old('inspection_status', $lot->inspection_status) == 'failed') selected @endif>不合格</option>
            </select>
          </td>
        </tr>
        <tr>
          <th class="bg-light">備考</th>
          <td>
            <textarea name="notes" class="form-control" rows="3" placeholder="備考を入力">{{old('notes', $lot->notes)}}</textarea>
          </td>
        </tr>
        <tr>
          <th class="bg-light">登録日時</th>
          <td>{{$lot->created_at}}</td>
        </tr>
        <tr>
          <th class="bg-light">更新日時</th>
          <td>{{$lot->updated_at}}</td>
        </tr>
      </tbody>
    </table>

    <div class="text-center mt-3">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save"></i> 更新する
      </button>
      <a href="{{route('inventory_lot')}}" class="btn btn-secondary btn-lg">
        <i class="fas fa-times"></i> キャンセル
      </a>
    </div>
  </form>
</div>

@endsection
