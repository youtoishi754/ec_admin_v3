@extends('layouts.parents')
@section('title', 'EC管理システム-新規登録')
@section('content')
<div class="container">
  <nav aria-label="パンくずリスト">
    <ol class="breadcrumb">
      <li class="breadcrumb-item">商品情報一覧</li>
      <li class="breadcrumb-item active" aria-current="page">新規登録</li>
    </ol>
  </nav>
  {{-- 見出し --}}
  <h3 style="border-bottom: 1px solid #000;border-left: 10px solid #000;padding: 7px;">商品情報入力</h3>
    {{-- エラー表示 --}}
    @if(count($errors) > 0)
      <ul>
        @foreach ($errors->all() as $error)
            <li style="color:#FF0000;">{{ $error }}</li>
        @endforeach
      </ul>
    @endif
  {{-- 商品情報入力フォーム --}}
  <form action="{{route('goods_add_do')}}" method="post" class="goods-form" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{csrf_token()}}"> 
    <input type="hidden" id="goods_number" name="goods_number" value="{{ request()->goods_number }}">
    <table class="table table-hover">
      <tr>
        <th>カテゴリ <span class="text-danger">*</span></th>
        <td>
          <select name="category_id" id="category_select" class="form-control" required onchange="updateGoodsNumber()">
            <option value="">-- カテゴリを選択してください --</option>
            @foreach($categories as $parent)
              <optgroup label="【{{ $parent->category_name }}】">
                @foreach($parent->children as $child)
                  <option value="{{ $child->id }}" data-code="{{ $child->category_code }}" 
                    {{ request()->category_id == $child->id ? 'selected' : '' }}>
                    {{ $child->category_name }} ({{ $child->category_code }})
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          </select>
          <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> カテゴリを選択すると商品番号が自動生成されます
          </small>
        </td>
      </tr>
      <tr>
        <th>商品番号</th>
        <td>
          <input type="text" id="goods_number_display" class="form-control" value="{{ request()->goods_number }}" readonly style="background-color: #e9ecef;">
          <small class="form-text text-muted">
            <i class="fas fa-lock"></i> 自動生成されます（カテゴリ選択後）
          </small>
        </td>
      </tr>
      <tr>
        <th>商品名 <span class="text-danger">*</span></th>
        <td><input type="text" name="goods_name" class="form-control" value="{{ request()->goods_name }}" required></td>
      </tr>
      <tr>
        <th>商品画像</th>
        <td>
          <div class="form-group">
            <label for="image_file" class="btn btn-primary btn-lg">
              <i class="fas fa-cloud-upload-alt"></i> 画像を選択
            </label>
            <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/jpg" style="display:none;" onchange="previewImage(event)">
            <div id="image_preview" class="mt-3">
              @if(request()->image_path)
                <div class="preview-container">
                  <img src="{{ request()->image_path }}" alt="商品画像" class="img-thumbnail">
                  <p class="text-muted mt-2"><i class="fas fa-check-circle text-success"></i> 画像が選択されています</p>
                </div>
              @endif
            </div>
            <small class="form-text text-muted">
              <i class="fas fa-info-circle"></i> 推奨サイズ: 800x800px以上 | JPG/PNG形式 | 最大5MB
            </small>
          </div>
        </td>
      </tr>
      <tr>
        <th>金額</th>
        <td><input type="text" name="goods_price" value="{{ request()->goods_price }}"></td>
      </tr>
      <tr>
        <th>個数</th>
        <td><input type="text" name="goods_stock" value="{{ request()->goods_stock }}"></td>
      </tr>
      <tr>
        <th>紹介文</th>
      <td><textarea class="form-control" name="intro_txt" rows="8">{!! nl2br(e(request()->intro_txt)) !!}</textarea>
      </tr>
      <tr>
        <th>表示</th>
        <td>
          <input type="radio" name="disp_flg" value="0" id="true_flg" @if(request()->disp_flg == 0) checked=checked @endif><label for="true_flg">表示</label>
          <input type="radio" name="disp_flg" value="1" id="false_flg" @if(request()->disp_flg == 1) checked=checked @endif><label for="false_flg">非表示</label>
        </td>
      </tr>
    </table> 
    <div class="text-center">
        <button type="submit" class="btn btn-primary btn-lg">
          <i class="fas fa-save"></i> 商品を登録する
        </button>
        <a href="{{ route('index') }}" class="btn btn-secondary btn-lg">
          <i class="fas fa-times"></i> キャンセル
        </a>
    </div>
  </form>
