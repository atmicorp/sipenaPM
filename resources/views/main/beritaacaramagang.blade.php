<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Berita Acara</title>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('css/fontawesome-free/css/all.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">

  <style>
    @media print {
      @page {
        size: A4;
        margin: 3mm 10mm 10mm 10mm;
      }

      body {
        margin: 0;
        font-family: 'Times New Roman', serif;
      }

      .print-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 120%;
        height: 120px;
        z-index: 999;
      }

      .print-header img {
        width: 100%;
        height: auto;
      }

      .print-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        font-size: 14px;
        font-family: 'Times New Roman', serif;
        padding-left: 10mm;
        z-index: 999;
      }

      section.invoice {
        margin-top: 150px;
        padding-bottom: 25mm;
      }

      p {
        text-align: justify;
        font-size: 16px;
        line-height: 1.6;
        page-break-inside: avoid;
      }

      table {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <!-- Header -->
    <div class="print-header">
      <img src="{{ asset('images/kop.png') }}" alt="Kop Surat">
    </div>

    <!-- Main Content -->
    <section class="invoice">
      <div class="container mx-auto" style="padding: 10px 20px;">
        
        <div class="text-center">
          <h5><strong>BERITA ACARA SIDANG PRESENTASI MAGANG</strong></h5>
          <h5><strong>PROGRAM STUDI SARJANA TERAPAN PERANCANGAN MANUFAKTUR</strong></h5>
        </div>

        <div class="mt-5">
          <p style="font-size: 20px;">
            Pada hari 
            <strong>{{ \Carbon\Carbon::parse($datamagang['tanggal'])->translatedFormat('l, d F Y') ?? '-' }}</strong>, 
            pukul <strong>{{ $datamagang['in'] ?? '-' }} - {{ $datamagang['out'] ?? '-' }}</strong> 
            bertempat di <strong>{{ $datamagang['lokasi'] ?? '-' }}</strong>, 
            dilaksanakan <strong>Presentasi Sidang Magang Program Studi Sarjana Terapan Perancangan Manufaktur</strong> 
            terhadap mahasiswa tingkat 4 tahun perkuliahan <strong>{{ $datamagang['tahun'] ?? '-' }}</strong> 
            dengan SK Pembimbing Magang nomor <strong>{{ $datamagang['sk'] ?? '-' }}</strong> dengan judul:
          </p>
        </div>

        <div class="text-center my-4" style="font-size: 22px;">
          <strong>"{{ strip_tags($datamagang['judul'] ?? '-') }}"</strong>
        </div>

        <p style="font-size: 20px;">
          Berdasarkan hasil presentasi magang Program Studi Sarjana Terapan Perancangan Manufaktur terhadap mahasiswa tingkat 4 tahun perkuliahan {{ $datamagang['tahun'] ?? '-' }}, menyatakan bahwa laporan hasil magang dengan judul tersebut:
        </p>

        <!-- Checklist -->
          <table style="width:100%; font-size: 20px; border-collapse: collapse;" cellspacing="0" cellpadding="8">
          <tr>
            <td style="width: 30px;">1</td>
            <td style="width: 50px; border: 1px solid black;"></td>
            <td>Diterima tanpa syarat pada tanggal _______________________________________________________</td>
          </tr>
          <tr>
            <td style="width: 30px;">2</td>
            <td style="border: 1px solid black;"></td>
            <td>
              Diterima dengan syarat revisi pada tanggal _________________________________________________<br>
            </td>
          </tr>
          <tr>
            <td style="width: 30px;"></td>
            <td></td>
            <td>
            
              dan revisi selesai paling lambat tanggal ____________________________________________________
            </td>
          </tr>
        </table>

  
          <div class="row mt-3 mb-4">
          <div class="col-12">
            <p style="font-size: 20px;">
              Demikian berita acara ini dibuat dengan sebenarnya, untuk digunakan sebagaimana mestinya.
            </p>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-12">
            <p style="font-size: 20px; text-align: right;">
              Surakarta, {{ \Carbon\Carbon::parse($datamagang['tanggal'])->translatedFormat('d F Y') ?? '-' }}.
            </p>
          </div>
        </div>

         <div class="row mt-3">
          <div class="col-5">
            <p style="font-size: 20px;">Peserta Ujian :</p>
            <table class="table" style="border: none;">
              <tbody>
             
                <tr>
                  <td style="width: 300px; height: 90px; border: none; font-size: 20px;  width: 500px;">{{ $datamagang['name'] ?? '-' }}<br>  ({{ $datamagang['nim'] ?? '-' }})</td>
                
                </tr>
              
              </tbody>
            </table>
          </div>

          <div class="col-7">
          <p style="font-size: 20px;">Dosen Pembimbing dan Dosen Penguji :</p>
            <table class="table" style="border: none;">
              <tbody>
                @foreach($pengujimagang as $pnj)
                <tr>
                  <td style="height: 100px; padding-right: 10px; border: none; font-size: 20px;">{{ $pnj->status->status_dosen ?? '-' }}</td>
                  <td style="height: 100px; padding-right: 10px; border: none; font-size: 20px;  width: 500px;">
                     {{ $pnj->userdosen->name ?? '-' }}{{ isset($pnj->userdosen->details->gelar_belakang) ? ', '.$pnj->userdosen->details->gelar_belakang : '' }}<br>
                      ({{ $pnj->userdosen->details->nidn ?? '-' }})
                  </td>
                  <td style="height: 100px; width: 190px; border: none; font-size: 20px;">{{ $loop->iteration }}.</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        

      </div>
    </section>
  </div>

  <div class="print-footer">
    F SPMI.9.2/0/01042018
  </div>

  <script>
    window.onload = function() {
      window.print();
    };
  </script>
</body>
</html>
