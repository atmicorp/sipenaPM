<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Form Revisi</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
      table-layout: auto;
      margin-bottom: 20px;
    }

    thead {
      display: table-header-group;
    }

    table th, table td {
      border: 1px solid #000;
      padding: 8px;
      font-size: 14px;
      word-wrap: break-word;
      white-space: normal;
      vertical-align: top;
      page-break-inside: auto !important;
      overflow: visible !important;
    }

    table th {
      background-color: #f2f2f2;
      text-align: center;
    }

    td ol, td ul {
      padding-left: 20px;
      margin: 0;
      page-break-inside: auto !important;
    }

    td li {
      margin-bottom: 4px;
      font-size: 12px;
      line-height: 1.4;
      page-break-inside: auto !important;
      break-inside: auto !important;
    }

    .text-center {
      text-align: center;
    }

    .mb-3 {
      margin-bottom: 1rem;
    }

    .page-break {
      page-break-after: always;
    }
    .row {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    }
    .col {
      display: inline-block;
      width: 48%; /* Untuk memastikan mereka berbagi ruang */
      vertical-align: top;
    }
    .col-full {
      width: 100%;
    }
  </style>
</head>
<body>

<htmlpageheader name="kopHeader">
<div class='text-center' style='margin: 0; padding-top: -20px;'>
    <img src="{{ public_path('images/kop.png') }}" alt="Kop Surat" style="max-width: 100%;">
  </div>
</htmlpageheader>

<sethtmlpageheader name="kopHeader" value="on" show-this-page="1" />

<div class="text-center">
  <h3><strong>BUKTI REVISI TUGAS AKHIR PEMBIMBING DAN PENGUJI</strong></h3>
  <h3><strong>TAHUN AJARAN {{$kelompok->tahun_perkuliahan}}</strong></h3>
</div>

<p><strong>Nama :</strong></p>
<ol class="mb-3">
    @foreach($peserta as $ps)
        <li>{{ $ps->usermahasiswaTA->name }} - {{ $ps->usermahasiswaTA->details->nim }} </li>
    @endforeach
</ol>

<table style="table-layout: fixed; width: 100%;">
  <thead>
    <tr>
      <th style="width: 50px; word-wrap: break-word;">Tanggal</th>
      <th style="width: 250px; word-wrap: break-word;">Dosen</th>
      
      @if(count($groupedFinalData) > 0)
        @foreach(array_keys($groupedFinalData->first()->first()) as $key)
          @if(!in_array($key, ['id_penguji', 'kelompok_ta', 'dosen', 'tanggal_presentasi', 'status']))
            <th style="min-width: 100px; word-wrap: break-word; overflow-wrap: break-word;">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
          @endif
        @endforeach
      @endif
      <th style="width: 100px; word-wrap: break-word;">TTD</th>
    </tr>
  </thead>
  <tbody>
    @foreach($groupedFinalData->flatten(1) as $index => $item)
      <tr @if(($index + 1) % 10 === 0) class="page-break" @endif>
        <td>{{ \Carbon\Carbon::parse($item['tanggal_presentasi'])->format('d/m/Y') }}</td>
        <td>{{ $item['dosen'] }}</td>
        

        @foreach ($item as $key => $value)
          @if(!in_array($key, ['id_penguji', 'kelompok_ta', 'dosen', 'tanggal_presentasi', 'status']))
            @php
              $styledValue = str_replace(
                ['<ol>', '<ul>', '<li>', '<p>'],
                [
                  '<ol style="padding-left:15px; margin:0; page-break-inside:auto;">',
                  '<ul style="padding-left:15px; margin:0; page-break-inside:auto;">',
                  '<li style="margin-bottom:5px; line-height:1.4; page-break-inside:auto;">',
                  '<p style="margin:0 0 5px 0;">'
                ],
                $value
              );
            @endphp
            <td style="page-break-inside: auto; overflow: visible; word-wrap: break-word; min-width: 200px; overflow-wrap: break-word;">
              {!! trim($styledValue) !== '' ? $styledValue : 'N/A' !!}
            </td>
          @endif
        @endforeach

        <td style="width: 150px;  height: 100px; word-wrap: break-word;">&nbsp;</td> {{-- Kolom TTD lebih tinggi --}}
      </tr>
    @endforeach
  </tbody>
</table>


</body>
</html>
