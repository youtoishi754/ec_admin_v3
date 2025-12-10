<?php
/**
 * IPAフォントをDomPDFに登録するスクリプト
 */

require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// DomPDFのフォントディレクトリ
$fontDir = __DIR__ . '/storage/fonts';
$dompdfFontDir = __DIR__ . '/vendor/dompdf/dompdf/lib/fonts';

// フォントファイルのパス
$fontFile = $fontDir . '/ipaexg.ttf';

if (!file_exists($fontFile)) {
    die("Font file not found: $fontFile\n");
}

// フォントをDomPDFのフォントディレクトリにコピー
$destFile = $dompdfFontDir . '/ipaexg.ttf';
if (!file_exists($destFile)) {
    copy($fontFile, $destFile);
    echo "Copied font to: $destFile\n";
}

// DomPDFの初期化
$options = new Options();
$options->set('fontDir', $fontDir);
$options->set('fontCache', $fontDir);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// フォントメトリクスを取得
$fontMetrics = $dompdf->getFontMetrics();

// IPAゴシックフォントを登録
$fontMetrics->registerFont(
    ['family' => 'ipagothic', 'style' => 'normal', 'weight' => 'normal'],
    $destFile
);

echo "Font registered successfully!\n";
echo "Font family: ipagothic\n";
echo "Font file: $destFile\n";

// installed-fonts.jsonを更新
$installedFontsFile = $fontDir . '/installed-fonts.json';
$installedFonts = [];

if (file_exists($installedFontsFile)) {
    $installedFonts = json_decode(file_get_contents($installedFontsFile), true);
}

$installedFonts['ipagothic'] = [
    'normal' => $destFile,
    'bold' => $destFile,
    'italic' => $destFile,
    'bold_italic' => $destFile,
];

file_put_contents($installedFontsFile, json_encode($installedFonts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Updated installed-fonts.json\n";
