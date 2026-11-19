@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Import Mahasiswa dari Logbook</h1>
        </div>
      </div>
    </div>
  </section>

  @if ($errors->any())
    <div class="alert alert-danger mx-3">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger mx-3">{{ session('error') }}</div>
  @endif

  @if(session('success'))
    <div class="alert alert-success mx-3">{{ session('success') }}</div>
  @endif

  <section class="content">
    <div class="container-fluid">
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title"><i class="fas fa-file-import"></i> Import Data Mahasiswa Magang dari Logbook</h3>
        </div>
        <div class="card-body">

          <p>
            Menu ini memindahkan data mahasiswa magang <strong>aktif</strong> dari aplikasi Logbook
            ke SIPENA, lengkap dengan perusahaan dan dosen pembimbingnya.
            Dosen di Logbook dicocokkan dengan Dosen di SIPENA berdasarkan <strong>email</strong>.
          </p>
          <ul>
            <li>Data master adalah Logbook. Kalau ada dosen di Logbook yang belum ada di SIPENA, data tidak bisa disimpan.</li>
            <li>Data baru tersimpan setelah Anda klik <em>Simpan</em> di halaman preview, dan hanya kalau <strong>semua baris cocok</strong>.</li>
            <li>Nilai tidak ikut dipindahkan. Jadwal presentasi diisi setelahnya lewat menu setup data magang.</li>
          </ul>

          <hr>

          @if (!$syarat['kosong'])
            <div class="alert alert-warning">
              <h5><i class="icon fas fa-exclamation-triangle"></i> Import belum bisa dilakukan</h5>
              <p class="mb-1">Import dari Logbook hanya boleh dilakukan saat data mahasiswa benar-benar kosong:</p>
              <ul class="mb-2">
                @foreach ($syarat['alasan'] as $alasan)
                  <li>{{ $alasan }}</li>
                @endforeach
              </ul>
              <a href="{{ route('reset.view') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-trash-restore"></i> Ke Menu Reset Data
              </a>
            </div>
          @else
            <div class="alert alert-success">
              <i class="fas fa-check-circle"></i>
              Data mahasiswa di SIPENA sudah kosong. Import bisa dilakukan.
            </div>

            <form id="formUpload" action="{{ route('importlogbook.preview') }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <label for="file">File Excel hasil export Logbook (.xlsx)</label>
                <div class="custom-file">
                  <input type="file" name="file" class="custom-file-input" id="file" accept=".xlsx" required>
                  <label class="custom-file-label" for="file">Pilih file...</label>
                </div>
                <small class="form-text text-muted">
                  Ambil file dari Logbook (menu export mahasiswa magang aktif). Setelah upload,
                  Anda akan melihat preview dulu sebelum data disimpan.
                </small>
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-eye"></i> Upload &amp; Sinkronkan
              </button>
            </form>
          @endif

        </div>
      </div>
    </div>
  </section>
</div>

{{-- Popup loading: menutup layar supaya admin tidak klik sembarangan saat proses berjalan --}}
<div id="loadingOverlay" style="display:none; position:fixed; inset:0; z-index:99999; background:rgba(0,0,0,.65);">
  <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); text-align:center; color:#fff;">
    <div class="spinner-border" style="width:3.5rem; height:3.5rem;" role="status"></div>
    <h5 class="mt-3 mb-1">Sedang menyinkronkan data...</h5>
    <p class="mb-0">Mohon tunggu dan jangan menutup atau me-refresh halaman ini.</p>
  </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('.custom-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Submit hanya terjadi setelah validasi browser lolos (file sudah dipilih)
    $('#formUpload').on('submit', function () {
        $('#loadingOverlay').show();
        $(this).find('button[type=submit]').prop('disabled', true);
    });

    // Kalau admin menekan tombol Back, jangan biarkan overlay tersangkut
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            $('#loadingOverlay').hide();
            $('#formUpload button[type=submit]').prop('disabled', false);
        }
    });
});
</script>
@endsection