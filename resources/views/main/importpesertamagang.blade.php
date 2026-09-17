@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Import Peserta Magang</h1>
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
          <h3 class="card-title"><i class="fas fa-file-excel"></i> Upload Data Awal Peserta Magang</h3>
        </div>
        <div class="card-body">

          <p>
            Gunakan menu ini untuk mengisi data awal peserta magang di setiap awal periode
            (mahasiswa, perusahaan tempat magang, dan penempatannya) lewat satu file Excel.
          </p>
          <p>
            Belum punya file template, atau ragu formatnya sudah benar? Download dulu template
            resminya di sini, lalu isi sesuai petunjuk di sheet "Petunjuk":
          </p>

          <a href="{{ route('importpesertamagang.template') }}" class="btn btn-outline-primary mb-4">
            <i class="fas fa-download"></i> Download Template Excel
          </a>

          <hr>

          <form action="{{ route('importpesertamagang.preview') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <label for="file">File Excel (.xlsx)</label>
              <div class="custom-file">
                <input type="file" name="file" class="custom-file-input" id="file" accept=".xlsx" required>
                <label class="custom-file-label" for="file">Pilih file...</label>
              </div>
              <small class="form-text text-muted">
                Data belum langsung tersimpan — setelah upload, Anda akan melihat pratinjau (preview)
                dulu sebelum benar-benar disimpan ke sistem.
              </small>
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-eye"></i> Upload &amp; Lihat Preview
            </button>
          </form>

        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    $('.custom-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});
</script>
@endsection