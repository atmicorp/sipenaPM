<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Form Revisi</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{asset('css/fontawesome-free/css/all.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('css/adminlte.min.css')}}">

  <style>
    @media print {
      @page {
        size: landscape;
        margin: 10mm;
      }

      body {
        margin: 0;
        font-family: 'Times New Roman', Times, serif;
      }

      .print-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 120px; /* Tinggi kop */
        z-index: 999;
      }

      .print-header img {
        width: 100%;
        height: auto;
      }

      section.invoice {
        padding-top: 150px; /* Sesuaikan agar isi tidak tertimpa */
      }

      table {
        page-break-inside: auto;
      }

      tr {
        page-break-inside: avoid;
        page-break-after: auto;
      }
    }
  </style>
</head>
<body>
<div class="wrapper">
  <!-- Header tetap -->
  <div class="print-header">
    <img src="{{ asset('images/kop.png') }}" alt="Kop Surat">
  </div>

  <!-- Main content -->
  <section class="invoice">
    <div class="container mx-auto" style="padding-left: 20px; padding-right: 20px;">
      <div class="row mb-3">
        <div class="col-12 table-responsive text-center">
          <h5><strong>BUKTI REVISI MAGANG DOSEN PEMBIMBING DAN PENGUJI</strong></h5>
          <h5><strong>TAHUN AJARAN 2025/2026</strong></h5>
        </div>
      </div>

      <div class="row">
        <div class="col-6 table-responsive">
          <p>Nama : {{$user->name}} - {{$user->details->nim}}</p>
        </div>
        <div class="col-6 table-responsive">
          <!-- Kosong / tambahan info -->
        </div>
      </div>

      <div class="row">
        <div class="col-12 table-responsive">
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Dosen</th>
                <th style="width: 150px;">Status Dosen</th>
                @if(count($groupedFinalData) > 0)
                  @foreach(array_keys($groupedFinalData->first()->first()) as $key)
                    @if(!in_array($key, ['id_penguji', 'mahasiswa', 'dosen', 'tanggal_presentasi', 'status']))
                      <th style="width: 300px;">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                    @endif
                  @endforeach
                @endif
                <th style="width: 200px;">TTD</th>
              </tr>
            </thead>
            <tbody>
              @foreach($groupedFinalData as $mahasiswa => $items)
                @foreach($items as $item)
                  <tr>
                    <td>{{ \Carbon\Carbon::parse($item['tanggal_presentasi'])->format('d/m/Y') }}</td>
                    <td>{{ $item['dosen'] }}</td>
                    <td>{{ $item['status'] }}</td>

                    @foreach ($item as $key => $value)
                      @if(!in_array($key, ['id_penguji', 'mahasiswa', 'dosen', 'tanggal_presentasi', 'status']))
                        <td>
                          @if(is_numeric($value))
                            {{ $value }}
                          @else
                            {!! trim($value) !== '' ? $value : 'N/A' !!}
                          @endif
                        </td>
                      @endif
                    @endforeach
                    <td></td>
                  </tr>
                @endforeach
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>


</body>
</html>
