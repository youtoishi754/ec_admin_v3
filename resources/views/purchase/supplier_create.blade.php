@extends('layouts.parents')
@section('title', '仕入先登録')
@section('content')

<div class="container">
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;margin-top:10px;">仕入先登録</h3>
  
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

  <form action="{{ route('supplier_store') }}" method="POST">
    @csrf
    
    <div class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-building"></i> 基本情報</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>仕入先コード <span class="text-danger">*</span></label>
              <input type="text" name="supplier_code" class="form-control" value="{{ old('supplier_code') }}" required maxlength="20">
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              <label>仕入先名 <span class="text-danger">*</span></label>
              <input type="text" name="supplier_name" class="form-control" value="{{ old('supplier_name') }}" required maxlength="100">
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
              <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person') }}" maxlength="50">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>電話番号</label>
              <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone') }}" maxlength="20">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>FAX</label>
              <input type="text" name="fax" class="form-control" value="{{ old('fax') }}" maxlength="20">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>メールアドレス</label>
              <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email') }}" maxlength="100">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>郵便番号</label>
              <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code') }}" maxlength="10">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>住所</label>
              <input type="text" name="address" class="form-control" value="{{ old('address') }}" maxlength="255">
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
              <input type="text" name="payment_terms" class="form-control" value="{{ old('payment_terms') }}" maxlength="100" placeholder="例: 月末締め翌月末払い">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>リードタイム（日数）</label>
              <input type="number" name="lead_time_days" class="form-control" value="{{ old('lead_time_days') }}" min="0">
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              <label>最低発注金額</label>
              <input type="number" name="minimum_order_amount" class="form-control" value="{{ old('minimum_order_amount') }}" min="0">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label>備考</label>
              <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label>ステータス</label>
              <select name="is_active" class="form-control">
                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>有効</option>
                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>無効</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="text-center mb-4">
      <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> 登録</button>
      <a href="{{ route('supplier_list') }}" class="btn btn-secondary btn-lg"><i class="fas fa-times"></i> キャンセル</a>
    </div>
  </form>
</div>

@endsection
