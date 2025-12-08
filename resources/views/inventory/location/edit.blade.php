@extends('layouts.parents')
@section('title', 'ロケーション編集')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">ロケーション編集</h3>
  
  <form action="{{route('inventory_location_update', $location->id)}}" method="POST">
    @csrf
    <table class="table table-bordered">
      <tbody>
        <tr>
          <th class="bg-light" style="width: 200px;">倉庫 <span class="text-danger">*</span></th>
          <td>
            <select name="warehouse_id" class="form-control" required>
              <option value="">選択してください</option>
              @foreach($warehouses as $warehouse)
                <option value="{{$warehouse->id}}" @if(old('warehouse_id', $location->warehouse_id) == $warehouse->id) selected @endif>
                  {{$warehouse->warehouse_name}} ({{$warehouse->warehouse_code}})
                </option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th class="bg-light">ロケーションコード <span class="text-danger">*</span></th>
          <td>
            <input type="text" name="location_code" value="{{old('location_code', $location->location_code)}}" class="form-control" required maxlength="50" placeholder="例: A-01-01">
          </td>
        </tr>
        <tr>
          <th class="bg-light">通路</th>
          <td>
            <input type="text" name="aisle" value="{{old('aisle', $location->aisle)}}" class="form-control" maxlength="20" placeholder="例: A">
          </td>
        </tr>
        <tr>
          <th class="bg-light">棚</th>
          <td>
            <input type="text" name="rack" value="{{old('rack', $location->rack)}}" class="form-control" maxlength="20" placeholder="例: 01">
          </td>
        </tr>
        <tr>
          <th class="bg-light">段</th>
          <td>
            <input type="text" name="shelf" value="{{old('shelf', $location->shelf)}}" class="form-control" maxlength="20" placeholder="例: 01">
          </td>
        </tr>
        <tr>
          <th class="bg-light">収容能力</th>
          <td>
            <input type="number" name="capacity" value="{{old('capacity', $location->capacity)}}" class="form-control" min="0" placeholder="例: 100">
          </td>
        </tr>
        <tr>
          <th class="bg-light">有効フラグ</th>
          <td>
            <label>
              <input type="checkbox" name="is_active" value="1" @if(old('is_active', $location->is_active)) checked @endif> 有効
            </label>
          </td>
        </tr>
        <tr>
          <th class="bg-light">登録日時</th>
          <td>{{$location->created_at}}</td>
        </tr>
        <tr>
          <th class="bg-light">更新日時</th>
          <td>{{$location->updated_at}}</td>
        </tr>
      </tbody>
    </table>

    <div class="text-center mt-3">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save"></i> 更新する
      </button>
      <a href="{{route('inventory_location')}}" class="btn btn-secondary btn-lg">
        <i class="fas fa-times"></i> キャンセル
      </a>
    </div>
  </form>
</div>

@endsection
