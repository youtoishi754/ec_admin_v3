# ===================================
# ダミー画像を一括作成・配置
# 作成日: 2025-12-07
# 説明: 全商品フォルダに既存のダミー画像（main.png）を配置（上書き）
# ===================================

# 基本パス
$publicPath = "F:\windows11_user\xampp\htdocs\ec_admin\public"
$productsPath = "$publicPath\images\products"

# ソース画像（既存のmain.png）
$sourceImagePath = "$productsPath\A01_000001\main.png"

# ソース画像の存在確認
if (-not (Test-Path $sourceImagePath)) {
    Write-Host "エラー: ソース画像が見つかりません" -ForegroundColor Red
    Write-Host "パス: $sourceImagePath"
    exit 1
}

Write-Host "ソース画像を確認しました: $sourceImagePath" -ForegroundColor Green
Write-Host "このファイルを全商品に配置します（既存ファイルも上書き）" -ForegroundColor Yellow
Write-Host ""

# 商品番号リスト
$goodsNumbers = @(
    'A01_000001', 'A01_000002', 'A01_000003', 'A01_000004',
    'A02_000001', 'A02_000002', 'A02_000003',
    'A03_000001', 'A03_000002', 'A03_000003',
    'A05_000001', 'A05_000002',
    'A08_000001', 'A08_000002',
    'A10_000001', 'A10_000002',
    'B01_000001', 'B01_000002',
    'B02_000001', 'B02_000002',
    'B03_000001', 'B03_000002',
    'B06_000001', 'B06_000002',
    'C01_000001', 'C01_000002', 'C01_000003',
    'C03_000001', 'C03_000002',
    'C05_000001', 'C05_000002',
    'D01_000001', 'D01_000002', 'D01_000003',
    'D02_000001', 'D02_000002',
    'E01_000001', 'E01_000002',
    'E02_000001', 'E02_000002',
    'E04_000001', 'E04_000002'
)

Write-Host "商品画像フォルダを作成中..."
Write-Host "対象商品数: $($goodsNumbers.Count)"
Write-Host ""

$created = 0
$copied = 0
$overwritten = 0

foreach ($goodsNumber in $goodsNumbers) {
    $productDir = "$productsPath\$goodsNumber"
    $targetImage = "$productDir\main.png"
    
    # ディレクトリが存在しない場合は作成
    if (-not (Test-Path $productDir)) {
        New-Item -Path $productDir -ItemType Directory -Force | Out-Null
        $created++
    }
    
    # 画像を強制的にコピー（上書き）
    if (Test-Path $targetImage) {
        Copy-Item $sourceImagePath $targetImage -Force
        $overwritten++
        Write-Host "  ✓ [上書き完了] $goodsNumber" -ForegroundColor Cyan
    } else {
        Copy-Item $sourceImagePath $targetImage -Force
        $copied++
        Write-Host "  ✓ [コピー完了] $goodsNumber" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "処理完了" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "  フォルダ作成: $created 件" -ForegroundColor White
Write-Host "  新規コピー  : $copied 件" -ForegroundColor Green
Write-Host "  上書き      : $overwritten 件" -ForegroundColor Cyan
Write-Host "  合計        : $($copied + $overwritten) 件" -ForegroundColor Yellow
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host ""

# フォルダ一覧表示
Write-Host "配置先フォルダ一覧:"
Get-ChildItem $productsPath -Directory | Select-Object Name, @{Name="Files";Expression={(Get-ChildItem $_.FullName -File).Count}} | Format-Table -AutoSize

# 確認
Write-Host ""
Write-Host "画像配置先を確認しますか? (Y/N): " -NoNewline
$confirm = Read-Host
if ($confirm -eq 'Y' -or $confirm -eq 'y') {
    Start-Process "explorer.exe" -ArgumentList $productsPath
}