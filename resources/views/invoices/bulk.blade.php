<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulk Invoice Print</title>
    @php
        $settings = \App\Models\InvoiceSetting::allSettings();
    @endphp
    <style>
        @page {
            size: A4;
            margin: 8mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            background: #f5f5f5;
        }

        .invoice-page {
            width: 100%;
            height: 277mm;
            background: white;
            padding: 10mm 12mm;
            page-break-after: always;
            box-sizing: border-box;
        }

        .invoice-page:last-child {
            page-break-after: avoid;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .store-info h1 {
            font-size: 22px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 2px;
        }

        .store-info .tagline {
            font-size: 10px;
            color: #666;
            font-style: italic;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-info .title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 3px;
        }

        .invoice-info .meta {
            font-size: 10px;
            color: #666;
        }

        .invoice-info .meta strong {
            color: #333;
        }

        /* Penerima Section */
        .penerima-section {
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e0e0e0;
        }

        .penerima-section .label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .penerima-section .name {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a1a;
        }

        .penerima-section .address,
        .penerima-section .phone {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .items-table th {
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            padding: 6px 0;
            border-bottom: 2px solid #333;
        }

        .items-table th.right {
            text-align: right;
        }

        .items-table td {
            padding: 8px 0;
            border-bottom: 1px solid #e0e0e0;
            font-size: 10px;
            color: #333;
        }

        .items-table td.right {
            text-align: right;
        }

        .items-table td.product {
            color: #555;
        }

        /* Totals */
        .totals {
            margin-top: 12px;
        }

        .totals-row {
            display: flex;
            justify-content: flex-end;
            padding: 3px 0;
            font-size: 10px;
        }

        .totals-row .label {
            width: 100px;
            text-align: right;
            color: #666;
        }

        .totals-row .value {
            width: 100px;
            text-align: right;
            color: #333;
        }

        .totals-row.grand {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #e0e0e0;
        }

        .totals-row.grand .label {
            font-weight: 700;
            font-size: 12px;
            color: #333;
        }

        .totals-row.grand .value {
            font-weight: 700;
            font-size: 14px;
            color: #1a1a1a;
        }

        /* Shipping Label */
        .shipping-label {
            margin-top: 25px;
            border: 2px solid #333;
            padding: 12px;
        }

        .shipping-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        .shipping-header h2 {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a1a;
        }

        .shipping-header .order-id {
            text-align: right;
            font-size: 9px;
            color: #666;
        }

        .shipping-header .order-id strong {
            display: block;
            font-size: 10px;
            color: #333;
        }

        .shipping-penerima .label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .shipping-penerima .name {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 3px 0;
        }

        .shipping-penerima .phone {
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }

        .shipping-penerima .address {
            font-size: 10px;
            color: #555;
            margin-top: 3px;
        }

        .shipping-footer {
            display: flex;
            justify-content: space-between;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
            font-size: 9px;
            color: #666;
        }

        .shipping-footer strong {
            color: #333;
        }

        /* Print Controls */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }

        .print-btn {
            background: #333;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .print-btn:hover {
            background: #555;
        }

        .back-btn {
            background: #f0f0f0;
            color: #333;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }

        .print-info {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 1000;
        }

        /* Screen preview spacing */
        .invoice-wrapper {
            max-width: 210mm;
            margin: 0 auto;
        }

        .invoice-wrapper .invoice-page {
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        @media print {

            .print-controls,
            .print-info {
                display: none !important;
            }

            body {
                background: white;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .invoice-wrapper .invoice-page {
                box-shadow: none;
                margin-bottom: 0;
            }
        }
    </style>
</head>

<body>
    <div class="print-controls">
        <a href="javascript:history.back()" class="back-btn">← Kembali</a>
        <button class="print-btn" onclick="window.print()">🖨️ Print All ({{ $orders->count() }} Invoice)</button>
    </div>
    <div class="print-info">📄 {{ $orders->count() }} invoice akan dicetak</div>

    <div class="invoice-wrapper">
        @foreach($orders as $order)
            <div class="invoice-page">
                <!-- Header -->
                <div class="header">
                    <div class="store-info">
                        <h1>{{ $settings['store_name'] ?? 'ARIMBI STORE' }}</h1>
                        <div class="tagline">{{ $settings['store_tagline'] ?? 'Katalog Online & Overseas Snacks' }}</div>
                    </div>
                    <div class="invoice-info">
                        <div class="title">INVOICE</div>
                        <div class="meta">
                            <strong>No:</strong> {{ $order->invoice_number }}<br>
                            <strong>Tanggal:</strong> {{ $order->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>

                <!-- Penerima -->
                <div class="penerima-section">
                    <div class="label">Penerima:</div>
                    <div class="name">{{ $order->receiver_name ?: $order->customer_name }}</div>
                    <div class="address">{{ $order->receiver_address ?: '-' }}</div>
                    <div class="phone">Telp: {{ $order->receiver_phone ?: $order->customer_phone }}</div>
                </div>

                <!-- Items Table -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 50%">Deskripsi Produk</th>
                            <th class="right">QTY</th>
                            <th class="right">Harga Satuan</th>
                            <th class="right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td class="product">{{ $item->itemable?->name ?? 'Unknown Item' }}</td>
                                <td class="right">{{ $item->quantity }}</td>
                                <td class="right">Rp {{ number_format($item->price_at_purchase, 0, ',', '.') }}</td>
                                <td class="right">Rp
                                    {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="totals">
                    @php
                        $subtotal = $order->items->sum(fn($i) => $i->price_at_purchase * $i->quantity);
                        $shippingCost = $order->shipping_cost ?? 0;
                        $grandTotal = $order->total_price;
                    @endphp
                    <div class="totals-row">
                        <div class="label">Subtotal</div>
                        <div class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</div>
                    </div>
                    <div class="totals-row">
                        <div class="label">Ongkos Kirim</div>
                        <div class="value">Rp {{ number_format($shippingCost, 0, ',', '.') }}</div>
                    </div>
                    <div class="totals-row grand">
                        <div class="label">TOTAL</div>
                        <div class="value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                    </div>
                </div>

                <!-- Shipping Label -->
                <div class="shipping-label">
                    <div class="shipping-header">
                        <h2>{{ $settings['store_name'] ?? 'ARIMBI STORE' }}</h2>
                        <div class="order-id">
                            ORDER ID<br>
                            <strong>{{ $order->invoice_number }}</strong>
                        </div>
                    </div>

                    <div class="shipping-penerima">
                        <div class="label">Penerima:</div>
                        <div class="name">{{ $order->receiver_name ?: $order->customer_name }}</div>
                        <div class="phone">{{ $order->receiver_phone ?: $order->customer_phone }}</div>
                        <div class="address">{{ $order->receiver_address ?: '-' }}</div>
                    </div>

                    <div class="shipping-footer">
                        <div><strong>Pengirim:</strong> {{ $settings['shipping_sender_name'] ?? 'Arimbi Store' }}
                            ({{ $settings['shipping_sender_phone'] ?? '' }})</div>
                        <div><strong>Isi:</strong> {{ $settings['package_content'] ?? 'Produk Kecantikan / Makanan' }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>