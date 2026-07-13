<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan L-Dry</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #14b8a6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
            border-collapse: collapse;
        }
        .brand-title {
            font-size: 22px;
            font-weight: bold;
            color: #14b8a6;
            letter-spacing: 1px;
        }
        .business-info {
            text-align: right;
            line-height: 1.4;
            color: #666;
        }
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            color: #1f2937;
        }
        .report-subtitle {
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 25px;
        }
        .summary-box {
            background-color: #f0fdfa;
            border: 1px solid #ccfbf1;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 20px;
        }
        .summary-box table {
            width: 100%;
        }
        .summary-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #0d9488;
            font-weight: bold;
        }
        .summary-value {
            font-size: 16px;
            font-weight: bold;
            color: #0f766e;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th {
            background-color: #1f2937;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            padding: 8px;
            text-align: left;
        }
        .data-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .data-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f3f4f6;
            border-top: 1px solid #9ca3af;
            border-bottom: 2px solid #1f2937;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td>
                    <span class="brand-title">{{ $businessName }}</span><br>
                    <span style="color: #6b7280; font-size: 9px; font-weight: bold;">PREMIUM LAUNDRY SYSTEM</span>
                </td>
                <td class="business-info">
                    <strong>Alamat Outlet:</strong> {{ $address }}<br>
                    <strong>WhatsApp:</strong> {{ $whatsapp }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="report-title">Laporan Rekapitulasi Keuangan</div>
    <div class="report-subtitle">
        Periode Laporan: {{ $startDate->format('d M Y') }} s/d {{ $endDate->format('d M Y') }}
    </div>

    <!-- Summary Box -->
    <div class="summary-box">
        <table>
            <tr>
                <td>
                    <span class="summary-label">Total Pendapatan (Omzet)</span><br>
                    <span class="summary-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </td>
                <td class="text-center">
                    <span class="summary-label">Total Cucian Selesai</span><br>
                    <span class="summary-value">{{ $totalOrders }} Order</span>
                </td>
                <td class="text-right">
                    <span class="summary-label">Total Berat (Kiloan)</span><br>
                    <span class="summary-value">{{ $totalWeight }} kg</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Itemized Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Tanggal Selesai</th>
                <th>Pelanggan</th>
                <th>Layanan</th>
                <th>Satuan</th>
                <th>Berat/Qty</th>
                <th>Kurir</th>
                <th class="text-right">Total Tarif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td style="font-weight: bold; color: #1f2937;">{{ $order->order_number }}</td>
                    <td>{{ $order->updated_at->format('d M Y H:i') }}</td>
                    <td>{{ $order->customer->name }}</td>
                    <td>{{ $order->service->name }}</td>
                    <td style="text-transform: uppercase;">{{ $order->service->unit }}</td>
                    <td>{{ $order->weight_kg ? $order->weight_kg : '-' }}</td>
                    <td>{{ $order->kurir ? $order->kurir->name : '-' }}</td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="color: #9ca3af; font-style: italic; padding: 20px;">
                        Tidak ada transaksi terdata pada rentang tanggal ini.
                    </td>
                </tr>
            @endforelse
            
            <!-- Grand Total Row -->
            <tr class="total-row">
                <td colspan="5">GRAND TOTAL</td>
                <td>{{ $totalWeight }} kg</td>
                <td></td>
                <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Dokumen laporan keuangan ini digenerate secara otomatis oleh L-Dry Laundry System pada {{ date('d M Y H:i') }} WIB.
    </div>

</body>
</html>
