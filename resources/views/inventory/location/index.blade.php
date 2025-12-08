@extends('layouts.parents')
@section('title', 'ロケーション管理')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">ロケーション一覧</h3>
  
  {{-- 新規登録ボタン --}}
  <div class="mb-3">
    <a class="btn btn-primary" href="{{ route('inventory_location_create') }}">
      <i class="fas fa-plus"></i> 新規ロケーション登録
    </a>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{route('inventory_location')}}" method="GET">
      <table class="table table_border_radius">
        <thead>
          <tr>
            <th colspan="6">検索条件</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>倉庫</th>
            <td>
              <select name="warehouse_id" class="form-control">
                <option value="">全て</option>
                @foreach($warehouses as $warehouse)
                  <option value="{{$warehouse->id}}" @if(request()->warehouse_id == $warehouse->id) selected @endif>
                    {{$warehouse->warehouse_name}} ({{$warehouse->warehouse_code}})
                  </option>
                @endforeach
              </select>
            </td>
            <th>ロケーションコード</th>
            <td><input type="text" name="location_code" value="{{ request()->location_code }}" class="form-control"></td>
            <th>有効フラグ</th>
            <td>
              <select name="is_active" class="form-control">
                <option value="">全て</option>
                <option value="1" @if(request()->is_active === "1") selected @endif>有効</option>
                <option value="0" @if(request()->is_active === "0") selected @endif>無効</option>
              </select>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{route('inventory_location')}}'"><i class="fas fa-undo"></i> リセット</button>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- ロケーション一覧テーブル --}}
  @if(count($locations) > 0)
  {{ $locations->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="thead-light">
        <tr>
          <th>倉庫コード</th>
          <th>倉庫名</th>
          <th>ロケーションコード</th>
          <th>通路</th>
          <th>棚</th>
          <th>段</th>
          <th>収容能力</th>
          <th>在庫アイテム数</th>
          <th>総在庫数</th>
          <th>状態</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($locations as $location)
        <tr>
          <td>{{$location->warehouse_code}}</td>
          <td>{{$location->warehouse_name}}</td>
          <td><strong>{{$location->location_code}}</strong></td>
          <td>{{$location->aisle ?? '-'}}</td>
          <td>{{$location->rack ?? '-'}}</td>
          <td>{{$location->shelf ?? '-'}}</td>
          <td class="text-right">{{ $location->capacity ? number_format($location->capacity) : '-' }}</td>
          <td class="text-right">
            {{ isset($inventory_counts[$location->id]) ? number_format($inventory_counts[$location->id]->goods_count) : 0 }}
          </td>
          <td class="text-right">
            {{ isset($inventory_counts[$location->id]) ? number_format($inventory_counts[$location->id]->total_quantity) : 0 }}
          </td>
          <td>
            @if($location->is_active)
              <span class="badge badge-success">有効</span>
            @else
              <span class="badge badge-secondary">無効</span>
            @endif
          </td>
          <td>
            <a href="{{route('inventory_location_edit', $location->id)}}" class="btn btn-sm btn-info">
              <i class="fas fa-edit"></i> 編集
            </a>
            <form action="{{route('inventory_location_delete', $location->id)}}" method="POST" style="display:inline;">
              @csrf
              <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('本当に削除しますか？\n在庫が存在する場合は削除できません。')">
                <i class="fas fa-trash"></i> 削除
              </button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $locations->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> ロケーションデータが見つかりませんでした。
  </div>
  @endif
</div>

@endsection
