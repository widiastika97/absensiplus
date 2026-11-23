<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Presensi {{ $tanggal_mulai }} s/d {{ $tanggal_selesai }}</title>
    @if(!empty($isPrint))
    <style>
        /* Landscape + fit-to-width agar matriks tidak terpotong di sisi kanan */
        @page { size: A4 landscape; margin: 6mm; }
        html { zoom: {{ $printZoom }}; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        /* Ulangi header tabel di tiap halaman & jangan membelah baris */
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        table { page-break-inside: auto; }
        th, td { padding: 3px 2px !important; }
    </style>
    @endif
</head>
<body>
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse; font-family: Calibri, Arial, sans-serif; font-size: 11px;">
    <thead>
        <tr>
            <th colspan="{{ 5 + count($daftarTanggal) + 4 }}" style="background-color: #0f172a; color: #fff; font-size: 14px; font-weight: bold; text-align: center; padding: 12px;">
                LAPORAN REKAPITULASI PRESENSI KARYAWAN
            </th>
        </tr>
        <tr>
            <th colspan="{{ 5 + count($daftarTanggal) + 4 }}" style="background-color: #1e293b; color: #e2e8f0; font-size: 11px; text-align: center; padding: 6px;">
                Periode: {{ date('d F Y', strtotime($tanggal_mulai)) }} s/d {{ date('d F Y', strtotime($tanggal_selesai)) }}
                @if(!empty($isPrint)) (Generated: {{ date('d/m/Y H:i') }} WITA) @endif
            </th>
        </tr>
        <tr style="background-color: #334155; color: #fff; font-weight: bold; text-align: center;">
            <th style="width: 35px;">No</th>
            <th style="width: 95px;">NIP</th>
            <th style="width: 170px; text-align: left;">Nama Karyawan</th>
            <th style="width: 120px;">Departemen</th>
            <th style="width: 120px;">Jabatan</th>
            @foreach($daftarTanggal as $tgl)
                @php $w = (int) date('w', strtotime($tgl)); $singkatHari = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; @endphp
            <th style="width: 85px; background-color: {{ in_array($w, [0, 6]) ? '#475569' : '#334155' }};">
                {{ date('d/m', strtotime($tgl)) }}<br>
                <span style="font-size: 9px; font-weight: normal;">{{ $singkatHari[$w] }}</span>
            </th>
            @endforeach
            <th style="width: 45px; background-color: #065f46;">Hadir</th>
            <th style="width: 45px; background-color: #9a3412;">Telat</th>
            <th style="width: 45px; background-color: #6b21a8;">Cuti</th>
            <th style="width: 45px; background-color: #991b1b;">Alpha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($karyawanList as $idx => $k)
            @php
                $kId = $k->id;
                $totHadir = $totTelat = $totCuti = $totAlpha = 0;
            @endphp
        <tr>
            <td align="center" style="background-color: #f8fafc;">{{ $idx + 1 }}</td>
            <td align="left" style="background-color: #f8fafc; mso-number-format:'@';">'{{ $k->nip }}</td>
            <td style="font-weight: bold;">{{ $k->nama }}</td>
            <td>{{ $k->departemen }}</td>
            <td>{{ $k->jabatan }}</td>
            @foreach($daftarTanggal as $tgl)
                @php
                    $masuk = $presensiMap[$kId][$tgl]['masuk'] ?? null;
                    $pulang = $presensiMap[$kId][$tgl]['pulang'] ?? null;
                    $hasAbsensi = ($masuk !== null || $pulang !== null);
                    $isSunday = ((int) date('w', strtotime($tgl)) === 0);
                    $isLibur = isset($liburMap[$tgl]);
                    $isFuture = ($tgl > $today);

                    if ($hasAbsensi) {
                        $jamM = $masuk ? substr($masuk->jam, 0, 5) : '-';
                        $jamP = $pulang ? substr($pulang->jam, 0, 5) : '-';
                        $isTelat = ($masuk && ($masuk->status === 'terlambat' || (empty($masuk->status) && strtotime($masuk->jam) > strtotime('08:00:00'))));

                        if ($isTelat) {
                            $totTelat++;
                            $cellStyle = 'background-color: #FFEDD5; color: #C2410C; font-weight: bold;';
                            $cellText = 'M: ' . $jamM . '<br>P: ' . $jamP;
                        } else {
                            $totHadir++;
                            $cellStyle = 'background-color: #ECFDF5; color: #047857;';
                            $cellText = 'M: ' . $jamM . '<br>P: ' . $jamP;
                        }
                    } elseif (isset($cutiMap[$kId][$tgl])) {
                        $totCuti++;
                        $cellStyle = 'background-color: #F3E8FF; color: #7E22CE; font-weight: bold;';
                        $cellText = 'CUTI';
                    } elseif ($isFuture) {
                        $cellStyle = 'background-color: #F8FAFC; color: #94A3B8;';
                        $cellText = '-';
                    } elseif ($isSunday || $isLibur) {
                        $cellStyle = 'background-color: #F1F5F9; color: #64748B; font-size: 10px;';
                        $cellText = 'LIBUR';
                    } else {
                        $totAlpha++;
                        $cellStyle = 'background-color: #FEE2E2; color: #B91C1C; font-weight: bold;';
                        $cellText = 'ALPHA';
                    }
                @endphp
            <td align="center" style="{{ $cellStyle }} vertical-align: middle; font-size: 10px;">{!! $cellText !!}</td>
            @endforeach
            <td align="center" style="background-color: #ecfdf5; font-weight: bold; color: #047857;">{{ $totHadir }}</td>
            <td align="center" style="background-color: #ffedd5; font-weight: bold; color: #c2410c;">{{ $totTelat }}</td>
            <td align="center" style="background-color: #f3e8ff; font-weight: bold; color: #7e22ce;">{{ $totCuti }}</td>
            <td align="center" style="background-color: #fee2e2; font-weight: bold; color: #b91c1c;">{{ $totAlpha }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@if(isset($catatanList) && $catatanList->isNotEmpty())
<table border="0" cellpadding="4" cellspacing="0" style="margin-top: 18px; width: 100%; font-size: 10px;">
    <thead>
        <tr>
            <th colspan="3" style="background-color: #b45309; color: #fff; text-align: left; padding: 7px; font-size: 11px;">
                <i>Catatan Admin (Sistem Eror / Karyawan Lupa Absen)</i>
            </th>
        </tr>
        <tr>
            <th style="background-color: #fef3c7; text-align: left; width: 180px; padding: 5px;">Tanggal</th>
            <th style="background-color: #fef3c7; text-align: left; width: 220px; padding: 5px;">Nama Karyawan</th>
            <th style="background-color: #fef3c7; text-align: left; padding: 5px;">Catatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($catatanList as $ct)
        @php
            $kCat = $karyawanList->firstWhere('id', $ct->karyawan_id);
        @endphp
        <tr>
            <td style="padding: 5px; mso-number-format:'@';">{{ \Illuminate\Support\Carbon::parse($ct->tanggal)->translatedFormat('d M Y') }}</td>
            <td style="padding: 5px;">{{ $kCat?->nama ?? ('ID '.$ct->karyawan_id) }}</td>
            <td style="padding: 5px; color: #92400e;">{{ $ct->catatan }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if(!empty($isPrint))
<script>
    window.onload = function () { window.print(); };
</script>
@endif
</body>
</html>
