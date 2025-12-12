@extends('layouts.parents')
@section('title', '仕入先編集')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">仕入先編集 - {{ $supplier->supplier_name }}</h3>
  
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

  <form action="{{ route('supplier_update', ['id' => $supplier->id]) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-building"></i> 基本情報</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>仕入先コード <span class="text-danger">*</span></label>
              <input type="text" name="supplier_code" class="form-control" value="{{ old('supplier_code', $supplier->supplier_code) }}" required maxlength="20">
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <label>仕入先名 <span class="text-danger">*</span></label>
              <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name', $supplier->supplier_name) }}" required maxlength="100">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-address-card"></i> 連絡先情報</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>担当者名</label>
              <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person) }}" maxlength="50">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>電話番号</label>
              <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $supplier->contact_phone) }}" maxlength="20">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>FAX</label>
              <input type="text" name="fax" class="form-control" value="{{ old('fax', $supplier->fax) }}" maxlength="20">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>メールアドレス</label>
              <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $supplier->contact_email) }}" maxlength="100">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>郵便番号</label>
              <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $supplier->postal_code) }}" maxlength="10">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>住所</label>
              <input type="text" name="address" class="form-control" value="{{ old('address', $supplier->address) }}" maxlength="255">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-cog"></i> 取引条件</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>支払条件</label>
              <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms', $supplier->payment_terms) }}" maxlength="100">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>リードタイム（日数）</label>
              <input type="number" name="lead_time_days" class="form-control" value="{{ old('lead_time_days', $supplier->lead_time_days) }}" min="0">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>最低発注金額</label>
              <input type="number" name="minimum_order_amount" class="form-control" value="{{ old('minimum_order_amount', $supplier->minimum_order_amount) }}" min="0">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>備考</label>
              <textarea name="notes" class="form-control" rows="3">{{ old('notes', $supplier->notes) }}</textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>ステータス</label>
              <select name="is_active" class="form-control">
                <option value="1" {{ old('is_active', $supplier->is_active) == 1 ? 'selected' : '' }}>有効</option>
                <option value="0" {{ old('is_active', $supplier->is_active) == 0 ? 'selected' : '' }}>無効</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mb-4">
      <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> 更新</button>
      <a href="{{ route('supplier_list') }}" class="btn btn-secondary btn-lg"><i class="fas fa-arrow-left"></i> 一覧に戻る</a>
    </div>
  </form>

  {{-- 取扱商品一覧 --}}
  @if(count($goods) > 0)
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0"><i class="fas fa-box"></i> 取扱商品（{{ count($goods) }}件）</h5>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>商品番号</th>
            <th>商品名</th>
            <th class="text-right">単価</th>
          </tr>
        </thead>
        <tbody>
          @foreach($goods as $g)
          <tr>
            <td>{{ $g->goods_number }}</td>
            <td>{{ $g->goods_name }}</td>
            <td class="text-right">¥{{ number_format($g->goods_price) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif

  {{-- 発注履歴 --}}
  @if(count($orders) > 0)
  <div class="card mb-4">
    <div class="card-header">
      <h5 class="mb-0"><i class="fas fa-history"></i> 発注履歴（最新10件）</h5>
    </div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th>発注番号</th>
            <th>発注日</th>
            <th class="text-right">金額</th>
            <th>ステータス</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orders as $order)
          @php
            $statusLabels = ['draft' => '下書き', 'pending' => '承認待ち', 'ordered' => '発注済み', 'received' => '入荷完了', 'cancelled' => 'キャンセル'];
          @endphp
          <tr>
            <td><a href="{{ route('purchase_order_edit', ['id' => $order->id]) }}">{{ $order->order_number }}</a></td>
            <td>{{ $order->order_date }}</td>
            <td class="text-right">¥{{ number_format($order->total_amount) }}</td>
            <td>{{ $statusLabels[$order->status] ?? $order->status }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</div>

@endsection
