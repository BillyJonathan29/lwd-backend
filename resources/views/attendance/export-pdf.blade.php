<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LPJ Presensi - LWD</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            color: #1e293b;
            background: #fff;
        }

        .page-wrapper {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            padding: 24px 32px;
        }

        /* ===================== HEADER INSTITUSI ===================== */
        .lpj-header {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-bottom: 12px;
            border-bottom: 3px solid #1e3a8a;
        }

        .lpj-header-logo {
            width: 64px;
            height: 64px;
            background: #1e3a8a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 20px;
            flex-shrink: 0;
        }

        .lpj-header-text h1 {
            font-size: 15pt;
            font-weight: 900;
            color: #1e3a8a;
            line-height: 1.2;
        }

        .lpj-header-text p {
            font-size: 9pt;
            color: #475569;
            margin-top: 2px;
        }

        /* ===================== TITLE DOKUMEN ===================== */
        .doc-title-section {
            text-align: center;
            margin: 20px 0 16px;
        }

        .doc-title-section h2 {
            font-size: 13pt;
            font-weight: 900;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-title-section p {
            font-size: 9pt;
            color: #64748b;
            margin-top: 4px;
        }

        /* ===================== INFO BOX ===================== */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 16px;
        }

        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
        }

        .info-box .label {
            font-size: 7.5pt;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .info-box .value {
            font-size: 10pt;
            font-weight: 700;
            color: #1e293b;
            margin-top: 2px;
        }

        /* ===================== TABLE ===================== */
        .legend {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
            font-size: 8pt;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }

        thead tr th {
            background: #1e3a8a;
            color: white;
            padding: 6px 8px;
            text-align: center;
            font-weight: 700;
            font-size: 7.5pt;
            border: 1px solid #1e3a8a;
        }

        thead tr th.col-left {
            text-align: left;
        }

        thead tr.subheader th {
            background: #1d4ed8;
            font-weight: 600;
            font-size: 7pt;
        }

        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #eff6ff; }

        tbody td {
            padding: 5px 8px;
            border: 1px solid #e2e8f0;
            text-align: center;
            vertical-align: middle;
        }

        tbody td.col-left { text-align: left; }

        /* Status pills */
        .s-hadir  { background: #dcfce7; color: #166534; font-weight: 700; border-radius: 3px; padding: 1px 5px; font-size: 7.5pt; }
        .s-izin   { background: #f3e8ff; color: #6b21a8; font-weight: 700; border-radius: 3px; padding: 1px 5px; font-size: 7.5pt; }
        .s-sakit  { background: #dbeafe; color: #1e40af; font-weight: 700; border-radius: 3px; padding: 1px 5px; font-size: 7.5pt; }
        .s-alpa   { background: #fee2e2; color: #991b1b; font-weight: 700; border-radius: 3px; padding: 1px 5px; font-size: 7.5pt; }

        .col-summary {
            background: #fefce8;
            font-weight: 700;
            font-size: 8pt;
        }

        /* ===================== SUMMARY ROW ===================== */
        tfoot td {
            background: #1e3a8a;
            color: white;
            font-weight: 700;
            padding: 6px 8px;
            border: 1px solid #1e3a8a;
            text-align: center;
            font-size: 8pt;
        }

        tfoot td.col-left { text-align: left; }

        /* ===================== FOOTER ===================== */
        .doc-footer {
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .signature-block {
            text-align: center;
            width: 200px;
        }

        .signature-block .title {
            font-size: 9pt;
            font-weight: 700;
            color: #1e293b;
        }

        .signature-block .sig-space {
            margin: 40px 0 8px;
            border-bottom: 1px solid #cbd5e1;
        }

        .signature-block .name {
            font-size: 9pt;
            font-weight: 700;
        }

        .signature-block .role {
            font-size: 8pt;
            color: #64748b;
        }

        .print-info {
            font-size: 8pt;
            color: #94a3b8;
        }

        /* ===================== PRINT ===================== */
        @media print {
            body { margin: 0; }
            .page-wrapper { max-width: 100%; padding: 12px; }
            .no-print { display: none !important; }

            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }

            tr { page-break-inside: avoid; }
        }

        /* Print button */
        .action-bar {
            position: fixed;
            bottom: 24px;
            right: 24px;
            display: flex;
            gap: 10px;
            z-index: 999;
        }

        .btn-print {
            padding: 12px 24px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 11pt;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(30,58,138,0.35);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .btn-print:hover { background: #1e40af; }

        .btn-back {
            padding: 12px 24px;
            background: white;
            color: #374151;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 11pt;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back:hover { border-color: #94a3b8; }
    </style>
</head>
<body>

{{-- ===== FLOATING ACTION BAR ===== --}}
<div class="action-bar no-print">
    <button class="btn-back" onclick="history.back()">← Kembali</button>
    <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
</div>

<div class="page-wrapper">

    {{-- ===== HEADER INSTITUSI ===== --}}
    <div class="lpj-header">
        <div class="lpj-header-logo">LWD</div>
        <div class="lpj-header-text">
            <h1>Learning With Data (LWD)</h1>
            <p>Laporan Pertanggungjawaban Presensi Anggota &mdash; Periode {{ now()->translatedFormat('Y') }}</p>
        </div>
    </div>

    {{-- ===== JUDUL DOKUMEN ===== --}}
    <div class="doc-title-section">
        <h2>Rekap Presensi Anggota</h2>
        <p>
            {{ $scope === 'single' ? 'Satu Pertemuan' : 'Seluruh Pertemuan (Satu Semester)' }}
            &bull; Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB
        </p>
    </div>

    {{-- ===== INFO GRID ===== --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="label">Total Anggota</div>
            <div class="value">{{ count($members) }} orang</div>
        </div>
        <div class="info-box">
            <div class="label">Total Pertemuan</div>
            <div class="value">{{ count($meetings) }} pertemuan</div>
        </div>
        <div class="info-box">
            <div class="label">Periode Pertama</div>
            <div class="value">{{ $meetings->first() ? \Carbon\Carbon::parse($meetings->first()->date)->translatedFormat('d F Y') : '-' }}</div>
        </div>
        <div class="info-box">
            <div class="label">Periode Terakhir</div>
            <div class="value">{{ $meetings->last() ? \Carbon\Carbon::parse($meetings->last()->date)->translatedFormat('d F Y') : '-' }}</div>
        </div>
    </div>

    {{-- ===== LEGENDA ===== --}}
    <div class="legend">
        <strong>Keterangan:</strong>
        <span class="legend-item"><span class="legend-dot" style="background:#dcfce7;border:1px solid #166534;"></span> H = Hadir</span>
        <span class="legend-item"><span class="legend-dot" style="background:#f3e8ff;border:1px solid #6b21a8;"></span> I = Izin</span>
        <span class="legend-item"><span class="legend-dot" style="background:#dbeafe;border:1px solid #1e40af;"></span> S = Sakit</span>
        <span class="legend-item"><span class="legend-dot" style="background:#fee2e2;border:1px solid #991b1b;"></span> A = Alpa</span>
    </div>

    {{-- ===== TABEL PRESENSI ===== --}}
    @php
        $totalHadir = array_sum(array_column($rows, 'hadir'));
        $totalIzin  = array_sum(array_column($rows, 'izin'));
        $totalSakit = array_sum(array_column($rows, 'sakit'));
        $totalAlpa  = array_sum(array_column($rows, 'alpa'));
    @endphp

    <table>
        <thead>
            <tr>
                <th class="col-left" rowspan="2" style="width:35px">No</th>
                <th class="col-left" rowspan="2" style="width:80px">NIM</th>
                <th class="col-left" rowspan="2" style="min-width:160px">Nama Lengkap</th>
                <th class="col-left" rowspan="2" style="width:80px">Divisi</th>
                @foreach($meetings as $m)
                    <th style="min-width:42px;">
                        Ptm {{ $loop->iteration }}<br>
                        <span style="font-weight:400;font-size:6.5pt;">{{ \Carbon\Carbon::parse($m->date)->format('d/m') }}</span>
                    </th>
                @endforeach
                <th style="width:35px;background:#15803d">H</th>
                <th style="width:35px;background:#6b21a8">I</th>
                <th style="width:35px;background:#1e40af">S</th>
                <th style="width:35px;background:#991b1b">A</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="col-left">{{ $row['nim'] }}</td>
                <td class="col-left" style="font-weight:600">{{ $row['full_name'] }}</td>
                <td class="col-left">{{ $row['subdivision'] }}</td>
                @foreach($meetings as $m)
                    @php $st = $row['meeting_' . $m->id]; @endphp
                    <td>
                        @if($st === 'present')
                            <span class="s-hadir">H</span>
                        @elseif($st === 'excused')
                            <span class="s-izin">I</span>
                        @elseif($st === 'sick')
                            <span class="s-sakit">S</span>
                        @else
                            <span class="s-alpa">A</span>
                        @endif
                    </td>
                @endforeach
                <td class="col-summary" style="color:#166534;">{{ $row['hadir'] }}</td>
                <td class="col-summary" style="color:#6b21a8;">{{ $row['izin'] }}</td>
                <td class="col-summary" style="color:#1e40af;">{{ $row['sakit'] }}</td>
                <td class="col-summary" style="color:#991b1b;">{{ $row['alpa'] }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="col-left">TOTAL KESELURUHAN</td>
                @foreach($meetings as $m)
                    @php
                        $hadirCount = array_filter($rows, fn($r) => $r['meeting_' . $m->id] === 'present');
                        $notHadir   = array_filter($rows, fn($r) => $r['meeting_' . $m->id] !== 'present');
                    @endphp
                    <td title="Hadir: {{ count($hadirCount) }} | Absen: {{ count($notHadir) }}">
                        {{ count($hadirCount) }}/{{ count($rows) }}
                    </td>
                @endforeach
                <td>{{ $totalHadir }}</td>
                <td>{{ $totalIzin }}</td>
                <td>{{ $totalSakit }}</td>
                <td>{{ $totalAlpa }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- ===== FOOTER TTD ===== --}}
    <div class="doc-footer">
        <div>
            <p class="print-info">Dokumen ini digenerate otomatis oleh sistem LWD Backend.</p>
            <p class="print-info">Data diambil real-time dari database presensi.</p>
        </div>
        <div class="signature-block">
            <div class="title">Mengetahui,</div>
            <div class="title">Ketua LWD</div>
            <div class="sig-space"></div>
            <div class="name">_____________________</div>
            <div class="role">Ketua Learning With Data</div>
        </div>
    </div>

</div>

</body>
</html>
