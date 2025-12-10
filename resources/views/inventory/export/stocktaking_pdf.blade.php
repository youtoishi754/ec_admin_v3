<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>棚卸シート</title>
    <style>
        * {
            font-family: ipagothic, sans-serif;
        }
        body {
            font-family: ipagothic, sans-serif;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        .header .date {
            font-size: 12px;
            color: #666;
        }
        .warehouse-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .warehouse-title {
            font-size: 14px;
            font-weight: bold;
            background-color: #2c3e50;
            color: white;
            padding: 8px 10px;
            margin-bottom: 5px;
        }
        .stocktaking-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .stocktaking-table th,
        .stocktaking-table td {
            border: 1px solid #333;
            padding: 6px 8px;
        }
        .stocktaking-table th {
            background-color: #ecf0f1;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }
        .stocktaking-table td.number {
            text-align: right;
        }
        .stocktaking-table td.center {
            text-align: center;
        }
        .stocktaking-table .input-field {
            background-color: #ffffcc;
            width: 60px;
        }
        .stocktaking-table .diff-field {
            width: 60px;
        }
        .stocktaking-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 33%;
            padding: 10px;
        }
        .signature-label {
            font-size: 11px;
            margin-bottom: 20px;
        }
        .signature-line {
            border-bottom: 1px solid #333;
            height: 30px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #666;
        }
        .page-break {
            page-break-after: always;
        }
        .notes {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        .notes h4 {
            margin: 0 0 5px 0;
            font-size: 10px;
        }
        .notes ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>棚 卸 シ ー ト</h1>
        <div class="date">出力日時: {{ $exportDate }}</div>
    </div>

    @foreach($groupedInventories as $warehouseName => $items)
    <div class="warehouse-section">
        <div class="warehouse-title">倉庫: {{ $warehouseName }}</div>
        <table class="stocktaking-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No.</th>
                    <th style="width: 80px;">ロケーション</th>
                    <th style="width: 80px;">商品番号</th>
                    <th>商品名</th>
                    <th style="width: 70px;">ロット番号</th>
                    <th style="width: 70px;">シリアル番号</th>
                    <th style="width: 60px;">システム<br>在庫</th>
                    <th style="width: 60px;">実棚数<br>(記入)</th>
                    <th style="width: 60px;">差異</th>
                    <th style="width: 80px;">備考</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $item->location_code ?? '-' }}</td>
                    <td>{{ $item->goods_number }}</td>
                    <td>{{ $item->goods_name }}</td>
                    <td class="center">{{ $item->lot_number ?? '-' }}</td>
                    <td class="center">{{ $item->serial_number ?? '-' }}</td>
                    <td class="number">{{ number_format($item->system_quantity) }}</td>
                    <td class="input-field"></td>
                    <td class="diff-field"></td>
                    <td></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    <div class="notes">
        <h4>記入方法</h4>
        <ul>
            <li>「実棚数」欄に実際にカウントした数量を記入してください。</li>
            <li>「差異」欄には（実棚数 - システム在庫）を記入してください。</li>
            <li>差異がある場合は「備考」欄に理由を記入してください。</li>
            <li>棚卸完了後は必ず担当者・承認者の署名をお願いします。</li>
        </ul>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-label">棚卸日: ______年______月______日</div>
        </div>
        <div class="signature-box">
            <div class="signature-label">担当者:</div>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box">
            <div class="signature-label">承認者:</div>
            <div class="signature-line"></div>
        </div>
    </div>

    <div class="footer">
        <p>このシートは {{ $exportDate }} に出力されました。</p>
    </div>
</body>
</html>
