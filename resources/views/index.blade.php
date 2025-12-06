@extends('layouts.parents')
@section('title', 'EC管理システム')
@section('content')

  <div class="container">
  {{-- 検索条件テーブル --}}
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">商品情報一覧</h3>
  {{-- ソート機能用CSSの読み込み --}}
  <link href="{{asset('public/css/sort.css')}}" rel="stylesheet">
  {{-- エラー表示 --}}
  @if(count($errors) > 0)
    <ul>
      @foreach ($errors->all() as $error)
        <li style="color:#FF0000;">{{ $error }}</li>
      @endforeach
    </ul>
  @endif
  <div id="contents_search">
  <form action="{{route('index')}}">
      <table class="table table_border_radius">
        <thead>
          <tr>
            <th colspan="6">検索条件</th>
          </tr>
        </thead>
        <tbody id="SearchForm">
          <tr>
            <th>商品番号</th>
            <td colspan="2"><input type="text" name="goods_number" value="{{ request()->goods_number }}"></td>
            <th>商品名</th><!-- 入力でセレクトボックス絞り込み->セレクトボックスで選択させる -->
            <td colspan="2">
              <select type="text" name="goods_id" width="360px" id="Select2Form" class="ja-select2" style="width:300px;">
                <option value="" @if(request()->goods_id == "") selected="selected" @endif></option>
                @foreach( $goods_list as $key => $value ) 
                  <option value="{{$value->id}}" @if( request()->goods_id == $value->id ) selected="selected" @endif>【商品番号:{{$value->goods_number}}】{{$value->goods_name}}</option>
                @endforeach
              </select>
            </td>
          </tr>
          <tr>
            <th>金額</th>
            <td colspan="5"><input type="text" name="min_price" value="{{ request()->min_price }}">&nbsp;以上&nbsp;～&nbsp;<input type="text" name="max_price" value="{{ request()->max_price }}">&nbsp;以下</td>
          </tr>
          <tr>
            <th>在庫数</th>
            <td colspan="5"><input type="text" name="min_stock" value="{{ request()->min_stock }}">&nbsp;以上&nbsp;～&nbsp;<input type="text" name="max_stock" value="{{ request()->max_stock }}">&nbsp;以下</td>
          </tr>
          <tr>
            <th>在庫ステータス</th>
            <td colspan="5">
              <select name="stock_status">
                <option value="">すべて</option>
                <option value="out_of_stock" @if(request()->stock_status == "out_of_stock") selected @endif>欠品</option>
                <option value="low_stock" @if(request()->stock_status == "low_stock") selected @endif>低在庫</option>
                <option value="normal" @if(request()->stock_status == "normal") selected @endif>正常</option>
              </select>
            </td>
          </tr>
          <tr>
          <th>更新日時</th>
          <td colspan="5">
            <input style="width:60px;" id="s_up_year" type="text" name="s_up_year" value="{{request()->s_up_year}}">年
            <input style="width:60px;" id="s_up_month" type="text" name="s_up_month" value="{{request()->s_up_month}}" >月
            <input style="width:60px;" id="s_up_day" type="text" name="s_up_day" value="{{request()->s_up_day}}" >日
            <input  type="text" value="" id="s_up_date" style="display:none;" />
            ～&nbsp;
            <input style="width:60px;" id="e_up_year" type="text" name="e_up_year" value="{{request()->e_up_year}}" >年
            <input style="width:60px;" id="e_up_month" type="text" name="e_up_month" value="{{request()->e_up_month}}" >月
            <input style="width:60px;" id="e_up_day" type="text" name="e_up_day" value="{{request()->e_up_day}}" >日
            <input type="text" value="" id="e_up_date" style="display:none;" />
          </td>
          </tr>
          <tr>
          <th>追加日時</th>
          <td colspan="5">
            <input style="width:60px;" id="s_ins_year" type="text" name="s_ins_year" value="{{request()->s_ins_year}}" >年
            <input style="width:60px;" id="s_ins_month" type="text" name="s_ins_month" value="{{request()->s_ins_month}}" >月
            <input style="width:60px;" id="s_ins_day" type="text" name="s_ins_day" value="{{request()->s_ins_day}}" >日
            <input type="text" value=""  id="s_ins_date" style="display:none;" />
            ～&nbsp;
            <input style="width:60px;" id="e_ins_year" type="text" name="e_ins_year" value="{{request()->e_ins_year}}" >年
            <input style="width:60px;" id="e_ins_month" type="text" name="e_ins_month" value="{{request()->e_ins_month}}" >月
            <input style="width:60px;" id="e_ins_day" type="text" name="e_ins_day" value="{{request()->e_ins_day}}" >日
            <input  type="text" value="" id="e_ins_date" style="display:none;" />
          </td>
          </tr>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="6" class="t_foot">
              <input type="submit" style="padding:0px 24px 0px 24px;" value="検索">
              <input type="button" id="ClearButton" value="リセット">
            </th>
          </tr>
        </tfoot>
    </table>
    </form>
  </div>
{{-- <a href="{{ route('goods_add') }}">新規登録</a> --}}
<div class="mb-3">
    <a class="btn btn-primary" href="{{ route('goods_add') }}" role="button">
        <i class="fas fa-plus"></i> 新規登録
    </a>
