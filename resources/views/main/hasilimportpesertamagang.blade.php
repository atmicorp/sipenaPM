@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Hasil Import Peserta Magang</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Data berhasil disimpan. Ringkasan:
      </div>

      <div class="row mb-3">
        @foreach ([['Mahasiswa', 'mahasiswa'], ['Perusahaan', 'perusahaan'], ['Penempatan Magang', 'magang']] as [$label, $key])
        <div class="col-md-4">
          <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">{{ $label }}</span>
              <span class="info-box-number">
                {{ $result['summary'][$key]['valid'] }} tersimpan
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
        $problemRows = fn ($rows) => array_filter($rows, fn ($r) => $r['status'] !== 'valid');
        $mahasiswaProblems = $problemRows($result['mahasiswa']);
        $perusahaanProblems = $problemRows($result['perusahaan']);
        $magangProblems = $problemRows($result['magang']);
      @endphp

      @if (count($mahasiswaProblems) || count($perusahaanProblems) || count($magangProblems))
      <div class="card">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-exclamation-triangle text-warning"></i> Baris yang dilewati / gagal</h3>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-bordered table-sm">
            <thead>
              <tr><th>Sheet</th><th>Baris</th><th>Data</th><th>Status</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
              @foreach ($mahasiswaProblems as $row)
              <tr class="{{ $row['status'] === 'error' ? 'table-danger' : '' }}">
                <td>Sheet1 (Mahasiswa)</td>
                <td>{{ $row['row'] }}</td>
                <td>{{ $row['name'] }} — {{ $row['nim'] }}</td>
                <td><span class="badge {{ $row['status'] === 'error' ? 'badge-danger' : 'badge-secondary' }}">{{ ucfirst($row['status']) }}</span></td>
                <td>{{ $row['message'] }}</td>
              </tr>
              @endforeach
              @foreach ($perusahaanProblems as $row)
              <tr class="{{ $row['status'] === 'error' ? 'table-danger' : '' }}">
                <td>Sheet2 (Perusahaan)</td>
                <td>{{ $row['row'] }}</td>
                <td>{{ $row['nama'] }}</td>
                <td><span class="badge {{ $row['status'] === 'error' ? 'badge-danger' : 'badge-secondary' }}">{{ ucfirst($row['status']) }}</span></td>
                <td>{{ $row['message'] }}</td>
              </tr>
              @endforeach
              @foreach ($magangProblems as $row)
              <tr class="{{ $row['status'] === 'error' ? 'table-danger' : '' }}">
                <td>Sheet3 (Penempatan)</td>
                <td>{{ $row['row'] }}</td>
                <td>{{ $row['nim'] }} — {{ $row['nama_perusahaan'] }}</td>
                <td><span class="badge {{ $row['status'] === 'error' ? 'badge-danger' : 'badge-secondary' }}">{{ ucfirst($row['status']) }}</span></td>
                <td>{{ $row['message'] }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      @endif

      <a href="{{ route('importpesertamagang') }}" class="btn btn-primary">
        <i class="fas fa-upload"></i> Import Lagi
      </a>
      <a href="{{ route('viewpenempatanmagang') }}" class="btn btn-outline-secondary">
        <i class="fas fa-list"></i> Lihat Data Magang
      </a>

    </div>
  </section>
</div>
@endsection