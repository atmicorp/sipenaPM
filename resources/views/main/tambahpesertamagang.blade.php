@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Tambah Data Peserta Magang</h1>
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
      <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
          <ul class="nav nav-tabs" id="tabTambah" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" id="tab-manual-link" data-toggle="pill" href="#tab-manual" role="tab">
                <i class="fas fa-user-plus"></i> Tambah Manual
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="tab-excel-link" data-toggle="pill" href="#tab-excel" role="tab">
                <i class="fas fa-file-excel"></i> Import Excel
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" id="tab-logbook-link" data-toggle="pill" href="#tab-logbook" role="tab">
                <i class="fas fa-file-import"></i> Import dari Logbook
              </a>
            </li>
          </ul>
        </div>

        <div class="card-body">
          <div class="tab-content">

            {{-- ============ TAB 1: TAMBAH MANUAL ============ --}}
            <div class="tab-pane fade show active" id="tab-manual" role="tabpanel">
              <p class="text-muted">
                Untuk menambah mahasiswa susulan yang tidak ada di Logbook. Satu form langsung membuat
                akun mahasiswa, penempatan magang, dan dosen pembimbingnya.
              </p>

              <form id="formManual" action="{{ route('tambahpesertamagang.manual') }}" method="POST">
                @csrf

                <h5 class="mb-3">Data Mahasiswa</h5>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="name">Nama <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="nama@student.atmi.ac.id" required>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="nim">NIM <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nim" name="nim" value="{{ old('nim') }}" required>
                  </div>
                </div>

                <h5 class="mt-3 mb-3">Perusahaan Magang</h5>
                <div class="form-row">
                  <div class="form-group col-md-4">
                    <label for="perusahaan_id">Pilih Perusahaan</label>
                    <select class="form-control" id="perusahaan_id" name="perusahaan_id">
                      <option value="">-- Pilih perusahaan --</option>
                      @foreach ($perusahaan as $p)
                        <option value="{{ $p->id }}" @selected(old('perusahaan_id') == $p->id)>{{ $p->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-4">
                    <label for="perusahaan_baru">atau Perusahaan Baru</label>
                    <input type="text" class="form-control" id="perusahaan_baru" name="perusahaan_baru" value="{{ old('perusahaan_baru') }}" placeholder="Ketik nama perusahaan baru">
                  </div>
                  <div class="form-group col-md-4" id="boxAlamat" style="display:none;">
                    <label for="alamat_perusahaan">Alamat Perusahaan Baru</label>
                    <input type="text" class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" value="{{ old('alamat_perusahaan') }}">
                  </div>
                </div>

                <h5 class="mt-3 mb-3">Dosen Pembimbing</h5>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="id_dosen">Dosen Pembimbing <span class="text-danger">*</span></label>
                    <select class="form-control" id="id_dosen" name="id_dosen" required>
                      <option value="">-- Pilih dosen --</option>
                      @foreach ($dosen as $d)
                        @php
                          $namaDosen = trim(($d->details?->gelar_depan ?? '') . ' ' . $d->name)
                              . ($d->details?->gelar_belakang ? ', ' . $d->details->gelar_belakang : '');
                        @endphp
                        <option value="{{ $d->id }}" @selected(old('id_dosen') == $d->id)>{{ $namaDosen }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>

                @php
                    $tahunDefault = now()->month >= 8
                        ? now()->year . '/' . (now()->year + 1)
                        : (now()->year - 1) . '/' . now()->year;
                @endphp
                <h5 class="mt-3 mb-3">Tahun Perkuliahan</h5>
                  <div class="form-row">
                    <div class="form-group col-md-3">
                      <label for="tahun">Tahun Ajaran <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="tahun" name="tahun"
                            value="{{ old('tahun', $tahunDefault) }}" placeholder="2026/2027"
                            pattern="\d{4}/\d{4}" required>
                    </div>
                  </div>

                <h5 class="mt-3 mb-1">Jadwal Presentasi <small class="text-muted">(boleh dikosongkan, bisa diisi nanti)</small></h5>
                <div class="form-row mt-2">
                  <div class="form-group col-md-3">
                    <label for="tanggal_presentasi">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_presentasi" name="tanggal_presentasi" value="{{ old('tanggal_presentasi') }}">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="jam_presentasi">Jam Mulai</label>
                    <input type="time" class="form-control" id="jam_presentasi" name="jam_presentasi" value="{{ old('jam_presentasi') }}">
                  </div>
                  <div class="form-group col-md-2">
                    <label for="jam_presentasi_selesai">Jam Selesai</label>
                    <input type="time" class="form-control" id="jam_presentasi_selesai" name="jam_presentasi_selesai" value="{{ old('jam_presentasi_selesai') }}">
                  </div>
                  <div class="form-group col-md-5">
                    <label for="lokasi">Lokasi</label>
                    <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ old('lokasi') }}">
                  </div>
                </div>

                <button type="submit" class="btn btn-success mt-2">
                  <i class="fas fa-save"></i> Simpan Mahasiswa
                </button>
              </form>
            </div>

            {{-- ============ TAB 2: IMPORT EXCEL ============ --}}
            <div class="tab-pane fade" id="tab-excel" role="tabpanel">
              <p>
                Isi data awal peserta magang (mahasiswa, perusahaan, dan penempatannya) lewat satu file Excel
                sesuai template. Setelah upload, Anda melihat preview dulu sebelum data disimpan.
              </p>
              <a href="{{ route('importpesertamagang') }}" class="btn btn-primary">
                <i class="fas fa-file-excel"></i> Buka Import Excel
              </a>
              <a href="{{ route('importpesertamagang.template') }}" class="btn btn-outline-primary">
                <i class="fas fa-download"></i> Download Template
              </a>
            </div>

            {{-- ============ TAB 3: IMPORT DARI LOGBOOK ============ --}}
            <div class="tab-pane fade" id="tab-logbook" role="tabpanel">
              <p>
                Pindahkan data mahasiswa magang <strong>aktif</strong> dari aplikasi Logbook, lengkap dengan
                perusahaan dan dosen pembimbingnya. Dosen dicocokkan lewat email, dan data hanya bisa
                disimpan kalau semua baris cocok.
              </p>

              @if ($syaratLogbook['kosong'])
                <div class="alert alert-success">
                  <i class="fas fa-check-circle"></i> Data mahasiswa di SIPENA kosong. Import dari Logbook bisa dilakukan.
                </div>
                <a href="{{ route('importlogbook') }}" class="btn btn-primary">
                  <i class="fas fa-file-import"></i> Buka Import dari Logbook
                </a>
              @else
                <div class="alert alert-warning">
                  <h5><i class="icon fas fa-exclamation-triangle"></i> Import dari Logbook belum bisa dilakukan</h5>
                  <ul class="mb-2">
                    @foreach ($syaratLogbook['alasan'] as $alasan)
                      <li>{{ $alasan }}</li>
                    @endforeach
                  </ul>
                  <small class="d-block mb-2">
                    Import dari Logbook hanya untuk data awal periode. Untuk tambahan mahasiswa susulan, pakai tab Tambah Manual.
                  </small>
                  <a href="{{ route('reset.view') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash-restore"></i> Ke Menu Reset Data
                  </a>
                </div>
              @endif
            </div>

          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function () {
    var $select = $('#perusahaan_id');
    var $baru = $('#perusahaan_baru');
    var $boxAlamat = $('#boxAlamat');

    // Kalau mengetik perusahaan baru, pilihan dari daftar dinonaktifkan (tidak ikut terkirim)
    function sinkronPerusahaan() {
        var adaBaru = $.trim($baru.val()) !== '';
        $select.prop('disabled', adaBaru);
        $boxAlamat.toggle(adaBaru);
        if (adaBaru) { $select.val(''); }
    }
    $baru.on('input', sinkronPerusahaan);
    sinkronPerusahaan();

    // Cegah klik ganda saat menyimpan
    $('#formManual').on('submit', function () {
        $(this).find('button[type=submit]').prop('disabled', true);
    });
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            $('#formManual button[type=submit]').prop('disabled', false);
        }
    });
});
</script>
@endsection