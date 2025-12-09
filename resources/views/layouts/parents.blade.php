<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title>@yield('title')</title>

  {{-- BootstrapベースCSSファイル --}}
  <link href="{{asset('public/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

  {{-- ページレイアウト関連テンプレートCSSファイル --}}
  <link href="{{asset('public/css/modern-business.css')}}" rel="stylesheet">

  <link rel="stylesheet" href="{{ asset('public/css/common.css') }}">

  {{-- カレンダーのCSSファイル --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css">
  
  {{-- Font Awesome --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
  {{-- 商品管理一覧 --}}
  <link href="{{asset('public/css/goods.css')}}" rel="stylesheet">

  {{-- jQueryベースライブラリ --}}
  <script src="{{asset('public/vendor/jquery/jquery.min.js')}}"></script>

  {{-- カレンダーライブラリ --}}
  <script src="{{asset('public/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

  <!-- Select2.css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css">

  <!-- Select2本体 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

  <!-- Select2日本語化 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/i18n/ja.js"></script>

  <script>
  $(function()
  {
    $('.ja-select2').select2
    ({
      language: "ja" //日本語化
    });
    
    {{-- 日本語化 --}}
    $.datepicker.regional['ja'] = 
    {
      closeText: '閉じる',
      prevText: '<前',
      nextText: '次>',
      currentText: '今日',
      monthNames: ['1月','2月','3月','4月','5月','6月',
      '7月','8月','9月','10月','11月','12月'],
      monthNamesShort: ['1月','2月','3月','4月','5月','6月',
      '7月','8月','9月','10月','11月','12月'],
      dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
      dayNamesShort: ['日','月','火','水','木','金','土'],
      dayNamesMin: ['日','月','火','水','木','金','土'],
      weekHeader: '週',
      dateFormat: 'yy/mm/dd',
      firstDay: 0,
      changeYear: true,  // 年選択をプルダウン化
      changeMonth: true,  // 月選択をプルダウン化
      isRTL: false,
      showMonthAfterYear: true,
      yearSuffix: '年'
    };
    $.datepicker.setDefaults($.datepicker.regional['ja']);

    {{-- 指定したテキストボックスにカレンダー表示 --}}
    $("#s_up_date").datepicker
    ({
      buttonImage: "{{asset('public/css/icon_calendar.png')}}",
      buttonText: "カレンダーから選択",
      buttonImageOnly: true,
      showOn: "both",
      beforeShow : function(input,inst)
      {
        //開く前に日付を上書き
        var year = $(this).parent().find("#s_up_year").val();
        var month = $(this).parent().find("#s_up_month").val();
        var date = $(this).parent().find("#s_up_day").val();
        $(this).datepicker( "setDate" , year + "/" + month + "/" + date)
      },
      onSelect: function(dateText, inst)
      {
        //カレンダー確定時にフォームに反映
        var dates = dateText.split('/');
        $(this).parent().find("#s_up_year").val(dates[0]);
        $(this).parent().find("#s_up_month").val(dates[1]);
        $(this).parent().find("#s_up_day").val(dates[2]);
      }
    });

    $("#e_up_date").datepicker
    ({
      buttonImage: "{{asset('public/css/icon_calendar.png')}}",        
      buttonText: "カレンダーから選択", 
      buttonImageOnly: true,           
      showOn: "both",
      beforeShow : function(input,inst)
      {
      //開く前に日付を上書き
      var year = $(this).parent().find("#e_up_year").val();
      var month = $(this).parent().find("#e_up_month").val();
      var date = $(this).parent().find("#e_up_day").val();
      $(this).datepicker( "setDate" , year + "/" + month + "/" + date)
      },
      onSelect: function(dateText, inst)
      {
        //カレンダー確定時にフォームに反映
        var dates = dateText.split('/');
        $(this).parent().find("#e_up_year").val(dates[0]);
        $(this).parent().find("#e_up_month").val(dates[1]);
        $(this).parent().find("#e_up_day").val(dates[2]);
      }                   
    });

    $("#s_ins_date").datepicker
    ({
      buttonImage: "{{asset('public/css/icon_calendar.png')}}",        
      buttonText: "カレンダーから選択", 
      buttonImageOnly: true,           
      showOn: "both",
      beforeShow : function(input,inst)
      {
        //開く前に日付を上書き
        var year = $(this).parent().find("#s_ins_year").val();
        var month = $(this).parent().find("#s_ins_month").val();
        var date = $(this).parent().find("#s_ins_day").val();
        $(this).datepicker( "setDate" , year + "/" + month + "/" + date)
      },
      onSelect: function(dateText, inst)
      {
        //カレンダー確定時にフォームに反映
        var dates = dateText.split('/');
        $(this).parent().find("#s_ins_year").val(dates[0]);
        $(this).parent().find("#s_ins_month").val(dates[1]);
        $(this).parent().find("#s_ins_day").val(dates[2]);
      }                   
    });

    $("#e_ins_date").datepicker
    ({
      buttonImage: "{{asset('public/css/icon_calendar.png')}}",       
      buttonText: "カレンダーから選択", 
      buttonImageOnly: true,           
      showOn: "both",
      beforeShow : function(input,inst)
      {
        //開く前に日付を上書き
        var year = $(this).parent().find("#e_ins_year").val();
        var month = $(this).parent().find("#e_ins_month").val();
        var date = $(this).parent().find("#e_ins_day").val();
        $(this).datepicker( "setDate" , year + "/" + month + "/" + date)
      },
      onSelect: function(dateText, inst)
      {
        //カレンダー確定時にフォームに反映
        var dates = dateText.split('/');
        $(this).parent().find("#e_ins_year").val(dates[0]);
        $(this).parent().find("#e_ins_month").val(dates[1]);
        $(this).parent().find("#e_ins_day").val(dates[2]);
      }                   
    });
  });

  {{-- フォームのアクションを動的変更する --}}
  function submitAction(value,method) 
  {
    $('form').attr('action', value);

    if(method == 'get')
    { 
      $('form').attr("method","GET");
    }
    else if(method == 'post')
    {
      $('form').attr("method","POST");
    }

    $('form').submit();
  }

  $(function()
  {
    $('#ClearButton').click(function()
    {
      $('#SearchForm input, #SearchForm select').each(function()
      {
        //checkboxまたはradioボタンの時
        if(this.type == 'checkbox' || this.type == 'radio')
        {
          //一律でチェックを外す
          this.checked = false;
        }
        else
        {
          //checkboxまたはradioボタンまたはselect以外の時
          // val値を空にする
          $(this).val('');
          $("select option:selected").select2({width: "100%"});
        }
      });  
    });
  });
  </script>
  <style>
  {{-- コンテナのスタイル --}}
  html
  {
    height: 100%;
  }
  body
  {
    min-height: 100%;
    display: flex;
    flex-direction: column;
  }
  .container
  {
    flex:1;
  } 
  </style>
  <style>
  {{-- カレンダーアイコンのスタイル --}}
  img.ui-datepicker-trigger
  {
    cursor: pointer;
    margin-left: 5px!important;
    margin-right: 5px!important;
    vertical-align: middle;
  }
  </style>
  <style>
  {{-- ナビゲーションメニューのスタイル --}}
  .navbar {
    box-shadow: 0 2px 4px rgba(0,0,0,.1);
  }
  .navbar-brand {
    font-weight: bold;
    font-size: 1.3rem;
  }
  .navbar-brand i {
    color: #ffc107;
  }
  .nav-link {
    font-weight: 500;
    padding: 0.5rem 1rem !important;
  }
  .nav-link:hover {
    background-color: rgba(255,255,255,.1);
    border-radius: 4px;
  }
  .dropdown-menu {
    border: none;
    box-shadow: 0 4px 6px rgba(0,0,0,.1);
    margin-top: 0.5rem;
  }
  .dropdown-item {
    padding: 0.5rem 1.5rem;
  }
  .dropdown-item:hover {
    background-color: #f8f9fa;
  }
  .dropdown-item i {
    width: 20px;
    text-align: center;
    margin-right: 8px;
  }
  body {
    padding-top: 70px;
  }
  </style>
</head>
<body>
 {{-- ナビゲーション --}}
 <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="{{route('index')}}">
        <i class="fas fa-warehouse"></i> EC在庫管理システム
      </a>
      
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarMain">
        <ul class="navbar-nav mr-auto">
          {{-- ダッシュボード --}}
          <li class="nav-item">
            <a class="nav-link" href="{{route('index')}}">
              <i class="fas fa-home"></i> ダッシュボード
            </a>
          </li>

          {{-- 商品管理 --}}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarProducts" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-box"></i> 商品管理
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarProducts">
              <a class="dropdown-item" href="{{route('index')}}">
                <i class="fas fa-list"></i> 商品一覧
              </a>
              <a class="dropdown-item" href="{{route('goods_add')}}">
                <i class="fas fa-plus"></i> 商品登録
              </a>
            </div>
          </li>

          {{-- 在庫管理 --}}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarInventory" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-warehouse"></i> 在庫管理
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarInventory">
              <a class="dropdown-item" href="{{route('inventory')}}">
                <i class="fas fa-chart-line"></i> リアルタイム在庫
              </a>
              <a class="dropdown-item" href="{{route('inventory_alert')}}">
                <i class="fas fa-exclamation-triangle"></i> 在庫アラート
              </a>
              <a class="dropdown-item" href="{{route('inventory_location')}}">
                <i class="fas fa-map-marker-alt"></i> ロケーション管理
              </a>
              <a class="dropdown-item" href="{{route('inventory_lot')}}">
                <i class="fas fa-barcode"></i> ロット管理
              </a>
              <a class="dropdown-item" href="{{route('inventory_serial')}}">
                <i class="fas fa-hashtag"></i> シリアル番号管理
              </a>
              <a class="dropdown-item" href="{{route('inventory_expiry')}}">
                <i class="fas fa-calendar-times"></i> 有効期限管理
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{route('inventory_stocktaking')}}">
                <i class="fas fa-clipboard-check"></i> 在庫棚卸
              </a>
              <a class="dropdown-item" href="{{route('inventory_stocktaking_history')}}">
                <i class="fas fa-history"></i> 棚卸履歴
              </a>
            </div>
          </li>

          {{-- 入出庫管理 --}}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarStock" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-exchange-alt"></i> 入出庫管理
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarStock">
              <a class="dropdown-item" href="{{route('stock_in')}}">
                <i class="fas fa-arrow-down"></i> 入庫登録
              </a>
              <a class="dropdown-item" href="{{route('stock_out')}}">
                <i class="fas fa-arrow-up"></i> 出庫登録
              </a>
              <a class="dropdown-item" href="{{route('stock_return')}}">
                <i class="fas fa-undo"></i> 返品入庫
              </a>
              <a class="dropdown-item" href="{{route('stock_transfer')}}">
                <i class="fas fa-random"></i> 移動在庫
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{route('stock_movement_history')}}">
                <i class="fas fa-history"></i> 入出庫履歴
              </a>
            </div>
          </li>

          {{-- 発注管理 --}}
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarPurchase" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-shopping-cart"></i> 発注管理
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarPurchase">
              <a class="dropdown-item" href="{{ route('order_suggestion') }}">
                <i class="fas fa-lightbulb"></i> 発注提案
              </a>
              <a class="dropdown-item" href="{{ route('purchase_order_list') }}">
                <i class="fas fa-file-invoice"></i> 発注書作成
              </a>
              <a class="dropdown-item" href="{{ route('purchase_tracking') }}">
                <i class="fas fa-truck"></i> 発注状況追跡
              </a>
              <a class="dropdown-item" href="{{ route('supplier_list') }}">
                <i class="fas fa-building"></i> 仕入先管理
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="{{ route('order_analytics') }}">
                <i class="fas fa-chart-bar"></i> 発注実績分析
              </a>
            </div>
          </li>
        </ul>

        {{-- 右側メニュー --}}
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarUser" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fas fa-user-circle"></i> 管理者
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarUser">
              <a class="dropdown-item" href="#" onclick="alert('この機能は開発中です'); return false;">
                <i class="fas fa-user"></i> プロフィール
              </a>
              <a class="dropdown-item" href="#" onclick="alert('この機能は開発中です'); return false;">
                <i class="fas fa-cog"></i> 設定
              </a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" onclick="alert('この機能は開発中です'); return false;">
                <i class="fas fa-sign-out-alt"></i> ログアウト
              </a>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  @yield('content')

  {{-- フッター --}}
  <footer class="py-3 bg-dark absolute-bottom">
    <div class="container">
      <p class="m-0 text-center text-white">Copyright &copy; 2025 youtoishi754 All Rights Reserved.</p>
    </div>
  </footer>
</body>
</html>
