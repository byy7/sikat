<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengunjung</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .stat-box {
            text-align: start;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <h2>Laporan Data Pengunjung</h2>
    @if($month || $year)
        <p>Periode: {{ $month ?? 'Semua Bulan' }} {{ $year ?? '' }}</p>
    @endif
    <p>Tanggal Cetak: {{ now()->format('d-m-Y | H:i') }}</p>
</div>

<div class="stats">
    @foreach($stats as $stat)
        <div class="stat-box">
            <strong>{{ $stat['title'] }} : </strong> {{ $stat['value'] }}
        </div>
    @endforeach
</div>

<table>
    <thead>
    <tr>
        <th>No</th>
        <th>Nama</th>
        <th>Keperluan</th>
        <th>Tanggal</th>
    </tr>
    </thead>
    <tbody>
    @forelse($reports as $key => $report)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $report->name }}</td>
            <td>{{ $report->necessary }}</td>
            <td>{{ $report->created_at->format('d-m-Y | H:i') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="4" style="text-align: center">Data Tidak Tersedia</td>
        </tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
