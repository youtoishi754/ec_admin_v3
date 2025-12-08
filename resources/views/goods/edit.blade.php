@extends('layouts.parents')
@section('title', 'EC管理システム-編集登録')
@section('content')
  <div class="container">
    <nav aria-label="パンくずリスト">
      <ol class="breadcrumb">
        <li class="breadcrumb-item">商品情報一覧</li>
        <li class="breadcrumb-item active" aria-current="page">編集登録</li>
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
  <form action="{{route('goods_edit_do')}}" method="post" class="goods-form" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="un_id" value="{{ $goods_data->un_id }}">
    <table class="table table-hover">
      <tr>
        <th>カテゴリ <span class="text-danger">*</span></th>
        <td>
          <select name="category_id" id="category_select" class="form-control" required>
            <option value="">-- カテゴリを選択してください --</option>
            @foreach($categories as $parent)
              <optgroup label="【{{ $parent->category_name }}】">
                @foreach($parent->children as $child)
                  <option value="{{ $child->id }}" 
                    @if(isset($goods_data->category_id) && $goods_data->category_id == $child->id) selected @endif>
                    {{ $child->category_name }} ({{ $child->category_code }})
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          </select>
          <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> カテゴリを変更すると商品の分類が変わります
          </small>
        </td>
      </tr>
      <tr>
        <th>商品番号</th>
        <td>
          <input type="text" name="goods_number" class="form-control" value="{{ $goods_data->goods_number }}" readonly style="background-color: #e9ecef;">
          <small class="form-text text-muted">
            <i class="fas fa-lock"></i> 商品番号は変更できません
          </small>
        </td>
      </tr>
      <tr>
        <th>商品名 <span class="text-danger">*</span></th>
        <td><input type="text" name="goods_name" class="form-control" value="{{ $goods_data->goods_name }}" required></td>
      </tr>
      <tr>
        <th>商品画像</th>
        <td>
          <div class="form-group">
            @if($goods_data->image_path)
              <div id="current_image" class="mb-3">
                <p><strong><i class="fas fa-image"></i> 現在の画像:</strong></p>
                <div class="current-image-container">
                  <img src="{{ asset($goods_data->image_path) }}" alt="現在の商品画像" class="img-thumbnail">
                </div>
                <div class="custom-control custom-checkbox mt-2">
                  <input type="checkbox" class="custom-control-input" id="delete_image" name="delete_image" value="1" onchange="toggleDeleteWarning(this)">
                  <label class="custom-control-label text-danger" for="delete_image">
                    <i class="fas fa-trash-alt"></i> この画像を削除
                  </label>
                </div>
                <div id="delete_warning" class="alert alert-warning mt-2" style="display:none;">
                  <i class="fas fa-exclamation-triangle"></i> 保存すると画像が削除されます
                </div>
              </div>
            @endif
            <label for="image_file" class="btn btn-primary btn-lg">
              <i class="fas fa-cloud-upload-alt"></i> {{ $goods_data->image_path ? '画像を変更' : '画像を選択' }}
            </label>
            <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/jpg" style="display:none;" onchange="previewImage(event)">
            <div id="image_preview" class="mt-3"></div>
            <small class="form-text text-muted">
              <i class="fas fa-info-circle"></i> 推奨サイズ: 800x800px以上 | JPG/PNG形式 | 最大5MB
            </small>
          </div>
        </td>
      </tr>
      <tr>
        <th>金額</th>
        <td><input type="text" name="goods_price" value="{{ $goods_data->goods_price }}"></td>
      </tr>
      <tr>
        <th>在庫数（参考）</th>
        <td>
          <div class="alert alert-info mb-0">
            <strong>実在庫数: {{ number_format($goods_data->total_inventory ?? $goods_data->goods_stock) }}</strong>
            （利用可能: {{ number_format($goods_data->total_available ?? $goods_data->goods_stock) }}、
            引当済: {{ number_format($goods_data->total_reserved ?? 0) }}）
            <br>
            <small>※在庫数は「在庫管理」メニューから入出庫操作で変更してください</small>
          </div>
          <input type="hidden" name="goods_stock" value="{{ $goods_data->goods_stock }}">
        </td>
      </tr>
      <tr>
        <th>紹介文</th>
        <td><textarea class="form-control" name="intro_txt" row="1">{{ $goods_data->intro_txt }}</textarea>
      </tr>
      <tr>
        <th>表示</th>
        <td>
        <input type="radio" name="disp_flg" value="1" id="true_flg" @if($goods_data->disp_flg == 1) checked=checked @endif><label for="true_flg">表示</label>
        <input type="radio" name="disp_flg" value="0" id="false_flg" @if($goods_data->disp_flg == 0) checked=checked @endif><label for="false_flg">非表示</label>
        </td>
      </tr>
    </table> 
    <div class="text-center">
        <button type="submit" class="btn btn-primary btn-lg">
          <i class="fas fa-save"></i> 変更を保存する
        </button>
        <a href="{{ route('goods_detail', ['un_id' => $goods_data->un_id]) }}" class="btn btn-secondary btn-lg">
          <i class="fas fa-times"></i> キャンセル
        </a>
    </div>
  </form>
</div>

<style>
.current-image-container img {
  max-width: 400px;
  max-height: 400px;
  border: 2px solid #6c757d;
  border-radius: 8px;
  transition: all 0.3s;
}

.current-image-container img.faded {
  opacity: 0.3;
  filter: grayscale(50%);
}

.preview-container {
  position: relative;
  display: inline-block;
  animation: fadeIn 0.3s ease-in;
}

.preview-container img {
  max-width: 400px;
  max-height: 400px;
  border: 3px solid #007bff;
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
  background-color: #e7f3ff;
  border-left: 4px solid #007bff;
  border-radius: 4px;
  font-size: 0.9em;
}

.badge-new {
  display: inline-block;
  padding: 5px 10px;
  background-color: #007bff;
  color: white;
  border-radius: 4px;
  font-size: 0.8em;
  margin-left: 10px;
}
</style>

<script>
function toggleDeleteWarning(checkbox) {
  const warning = document.getElementById('delete_warning');
  if (checkbox.checked) {
    warning.style.display = 'block';
  } else {
    warning.style.display = 'none';
  }
}

function previewImage(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('image_preview');
  const currentImage = document.getElementById('current_image');
  
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
  
  // 現在の画像を薄く表示
  if (currentImage) {
    const currentImg = currentImage.querySelector('img');
    if (currentImg) {
      currentImg.classList.add('faded');
    }
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
          <p><strong><i class="fas fa-star"></i> 新しい画像のプレビュー</strong><span class="badge-new">NEW</span></p>
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
    // 現在の画像を元に戻す
    if (currentImage) {
      const currentImg = currentImage.querySelector('img');
      if (currentImg) {
        currentImg.classList.remove('faded');
      }
    }
  };
  
  reader.readAsDataURL(file);
}
</script>
@endsection
