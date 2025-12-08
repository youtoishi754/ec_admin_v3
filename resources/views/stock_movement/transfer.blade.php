@extends('layouts.parents')
@section('title', '移動在庫')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">移動在庫</h3>
  
  @if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
  </div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
  </div>
  @endif

  <form action="{{route('stock_transfer_store')}}" method="POST">
    @csrf
    <table class="table table_border_radius">
      <thead>
        <tr><th colspan="2">移動在庫情報入力</th></tr>
      </thead>
      <tbody>
        <tr>
          <th>商品 <span class="text-danger">*</span></th>
          <td>
            <select name="goods_id" class="form-control" required>
              <option value="">-- 商品を選択 --</option>
              @foreach($goods as $item)
                <option value="{{$item->id}}">{{$item->goods_number}} - {{$item->goods_name}}</option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th>移動元倉庫 <span class="text-danger">*</span></th>
          <td>
            <select name="from_warehouse_id" class="form-control" required>
              <option value="">-- 倉庫を選択 --</option>
              @foreach($warehouses as $warehouse)
                <option value="{{$warehouse->id}}">{{$warehouse->warehouse_code}} - {{$warehouse->warehouse_name}}</option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th>移動先倉庫 <span class="text-danger">*</span></th>
          <td>
            <select name="to_warehouse_id" class="form-control" required>
              <option value="">-- 倉庫を選択 --</option>
              @foreach($warehouses as $warehouse)
                <option value="{{$warehouse->id}}">{{$warehouse->warehouse_code}} - {{$warehouse->warehouse_name}}</option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th>数量 <span class="text-danger">*</span></th>
          <td><input type="number" name="quantity" class="form-control" required min="1" value="1"></td>
        </tr>
        <tr>
          <th>移動日時 <span class="text-danger">*</span></th>
          <td><input type="datetime-local" name="movement_date" class="form-control" required value="{{ now()->format('Y-m-d\TH:i') }}"></td>
        </tr>
        <tr>
          <th>備考</th>
          <td><textarea name="notes" class="form-control" rows="3"></textarea></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <th colspan="2" class="t_foot">
            <button type="submit" class="btn btn-info"><i class="fas fa-random"></i> 移動実行</button>
            <a href="{{route('stock_movement_history')}}" class="btn btn-secondary"><i class="fas fa-history"></i> 履歴を見る</a>
          </th>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
@endsection
