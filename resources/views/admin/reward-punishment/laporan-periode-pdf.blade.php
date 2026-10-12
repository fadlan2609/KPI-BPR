<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Reward & Punishment</title>
    <style>
        @page { size: A4 landscape; margin: 1.5cm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Times New Roman", serif; font-size: 10pt; color: #000; }
        .header { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { font-size: 16pt; font-weight: bold; }
        .header h2 { font-size: 13pt; font-weight: normal; margin-top: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px 6px; font-size: 9.5pt; }
        th { background: #1F4E79; color: white; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 1px 8px; border-radius: 10px; font-size: 8pt; font-weight: bold; }
        .badge-reward { background: #dcfce7; color: #166534; }
        .badge-punishment { background: #fee2e2; color: #991b1b; }
        .footer { margin-top: 20px; text-align: center; font-size: 8pt; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $bpr->nama_bpr ?? 'BPRS AMANAH BANGSA' }}</h1>
        <h2>LAPORAN REWARD & PUNISHMENT PEGAWAI</h2>
        <p style="margin-top: 5px;">Periode: {{ $periodeLabel }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="150">Pegawai</th>
                <th width="100">Jabatan</th>
                <th width="90">Kategori</th>
                <th width="120">Tipe</th>
                <th>Judul</th>
                <th width="80">Tanggal</th>
                <th width="100">Nominal</th>
                <th width="80">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $item)
                <tr>
                    <td class="text-center">{{ $i + 1 }}</td>
                    <td>{{ $item->pegawai->nama ?? '-' }}</td>
                    <td>{{ $item->pegawai->jabatan->nama ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge {{ $item->kategori === 'reward' ? 'badge-reward' : 'badge-punishment' }}">
                            {{ $item->kategori === 'reward' ? 'Reward' : 'Punishment' }}
                        </span>
                    </td>
                    <td>{{ $item->tipe_label }}</td>
                    <td>{{ $item->judul }}</td>
                    <td class="text-center">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td class="text-right">{{ $item->nominal_format ?? '-' }}</td>
                    <td class="text-center">{{ $item->status_color['label'] }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>