</div>
{{-- 商品情報一覧 --}}
@if(count($goods_list) > 0)
{{ $goods_list->appends(request()->input())->links('vendor.pagination.bootstrap-4') }}

{{-- モバイル用ソートコントロール --}}
<div class="mobile-sort-controls">
  <label for="mobile-sort">並び替え:</label>
  <select id="mobile-sort" class="mobile-sort-select" onchange="window.location.href=this.value">
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']))) }}">-- 選択してください --</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'price', 'sort_direction' => 'asc'])) }}" {{ request('sort_by') == 'price' && request('sort_direction') == 'asc' ? 'selected' : '' }}>金額 (昇順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'price', 'sort_direction' => 'desc'])) }}" {{ request('sort_by') == 'price' && request('sort_direction') == 'desc' ? 'selected' : '' }}>金額 (降順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'stock', 'sort_direction' => 'asc'])) }}" {{ request('sort_by') == 'stock' && request('sort_direction') == 'asc' ? 'selected' : '' }}>在庫数 (昇順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'stock', 'sort_direction' => 'desc'])) }}" {{ request('sort_by') == 'stock' && request('sort_direction') == 'desc' ? 'selected' : '' }}>在庫数 (降順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'total_available', 'sort_direction' => 'asc'])) }}" {{ request('sort_by') == 'total_available' && request('sort_direction') == 'asc' ? 'selected' : '' }}>利用可能在庫 (昇順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'total_available', 'sort_direction' => 'desc'])) }}" {{ request('sort_by') == 'total_available' && request('sort_direction') == 'desc' ? 'selected' : '' }}>利用可能在庫 (降順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'update', 'sort_direction' => 'asc'])) }}" {{ request('sort_by') == 'update' && request('sort_direction') == 'asc' ? 'selected' : '' }}>更新日付 (昇順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'update', 'sort_direction' => 'desc'])) }}" {{ request('sort_by') == 'update' && request('sort_direction') == 'desc' ? 'selected' : '' }}>更新日付 (降順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'insert', 'sort_direction' => 'asc'])) }}" {{ request('sort_by') == 'insert' && request('sort_direction') == 'asc' ? 'selected' : '' }}>追加日付 (昇順)</option>
    <option value="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'insert', 'sort_direction' => 'desc'])) }}" {{ request('sort_by') == 'insert' && request('sort_direction') == 'desc' ? 'selected' : '' }}>追加日付 (降順)</option>
  </select>
</div>

