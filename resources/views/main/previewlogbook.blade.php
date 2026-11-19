@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Preview Import dari Logbook</h1>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      {{-- Kasus 1: file tidak bisa dipakai sama sekali --}}
      @if ($result['error_file'])
        <div class="alert alert-danger">
          <h5><i class="icon fas fa-ban"></i> File tidak bisa diproses</h5>
          {{ $result['error_file'] }}
        </div>
        <a href="{{ route('importlogbook') }}" class="btn btn-secondary mb-4">
          <i class="fas fa-arrow-left"></i> Kembali & Upload Ulang
        </a>

      @else

        {{-- Syarat data kosong tidak terpenuhi (mis. ada admin lain menambah data) --}}
        @if (!$result['syarat']['kosong'])
          <div class="alert alert-warning">
            <h5><i class="icon fas fa-exclamation-triangle"></i> Data mahasiswa di SIPENA tidak kosong</h5>
            <ul class="mb-2">
              @foreach ($result['syarat']['alasan'] as $alasan)
                <li>{{ $alasan }}</li>
              @endforeach
            </ul>
            <a href="{{ route('reset.view') }}" class="btn btn-danger btn-sm">
              <i class="fas fa-trash-restore"></i> Ke Menu Reset Data
            </a>
          </div>
        @endif

        {{-- Ringkasan --}}
        <div class="row mb-3">
          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Total Baris</span>
                <span class="info-box-number">{{ $result['summary']['total'] }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Cocok</span>
                <span class="info-box-number">{{ $result['summary']['cocok'] }}</span>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="info-box">
              <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Tidak Cocok</span>
                <span class="info-box-number">{{ $result['summary']['tidak_cocok'] }}</span>
              </div>
            </div>
          </div>
        </div>

        @if ($result['bisa_simpan'])
          <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Semua baris cocok. Data <strong>belum tersimpan</strong>; klik <em>Simpan</em> di bawah untuk memasukkannya ke database.
          </div>
        @else
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            Ada data yang tidak cocok, jadi tombol Simpan tidak ditampilkan. Data master adalah Logbook:
            perbaiki sumbernya (misalnya tambahkan dosen yang belum ada di SIPENA, atau perbaiki data di Logbook),
            lalu export dan upload ulang. Detail masalah ada di kolom Keterangan.
          </div>
        @endif

        {{-- Tabel --}}
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Hasil Sinkronisasi per Baris</h3>
            <div class="card-tools">
              <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-default active" data-filter="semua">Semua</button>
                <button type="button" class="btn btn-default" data-filter="tidak_cocok">Tidak Cocok</button>
                <button type="button" class="btn btn-default" data-filter="cocok">Cocok</button>
              </div>
            </div>
          </div>
          <div class="card-body table-responsive p-0" style="max-height: 550px;">
            <table class="table table-bordered table-sm table-head-fixed">
              <thead>
                <tr>
                  <th>Baris</th>
                  <th>Mahasiswa</th>
                  <th>NIM</th>
                  <th>Tahun Ajaran</th>
                  <th>Perusahaan</th>
                  <th>Dosen Pembimbing (Logbook)</th>
                  <th>Status</th>
                  <th>Keterangan</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($result['rows'] as $row)
                <tr class="{{ $row['status'] === 'cocok' ? 'table-success' : 'table-danger' }}" data-status="{{ $row['status'] }}">
                  <td>{{ $row['row'] }}</td>
                  <td>
                    {{ $row['name'] }}
                    <small class="d-block text-muted">{{ $row['email'] }}</small>
                  </td>
                  <td>{{ $row['nim'] }}</td>
                  <td>{{ $row['tahun'] ?: '-' }}</td>
                  <td>
                    {{ $row['perusahaan'] }}
                    @if ($row['perusahaan'] !== '')
                      <span class="badge {{ $row['perusahaan_sudah_ada'] ? 'badge-secondary' : 'badge-info' }}">
                        {{ $row['perusahaan_sudah_ada'] ? 'sudah ada' : 'baru' }}
                      </span>
                    @endif
                  </td>
                  <td>
                    {{ $row['nama_instruktur'] }}
                    <small class="d-block text-muted">{{ $row['email_instruktur'] }}</small>
                  </td>
                  <td>
                    <span class="badge {{ $row['status'] === 'cocok' ? 'badge-success' : 'badge-danger' }}">
                      {{ $row['status'] === 'cocok' ? 'Cocok' : 'Tidak cocok' }}
                    </span>
                  </td>
                  <td>
                    @if ($row['status'] === 'cocok')
                      Siap disimpan sebagai mahasiswa baru.
                    @else
                      <ul class="pl-3 mb-0">
                        @foreach ($row['masalah'] as $m)
                          <li>{{ $m }}</li>
                        @endforeach
                      </ul>
                    @endif
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>

        {{-- Tombol --}}
        <div class="mb-4">
          @if ($result['bisa_simpan'])
            <form id="formSimpan" action="{{ route('importlogbook.store') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan {{ $result['summary']['cocok'] }} Data ke Database
              </button>
            </form>
          @endif
          <form action="{{ route('importlogbook.cancel') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">
              <i class="fas fa-times"></i> Batal
            </button>
          </form>
        </div>

      @endif
    </div>
  </section>
</div>

<div id="loadingOverlay" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,.65);">
  <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; color:#fff;">
    <div class="spinner-border" style="width:3.5rem; height:3.5rem;" role="status"></div>
    <h5 class="mt-3 mb-1">Sedang menyimpan data...</h5>
    <p class="mb-0">Mohon tunggu dan jangan menutup atau me-refresh halaman ini.</p>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    // Filter tabel
    $('[data-filter]').on('click', function () {
        var f = $(this).data('filter');
        $('[data-filter]').removeClass('active');
        $(this).addClass('active');
        $('tbody tr[data-status]').each(function () {
            $(this).toggle(f === 'semua' || $(this).data('status') === f);
        });
    });

    // Loading saat menyimpan
    $('#formSimpan').on('submit', function () {
        $('#loadingOverlay').show();
        $(this).find('button[type=submit]').prop('disabled', true);
    });

    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            $('#loadingOverlay').hide();
            $('#formSimpan button[type=submit]').prop('disabled', false);
        }
    });
});
</script>
@endsection