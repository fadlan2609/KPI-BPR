<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Cuti</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 16px; }
        .header h2 { margin: 5px 0 0 0; font-size: 14px; font-weight: normal; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $bpr->nama ?? 'BPRS Amanah Bangsa' }}</h1>
        <h2>Laporan Cuti Pegawai</h2>
        @if(request('tanggal_mulai') && request('tanggal_selesai'))
            <p>Periode: {{ \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d/m/Y') }} 
               s/d {{ \Carbon\Carbon::parse(request('tanggal_selesai'))->format('d/m/Y') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Nama Pegawai</th>
                <th>NIP</th>
                <th>Jabatan</th>
                <th>Jenis Cuti</th>
                <th>Tanggal Mulai</th>
                <th>Tanggal Selesai</th>
                <th>Lama (Hari)</th>
                <th>Disetujui Final Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cuti as $i => $c)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $c->pegawai->nama }}</td>
                <td>{{ $c->pegawai->nip }}</td>
                <td>{{ $c->pegawai->jabatan->nama ?? '-' }}</td>
                <td>{{ ucfirst($c->jenis_cuti) }}</td>
                <td class="text-center">{{ $c->tanggal_mulai->format('d/m/Y') }}</td>
                <td class="text-center">{{ $c->tanggal_selesai->format('d/m/Y') }}</td>
                <td class="text-center">{{ $c->lama_hari }}</td>
                <td>{{ $c->finalApprovedBy->nama ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
    </div>
</body>
</html>