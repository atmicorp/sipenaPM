@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Hasil Import dari Logbook</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <div class="alert alert-success">
        <h5><i class="icon fas fa-check"></i> Import berhasil</h5>
        Semua data sudah tersimpan ke database.
      </div>

      <div class="row mb-3">
        @foreach ([
            ['Mahasiswa Dibuat', $result['mahasiswa'], 'fa-users', 'bg-success'],
            ['Perusahaan Baru', $result['perusahaan_baru'], 'fa-building', 'bg-info'],
            ['Penempatan Magang', $result['penempatan'], 'fa-briefcase', 'bg-primary'],
            ['Dosen Pembimbing', $result['pembimbing'], 'fa-user-tie', 'bg-warning'],
        ] as [$label, $jumlah, $ikon, $warna])
        <div class="col-md-3">
          <div class="info-box">
            <span class="info-box-icon {{ $warna }}"><i class="fas {{ $ikon }}"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">{{ $label }}</span>
              <span class="info-box-number">{{ $jumlah }}</span>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Jadwal presentasi (tanggal, jam, lokasi) belum terisi. Lengkapi lewat menu setup data penempatan magang.
      </div>

      <div class="card">
        <div class="card-header"><h3 class="card-title">Daftar yang Disimpan</h3></div>
        <div class="card-body table-responsive p-0" style="max-height: 450px;">
          <table class="table table-bordered table-sm table-head-fixed">
            <thead>
              <tr><th>No</th><th>Nama</th><th>NIM</th><th>Perusahaan</th><th>Dosen Pembimbing</th></tr>
            </thead>
            <tbody>
              @foreach ($result['rows'] as $row)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row['name'] }}<small class="d-block text-muted">{{ $row['email'] }}</small></td>
                <td>{{ $row['nim'] }}</td>
                <td>{{ $row['perusahaan'] }}</td>
                <td>{{ $row['nama_instruktur'] }}<small class="d-block text-muted">{{ $row['email_instruktur'] }}</small></td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <div class="mb-4">
        <a href="{{ route('viewpenempatanmagang') }}" class="btn btn-primary">
          <i class="fas fa-list"></i> Lihat Data Penempatan
        </a>
      </div>

    </div>
  </section>
</div>
@endsection