</div>

<style>
.preview-container {
  position: relative;
  display: inline-block;
  animation: fadeIn 0.3s ease-in;
}

.preview-container img {
  max-width: 400px;
  max-height: 400px;
  border: 3px solid #28a745;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  transition: transform 0.2s;
}

.preview-container img:hover {
  transform: scale(1.02);
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.file-info {
  margin-top: 10px;
  padding: 10px;
  background-color: #f8f9fa;
  border-radius: 4px;
  font-size: 0.9em;
}
</style>

<script>
// カテゴリ選択時に商品番号を自動生成
function updateGoodsNumber() {
  const select = document.getElementById('category_select');
  const selectedOption = select.options[select.selectedIndex];
  const categoryCode = selectedOption.getAttribute('data-code');
  
  if (categoryCode) {
    // Ajax で商品番号を取得
    fetch('{{ route("goods_generate_number") }}?category_code=' + categoryCode)
      .then(response => response.json())
      .then(data => {
        if (data.goods_number) {
          document.getElementById('goods_number').value = data.goods_number;
          document.getElementById('goods_number_display').value = data.goods_number;
          
          // 成功メッセージを一時表示
          const display = document.getElementById('goods_number_display');
          const originalBg = display.style.backgroundColor;
          display.style.backgroundColor = '#d4edda';
          display.style.borderColor = '#28a745';
          setTimeout(() => {
            display.style.backgroundColor = originalBg;
            display.style.borderColor = '';
          }, 1000);
        }
      })
      .catch(error => {
        console.error('商品番号の生成に失敗しました:', error);
        alert('商品番号の生成に失敗しました。');
      });
  } else {
    document.getElementById('goods_number').value = '';
    document.getElementById('goods_number_display').value = '';
  }
}

function previewImage(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('image_preview');
  
  if (!file) {
    return;
  }
  
  // ファイルタイプチェック
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
  if (!allowedTypes.includes(file.type)) {
    alert('❌ JPGまたはPNG形式の画像のみアップロード可能です。');
    event.target.value = '';
    return;
  }
  
  // ファイルサイズチェック（5MB）
  const maxSize = 5 * 1024 * 1024;
  if (file.size > maxSize) {
    const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
    alert(`❌ 画像サイズが大きすぎます。\n選択: ${sizeMB}MB\n最大: 5MB`);
    event.target.value = '';
    return;
  }
  
  // ローディング表示
  preview.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-2">画像を読み込み中...</p></div>';
  
  // 画像プレビュー表示
  const reader = new FileReader();
  
  reader.onload = function(e) {
    const img = new Image();
    img.onload = function() {
      const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
      preview.innerHTML = `
        <div class="preview-container">
          <img src="${e.target.result}" alt="プレビュー" class="img-thumbnail">
          <div class="file-info">
            <p class="mb-1"><i class="fas fa-check-circle text-success"></i> <strong>画像が選択されました</strong></p>
            <p class="mb-1"><i class="fas fa-file-image"></i> ファイル名: ${file.name}</p>
            <p class="mb-1"><i class="fas fa-ruler-combined"></i> サイズ: ${img.width} × ${img.height}px</p>
            <p class="mb-0"><i class="fas fa-hdd"></i> 容量: ${sizeMB}MB</p>
          </div>
        </div>
      `;
    };
    img.src = e.target.result;
  };
  
  reader.onerror = function() {
    alert('❌ 画像の読み込みに失敗しました。');
    preview.innerHTML = '';
    event.target.value = '';
  };
  
  reader.readAsDataURL(file);
}
</script>
@endsection
