<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin – Pembayaran Masuk</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f7fa; padding:30px }
        table { width:100%; border-collapse:collapse; background:#fff }
        th, td { padding:12px; border-bottom:1px solid #ddd }
        th { background:#f0f2f5; text-align:left }
        .badge {
            padding:4px 10px;
            border-radius:12px;
            font-size:12px;
            color:#fff;
        }
        .pending { background:#f59e0b }
        .approved { background:#10b981 }
        .rejected { background:#ef4444 }
    </style>
</head>
<body>

<h2>💰 Daftar Pembayaran Masuk</h2>

<table>
    <thead>
        <tr>
            <th>User</th>
            <th>Kamar</th>
            <th>Jumlah</th>
            <th>Metode</th>
            <th>Status</th>
            <th>Waktu</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($payments as $payment)
        <tr>
            <td>{{ $payment->bill->booking->user->name }}</td>
            <td>{{ $payment->bill->booking->room->name }}</td>
            <td>Rp {{ number_format($payment->amount) }}</td>
            <td>{{ $payment->method }}</td>
            <td>
                <span class="badge {{ $payment->status }}">
                    {{ strtoupper($payment->status) }}
                </span>
            </td>
            <td>{{ $payment->created_at->format('d M Y H:i') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" align="center">Belum ada pembayaran</td>
        </tr>
        @endforelse
    </tbody>
</table>

</body>
</html>
    