<div class="table-responsive">
 <form>
    <table class="table table-hover">
    <thead>
      <tr>
        <th class="sort-header">
          <span>商品番号</span>
          <div class="sort-buttons-group placeholder-buttons"></div>
        </th>
        <th class="sort-header">
          <span>商品名</span>
          <div class="sort-buttons-group placeholder-buttons"></div>
        </th>
        <th class="sort-header">
          <span>金額</span>
          <div class="sort-buttons-group">
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'price', 'sort_direction' => 'asc'])) }}" 
               class="sort-button sort-button-asc {{ request('sort_by') == 'price' && request('sort_direction') == 'asc' ? 'active' : '' }}">▲</a>
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'price', 'sort_direction' => 'desc'])) }}" 
               class="sort-button sort-button-desc {{ request('sort_by') == 'price' && request('sort_direction') == 'desc' ? 'active' : '' }}">▼</a>
          </div>
        </th>
        <th class="sort-header">
          <span>在庫数</span>
          <div class="sort-buttons-group">
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'stock', 'sort_direction' => 'asc'])) }}" 
               class="sort-button sort-button-asc {{ request('sort_by') == 'stock' && request('sort_direction') == 'asc' ? 'active' : '' }}">▲</a>
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'stock', 'sort_direction' => 'desc'])) }}" 
               class="sort-button sort-button-desc {{ request('sort_by') == 'stock' && request('sort_direction') == 'desc' ? 'active' : '' }}">▼</a>
          </div>
        </th>
        <th class="sort-header">
          <span>利用可能在庫</span>
          <div class="sort-buttons-group">
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'total_available', 'sort_direction' => 'asc'])) }}" 
               class="sort-button sort-button-asc {{ request('sort_by') == 'total_available' && request('sort_direction') == 'asc' ? 'active' : '' }}">▲</a>
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'total_available', 'sort_direction' => 'desc'])) }}" 
               class="sort-button sort-button-desc {{ request('sort_by') == 'total_available' && request('sort_direction') == 'desc' ? 'active' : '' }}">▼</a>
          </div>
        </th>
        <th class="sort-header">
          <span>在庫ステータス</span>
          <div class="sort-buttons-group placeholder-buttons"></div>
        </th>
        <th class="sort-header">
          <span>更新日付</span>
          <div class="sort-buttons-group">
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'update', 'sort_direction' => 'asc'])) }}" 
               class="sort-button sort-button-asc {{ request('sort_by') == 'update' && request('sort_direction') == 'asc' ? 'active' : '' }}">▲</a>
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'update', 'sort_direction' => 'desc'])) }}" 
               class="sort-button sort-button-desc {{ request('sort_by') == 'update' && request('sort_direction') == 'desc' ? 'active' : '' }}">▼</a>
          </div>
        </th>
        <th class="sort-header">
          <span>追加日付</span>
          <div class="sort-buttons-group">
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'insert', 'sort_direction' => 'asc'])) }}" 
               class="sort-button sort-button-asc {{ request('sort_by') == 'insert' && request('sort_direction') == 'asc' ? 'active' : '' }}">▲</a>
            <a href="{{ route('index', array_merge(request()->except(['sort_by', 'sort_direction', 'page']), ['sort_by' => 'insert', 'sort_direction' => 'desc'])) }}" 
               class="sort-button sort-button-desc {{ request('sort_by') == 'insert' && request('sort_direction') == 'desc' ? 'active' : '' }}">▼</a>
          </div>
        </th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($goods_list as $value)
      <?php 
        // 在庫ステータスを判定
        $stock = $value->total_inventory ?? $value->goods_stock;
        $available = $value->total_available ?? $value->goods_stock;
        $reserved = $value->total_reserved ?? 0;
        $min_level = $value->min_stock_level ?? 10;
        
        if($stock == 0) {
            $status_badge = '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> 欠品</span>';
        } elseif($stock <= $min_level) {
            $status_badge = '<span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> 低在庫</span>';
        } else {
            $status_badge = '<span class="badge badge-success"><i class="fas fa-check-circle"></i> 正常</span>';
        }
      ?>
      <tr> 
          <td data-label="商品番号">{{$value->goods_number}}</td>
          <td data-label="商品名">{{$value->goods_name}}</td>
          <td data-label="金額">{{number_format($value->goods_price)}}円</td>
          <td data-label="在庫数">
            <strong>{{number_format($stock)}}</strong>
            @if($reserved > 0)
              <small class="text-muted">(引当: {{number_format($reserved)}})</small>
            @endif
          </td>
          <td data-label="利用可能在庫">
            <span class="badge badge-info">{{number_format($available)}}</span>
          </td>
          <td data-label="在庫ステータス">{!! $status_badge !!}</td>
          <td data-label="更新日付">{{date('Y年m月d日',strtotime($value->up_date))}}</td>
          <td data-label="追加日付">{{date('Y年m月d日',strtotime($value->ins_date))}}</td>
          <td style="white-space: nowrap;">
            <a class="btn btn-success btn-sm" href="{{route('goods_detail')}}?un_id={{$value->un_id}}" role="button"><i class="fas fa-eye"></i> 詳細</a>
            <a class="btn btn-info btn-sm" href="{{route('goods_edit')}}?un_id={{$value->un_id}}" role="button"><i class="fas fa-edit"></i> 編集</a>
            <a class="btn btn-danger btn-sm" href="{{route('goods_delete')}}?un_id={{$value->un_id}}" role="button"><i class="fas fa-trash"></i> 削除</a>
        </td>
      </tr>
      @endforeach
    </tbody>
    </table>
  </form>
</div><!-- /.table-responsive -->
  {{ $goods_list->appends(request()->input())->links() }}
  @else
    <p style="color:#FF0000;">商品情報がありません</p>
  @endif
  </div>
@endsection
