<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Cetak Laporan Rekap Struk</title>
    <style>
        /* Standalone Print Stylings mapping standard tailwind tokens directly */
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0 0 5px 0;
            font-size: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e293b;
        }

        .header p {
            margin: 0;
            color: #475569;
            font-size: 13px;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
            color: #475569;
        }

        .meta-info div {
            margin-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-verified {
            background-color: #d1fae5;
            color: #059669;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .status-raw {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }

        /* Auto-trigger print when opened */
        @media print {
            body {
                padding: 0;
            }

            button.print-btn {
                display: none;
            }
        }

        button.print-btn {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 20px;
            font-weight: bold;
        }

        button.print-btn:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>

<body onload="window.print()">

    <button class="print-btn" onclick="window.print()">Cetak Dokumen Sekarang</button>

    <div class="header">
        <h1>Laporan Rekapitulasi Struk Belanja</h1>
        <p>Sistem Pemindai AI - PT. ITSJ (ReceiptOptima)</p>
    </div>

    <div class="meta-info">
        <div>
            <div><strong>Periode Laporan:</strong>
                @if(!empty($filters['start_date']) && !empty($filters['end_date']))
                    {{ \Carbon\Carbon::parse($filters['start_date'])->translatedFormat('d F Y') }} s/d
                    {{ \Carbon\Carbon::parse($filters['end_date'])->translatedFormat('d F Y') }}
                @elseif(!empty($filters['start_date']))
                    Mulai {{ \Carbon\Carbon::parse($filters['start_date'])->translatedFormat('d F Y') }}
                @elseif(!empty($filters['end_date']))
                    Sampai {{ \Carbon\Carbon::parse($filters['end_date'])->translatedFormat('d F Y') }}
                @else
                    Seluruh Riwayat (Sepanjang Waktu)
                @endif
            </div>
            <div><strong>Filter Toko:</strong> {{ !empty($filters['store']) ? $filters['store'] : 'Semua Toko' }}</div>
            <div><strong>Saringan Status:</strong>
                @if(isset($filters['status']) && $filters['status'] === 'raw') Mentah (Belum Diedit)
                @elseif(isset($filters['status']) && $filters['status'] === 'edited') Menunggu ACC (Sudah Diedit)
                @elseif(isset($filters['status']) && $filters['status'] === 'verified') Valid (Disetujui)
                @elseif(isset($filters['status']) && $filters['status'] === 'rejected') Ditolak / Koreksi
                @else Semua Status
                @endif
            </div>
        </div>
        <div style="text-align: right;">
            <div><strong>Dicetak Pada:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}</div>
            <div><strong>Dicetak Oleh:</strong> {{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tgl Struk</th>
                <th>Nama Toko / Ritel</th>
                <th>Kategori Utama</th>
                <th class="text-right">Total Transaksi</th>
                <th>Status Validasi</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @forelse($receipts as $index => $receipt)
                @php $grandTotal += $receipt->total_price; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d/m/Y') }}</td>
                    <td class="font-bold">{{ $receipt->store_name }}</td>
                    <td>{{ $receipt->category ?? '-' }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($receipt->total_price, 0, ',', '.') }}</td>
                    <td>
                        @if($receipt->status === 'verified')
                            <span class="status-badge status-verified">Valid (✅ ACC)</span>
                        @elseif($receipt->status === 'pending_approval' && $receipt->created_at != $receipt->updated_at)
                            <span class="status-badge status-pending">Menunggu ACC (Diedit)</span>
                        @elseif($receipt->created_at == $receipt->updated_at)
                            <span class="status-badge status-raw">Raw (Belum Edit)</span>
                        @else
                            <span class="status-badge status-raw">Koreksi</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #94a3b8;">
                        Tidak ada data struk yang sesuai dengan filter saat ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($receipts->count() > 0)
            <tfoot>
                <tr>
                    <th colspan="4" class="text-right">TOTAL AKUMULASI:</th>
                    <th class="text-right" style="font-size: 14px; color: #1e40af;">Rp
                        {{ number_format($grandTotal, 0, ',', '.') }}</th>
                    <th></th>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Laporan ini dicetak secara otomatis (Sistem ReceiptOptima).<br>Gunakan data ini untuk rekonsiliasi internal.
            Jangan distribusikan tanpa otorisasi Kepala Keuangan.</p>
    </div>

</body>

</html>