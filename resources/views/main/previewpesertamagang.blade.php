@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Preview Import Peserta Magang</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        Data <strong>belum tersimpan</strong>. Periksa dulu tabel di bawah. Baris berwarna
        <span class="badge badge-success">hijau</span> akan disimpan, baris
        <span class="badge badge-secondary">abu-abu</span> dilewati (misal karena datanya sudah ada),
        dan baris <span class="badge badge-danger">merah</span> gagal / perlu diperbaiki di Excel-nya.
      </div>

      <div class="row mb-3">
        @foreach ([['Mahasiswa', 'mahasiswa'], ['Perusahaan', 'perusahaan'], ['Penempatan Magang', 'magang']] as [$label, $key])
        <div class="col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-table"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">{{ $label }}</span>
              <span class="info-box-number">
                {{ $result['summary'][$key]['valid'] }} siap disimpan
                <small class="d-block text-muted">
                  {{ $result['summary'][$key]['dilewati'] }} dilewati,
                  {{ $result['summary'][$key]['error'] }} error,
                  dari {{ $result['summary'][$key]['total'] }} baris
                </small>
              </span>
            </div>
          </div>
        </div>
        @endforeach
      </div>

      @php
        $badge = fn ($status) => match ($status) {
            'valid' => 'badge-success',
            'dilewati' => 'badge-secondary',
            'error' => 'badge-danger',
            default => 'badge-light',
        };
        $rowClass = fn ($status) => match ($status) {
            'valid' => 'table-success',
            'dilewati' => '',
            'error' => 'table-danger',
            default => '',
        };
      @endphp

      <div class="card">
        <div class="card-header"><h3 class="card-title">Sheet1 — Data Mahasiswa</h3></div>
        <div class="card-body table-responsive p-0" style="max-height: 400px;">
          <table class="table table-bordered table-sm">
            <thead>
              <tr><th>Baris</th><th>Nama</th><th>Email</th><th>NIM</th><th>Status</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
              @forelse ($result['mahasiswa'] as $row)
              <tr class="{{ $rowClass($row['status']) }}">
                <td>{{ $row['row'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['email'] }}</td>
                <td>{{ $row['nim'] }}</td>
                <td><span class="badge {{ $badge($row['status']) }}">{{ ucfirst($row['status']) }}</span></td>
                <td>{{ $row['message'] }}</td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center text-muted">Tidak ada data pada Sheet1.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h3 class="card-title">Sheet2 — Data Perusahaan Magang</h3></div>
        <div class="card-body table-responsive p-0" style="max-height: 300px;">
          <table class="table table-bordered table-sm">
            <thead>
              <tr><th>Baris</th><th>Nama Perusahaan</th><th>Status</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
              @forelse ($result['perusahaan'] as $row)
              <tr class="{{ $rowClass($row['status']) }}">
                <td>{{ $row['row'] }}</td>
                <td>{{ $row['nama'] }}</td>
                <td><span class="badge {{ $badge($row['status']) }}">{{ ucfirst($row['status']) }}</span></td>
                <td>{{ $row['message'] }}</td>
              </tr>
              @empty
              <tr><td colspan="4" class="text-center text-muted">Tidak ada data pada Sheet2.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><h3 class="card-title">Sheet3 — Penempatan Peserta Magang</h3></div>
        <div class="card-body table-responsive p-0" style="max-height: 300px;">
          <table class="table table-bordered table-sm">
            <thead>
              <tr><th>Baris</th><th>NIM</th><th>Nama Perusahaan</th><th>Status</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
              @forelse ($result['magang'] as $row)
              <tr class="{{ $rowClass($row['status']) }}">
                <td>{{ $row['row'] }}</td>
                <td>{{ $row['nim'] }}</td>
                <td>{{ $row['nama_perusahaan'] }}</td>
                <td><span class="badge {{ $badge($row['status']) }}">{{ ucfirst($row['status']) }}</span></td>
                <td>{{ $row['message'] }}</td>
              </tr>
              @empty
              <tr><td colspan="5" class="text-center text-muted">Tidak ada data pada Sheet3.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div class="mb-4">
        <form action="{{ route('importpesertamagang.store') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Simpan Data yang Valid
          </button>
        </form>
        <form action="{{ route('importpesertamagang.cancel') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-outline-secondary">
            <i class="fas fa-times"></i> Batal
          </button>
        </form>
      </div>

    </div>
  </section>
</div>
@endsection