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

      .checkbox-list {
        font-size: 20px;
        list-style: decimal;
        padding-left: 20px;
      }

      .checkbox-list li {
        margin-bottom: 20px;
      }

      .checkbox-container label.fontnetral {
        font-weight: normal !important;
      }

      .checkbox-container {
        display: flex;
        align-items: flex-start;
      }

      .checkbox-container input[type="checkbox"] {
        transform: scale(4);
        margin-right: 30px;
        margin-left: 20px;
        margin-top: 5px;
        cursor: pointer;
      }

      .checkbox-container label {
        line-height: 1.6;
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
      <div class="container mx-auto" style="padding-left: 20px; padding-right: 20px; padding-top: 10px;">
        <div class="row">
          <div class="col-12 text-center">
            <h5><strong>BERITA ACARA PRESENTASI <span style="text-transform: uppercase;">{{$kategori->nama_kategori}}</span></strong></h5>
            <h5><strong>PROGRAM STUDI SARJANA TERAPAN PERANCANGAN MANUFAKTUR</strong></h5>
          </div>
        </div>

        <div class="row mt-5">
          <div class="col-12">
            <p style="font-size: 20px;">
              Pada hari <strong>{{ \Carbon\Carbon::parse($jadwalta->tanggal_presentasi)->translatedFormat('l, d F Y') ?? '-' }}</strong>, Pukul <strong>{{$jadwalta->jam_presentasi}} - {{$jadwalta->jam_presentasi_selesai}}</strong> bertempat di <strong>{{$jadwalta->lokasi}}</strong>, dilaksanakan <strong>Presentasi {{$kategori->nama_kategori}}</strong> terhadap mahasiswa <strong>Program Studi Perancangan Manufaktur</strong> tahun perkuliahan <strong>{{$kelompok->tahun_perkuliahan}}</strong> dengan SK Pembimbing Tugas Akhir nomor <strong>{{$kelompok->sk}}</strong> dengan judul:
            </p>
          </div>
        </div>

        <div class="row mb-4 mt-4">
          <div class="col-12 text-center" style="font-size: 26px;">
            <h5><strong>"{{ strip_tags($kelompok->judul_ta) }}"</strong></h5>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-12">
            <p style="font-size: 20px;">
              Berdasarkan hasil Presentasi {{$kategori->nama_kategori}} terhadap mahasiswa tingkat 4
              Program Studi Sarjana Terapan Perancangan Manufaktur Politeknik ATMI Surakarta tahun perkuliahan {{$kelompok->tahun_perkuliahan}}, 
              menyatakan bahwa laporan hasil dengan judul tersebut:
          </div>
        </div>

        <!-- List Checklist dengan Nomor -->
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
              Surakarta, {{ \Carbon\Carbon::parse($jadwalta->tanggal_presentasi)->translatedFormat('d F Y') ?? '-' }}.
            </p>
          </div>
        </div>

        <div class="row mt-3">
          <div class="col-5">
            <p style="font-size: 20px;">Kelompok Tugas Akhir {{$kelompok->nama_kelompok}}</p>
            <table class="table" style="border: none;">
              <tbody>
                @foreach($peserta as $pst)
                <tr>
                  <td style="width: 300px; height: 90px; border: none; font-size: 20px;  width: 500px;">{{$pst->usermahasiswaTA->name}} <br> ({{$pst->usermahasiswaTA->details->nim}})</td>
                  <td style="width: 300px; height: 90px; text-align: left; border: none; font-size: 20px;">{{ $loop->iteration }}.</td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          <div class="col-7">
          <p style="font-size: 20px;">Dosen Pembimbing dan Dosen Penguji {{$kelompok->nama_kelompok}}</p>
            <table class="table" style="border: none;">
              <tbody>
                @foreach($penguji as $pnj)
                <tr>
                  <td style="height: 100px; padding-right: 10px; border: none; font-size: 20px;">{{$pnj->statusdosenTA->status_dosen}}</td>
                  <td style="height: 100px; padding-right: 10px; border: none; font-size: 20px;  width: 500px;">
                    {{$pnj->userdosenTA->details->gelar_depan}} {{$pnj->userdosenTA->name}}, {{$pnj->userdosenTA->details->gelar_belakang}} <br>
                    ({{$pnj->userdosenTA->details->nidn}})
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

  <!-- Footer -->
  <div class="print-footer">
    F SPMI.9.2/0/01042018
  </div>

  <!-- Auto print -->
  <script>
    window.onload = function () {
      window.print();
    };
  </script>
</body>
</html>
