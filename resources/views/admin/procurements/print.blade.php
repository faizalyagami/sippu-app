{{-- resources/views/admin/procurements/print.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order - {{ $procurement->procurement_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #1e3c72;
        }
        .header h3 {
            margin: 5px 0;
            font-weight: normal;
        }
        .info-section {
            margin-bottom: 30px;
            padding: 15px;
            background: #f5f5f5;
            border-radius: 5px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th {
            background: #1e3c72;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .items-table tfoot tr {
            background: #f5f5f5;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            margin-top: 50px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-completed {
            background: #28a745;
            color: white;
        }
        .text-end {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>PURCHASE ORDER</h1>
        <h3>SIPPUS - Sistem Informasi Perpustakaan UNISBA</h3>
        <p>Jl. Tamansari No. 01, Bandung 40116</p>
        <h2>{{ $procurement->procurement_number }}</h2>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>Tanggal PO</td>
                <td>: {{ $procurement->procurement_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Supplier</td>
                <td>: {{ $procurement->vendor->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Perusahaan</td>
                <td>: {{ $procurement->vendor->company_name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>: {{ $procurement->vendor->address ?? '-' }}</td>
            </tr>
            <tr>
                <td>Kontak</td>
                <td>: {{ $procurement->vendor->contact_person ?? '-' }} ({{ $procurement->vendor->cp_phone ?? '-' }})</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>: {{ $procurement->vendor->email ?? '-' }}</td>
            </tr>
            <tr>
                <td>Telepon</td>
                <td>: {{ $procurement->vendor->phone_number ?? '-' }}</td>
            </tr>
            @if($procurement->expected_date)
            <tr>
                <td>Tanggal Diharapkan</td>
                <td>: {{ $procurement->expected_date->format('d/m/Y') }}</td>
            </tr>
            @endif
            <tr>
                <td>Status</td>
                <td>: <span class="status-badge status-completed">{{ ucfirst($procurement->status) }}</span></td>
            </tr>
        </table>
    </div>

    <h4>Detail Item</h4>
    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Judul Buku</th>
                <th>Penulis</th>
                <th>ISBN</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($procurement->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->book->title }}</td>
                <td>{{ $item->book->author }}</td>
                <td>{{ $item->book->isbn ?? '-' }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                <td class="text-end">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-end"><strong>Grand Total:</strong></td>
                <td class="text-end"><strong>Rp {{ number_format($procurement->total_amount, 0, ',', '.') }}</strong></td>
            </tr>
        </tfoot>
    </table>

    @if($procurement->notes)
    <div style="margin-top: 20px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
        <strong>Catatan:</strong>
        <p>{{ $procurement->notes }}</p>
    </div>
    @endif

    <div class="signature-section">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-line">
                {{ $procurement->createdBy->name ?? '' }}
            </div>
            <p>Admin Perpustakaan</p>
        </div>
        <div class="signature-box">
            <p>Mengetahui,</p>
            <div class="signature-line">
                {{ $procurement->vendor->contact_person ?? '' }}
            </div>
            <p>{{ $procurement->vendor->company_name ?? '' }}</p>
        </div>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak pada {{ now()->format('d/m/Y H:i') }} | SIPPU UNISBA</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>