<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>発注書 {{ $order->order_number }}</title>
    <style>
        * {
            font-family: ipagothic, sans-serif;
        }
        body {
            font-family: ipagothic, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
            padding-bottom: 10px;
            border-bottom: 3px solid #333;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .supplier-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .company-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .supplier-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .honorific {
            font-size: 14px;
            margin-left: 10px;
        }
        .company-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .order-info {
            margin-bottom: 20px;
        }
        .order-info table {
            width: 300px;
        }
        .order-info th {
            text-align: left;
            width: 100px;
            padding: 5px;
            background-color: #f5f5f5;
        }
        .order-info td {
            padding: 5px;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .detail-table th,
        .detail-table td {
            border: 1px solid #333;
            padding: 8px;
        }
        .detail-table th {
            background-color: #2c3e50;
            color: #fff;
            font-weight: bold;
            text-align: center;
        }
        .detail-table td.number {
            text-align: right;
        }
        .detail-table td.center {
            text-align: center;
        }
        .detail-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-table {
            margin-left: auto;
            width: 250px;
            border-collapse: collapse;
        }
        .total-table th,
        .total-table td {
            padding: 8px;
            border: 1px solid #333;
        }
        .total-table th {
            text-align: left;
            background-color: #f5f5f5;
        }
        .total-table td {
            text-align: right;
        }
        .total-table .grand-total {
            font-size: 14px;
            font-weight: bold;
            background-color: #2c3e50;
            color: #fff;
        }
        .total-table .grand-total td {
            font-size: 16px;
        }
        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
        }
        .notes h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>発 注 書</h1>
    </div>

    <div class="info-section">
        <div class="supplier-info">
            <div class="supplier-name">
                {{ $order->supplier_name }}<span class="honorific">御中</span>
            </div>
            @if($order->postal_code)
            <div>〒{{ $order->postal_code }}</div>
            @endif
            @if($order->address)
            <div>{{ $order->address }}</div>
            @endif
            @if($order->tel)
            <div>TEL: {{ $order->tel }}</div>
            @endif
            @if($order->contact_person)
            <div>担当: {{ $order->contact_person }} 様</div>
            @endif
        </div>
        <div class="company-info">
            <div class="company-name">{{ $company->name }}</div>
            <div>{{ $company->postal_code }}</div>
            <div>{{ $company->address }}</div>
            <div>TEL: {{ $company->tel }}</div>
            <div>FAX: {{ $company->fax }}</div>
        </div>
    </div>

    <div class="order-info">
        <table>
            <tr>
                <th>発注番号</th>
                <td>{{ $order->order_number }}</td>
            </tr>
            <tr>
                <th>発注日</th>
                <td>{{ \Carbon\Carbon::parse($order->order_date)->format('Y年m月d日') }}</td>
            </tr>
            @if($order->expected_delivery_date)
            <tr>
                <th>納品希望日</th>
                <td>{{ \Carbon\Carbon::parse($order->expected_delivery_date)->format('Y年m月d日') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="message">
        下記の通り発注いたしますので、ご査収の上、納品をお願いいたします。
    </div>

    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 40px;">No.</th>
                <th style="width: 100px;">商品番号</th>
                <th>商品名</th>
                <th style="width: 60px;">数量</th>
                <th style="width: 50px;">単位</th>
                <th style="width: 80px;">単価</th>
                <th style="width: 100px;">金額</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orderDetails as $index => $detail)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $detail->goods_number }}</td>
                <td>{{ $detail->goods_name }}</td>
                <td class="number">{{ number_format($detail->quantity) }}</td>
                <td class="center">{{ $detail->unit ?? '個' }}</td>
                <td class="number">¥{{ number_format($detail->unit_price) }}</td>
                <td class="number">¥{{ number_format($detail->subtotal) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table class="total-table">
            <tr>
                <th>小計</th>
                <td>¥{{ number_format($order->total_amount) }}</td>
            </tr>
            <tr>
                <th>消費税(10%)</th>
                <td>¥{{ number_format($order->total_amount * 0.1) }}</td>
            </tr>
            <tr class="grand-total">
                <th>合計金額</th>
                <td>¥{{ number_format($order->total_amount * 1.1) }}</td>
            </tr>
        </table>
    </div>

    @if($order->notes)
    <div class="notes">
        <h3>備考</h3>
        <div>{!! nl2br(e($order->notes)) !!}</div>
    </div>
    @endif

    <div class="footer">
        <p>この発注書は {{ now()->format('Y年m月d日 H:i') }} に出力されました。</p>
    </div>
</body>
</html>
