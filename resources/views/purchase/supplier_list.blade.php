@extends('layouts.parents')
@section('title', '仕入先管理')
@section('content')

<div class="container-fluid">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">仕入先管理</h3>
  
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- 統計ダッシュボード --}}
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-building"></i> 総仕入先数</h5>
          <h2>{{ number_format($stats['total_suppliers']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-check-circle"></i> 有効</h5>
          <h2>{{ number_format($stats['active_suppliers']) }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-white bg-secondary">
        <div class="card-body">
          <h5 class="card-title"><i class="fas fa-pause-circle"></i> 無効</h5>
          <h2>{{ number_format($stats['inactive_suppliers']) }}</h2>
        </div>
      </div>
    </div>
  </div>

  {{-- 検索条件 --}}
  <div id="contents_search">
    <form action="{{ route('supplier_list') }}" method="GET">
      <table class="table table_border_radius">
        <thead>
          <tr>
            <th colspan="6">検索条件</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th>仕入先コード</th>
            <td><input type="text" name="supplier_code" value="{{ request()->supplier_code }}" class="form-control"></td>
            <th>仕入先名</th>
            <td><input type="text" name="supplier_name" value="{{ request()->supplier_name }}" class="form-control"></td>
            <th>ステータス</th>
            <td>
              <select name="is_active" class="form-control">
                <option value="">全て</option>
                <option value="active" @if(request()->is_active == 'active') selected @endif>有効</option>
                <option value="inactive" @if(request()->is_active == 'inactive') selected @endif>無効</option>
              </select>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> 検索</button>
              <button type="button" class="btn btn-secondary" onclick="location.href='{{ route('supplier_list') }}'"><i class="fas fa-undo"></i> リセット</button>
              <span class="ml-3">|</span>
              <a href="{{ route('supplier_export_csv', request()->all()) }}" class="btn btn-outline-success ml-2"><i class="fas fa-file-csv"></i> CSV出力</a>
              <a href="{{ route('supplier_create') }}" class="btn btn-success float-right"><i class="fas fa-plus"></i> 新規仕入先登録</a>
            </th>
          </tr>
        </tfoot>
      </table>
    </form>
  </div>

  {{-- 仕入先一覧 --}}
  @if(count($suppliers) > 0)
  {{ $suppliers->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

  <div class="table-responsive">
    <table class="table table-hover table-bordered">
      <thead class="thead-light">
        <tr>
          <th>仕入先コード</th>
          <th>仕入先名</th>
          <th>担当者</th>
          <th>電話番号</th>
          <th>メール</th>
          <th class="text-right">取扱商品数</th>
          <th class="text-right">発注回数</th>
          <th class="text-right">発注総額</th>
          <th>ステータス</th>
          <th width="100">操作</th>
        </tr>
      </thead>
      <tbody>
        @foreach($suppliers as $supplier)
        <tr>
          <td>{{ $supplier->supplier_code }}</td>
          <td><a href="{{ route('supplier_edit', ['id' => $supplier->id]) }}">{{ $supplier->supplier_name }}</a></td>
          <td>{{ $supplier->contact_person ?? '-' }}</td>
          <td>{{ $supplier->contact_phone ?? '-' }}</td>
          <td>{{ $supplier->contact_email ?? '-' }}</td>
          <td class="text-right">{{ number_format($supplier->goods_count) }}</td>
          <td class="text-right">{{ number_format($supplier->order_count) }}</td>
          <td class="text-right">¥{{ number_format($supplier->total_amount ?? 0) }}</td>
          <td>
            @if($supplier->is_active)
            <span class="badge badge-success">有効</span>
            @else
            <span class="badge badge-secondary">無効</span>
            @endif
          </td>
          <td>
            <a href="{{ route('supplier_edit', ['id' => $supplier->id]) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
            @if($supplier->order_count == 0 && $supplier->goods_count == 0)
            <form action="{{ route('supplier_delete', ['id' => $supplier->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('この仕入先を削除してもよろしいですか？');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
            </form>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{ $suppliers->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}
  @else
  <div class="alert alert-info">
    <i class="fas fa-info-circle"></i> 仕入先が登録されていません。
  </div>
  @endif
</div>

@endsection
