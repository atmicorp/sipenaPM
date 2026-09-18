@extends('master.layoutsmaster')
@section('styles')
<style>
   /* custom css */
</style>
@endsection
@section('content')
    <!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        
      </div>
    </div><!-- /.container-fluid -->
  </section>

  @if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  @if(session('error'))
  <div class="alert alert-danger">
    {{ session('error') }}
  </div>
  @endif

  @if(session('success'))
  <div class="alert alert-success">
    {{ session('success') }}
  </div>
  @endif

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- SELECT2 EXAMPLE -->
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title">Penempatan Tugas Praktik Kerja Mahasiswa (Magang)</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">

          @if ($mahasiswa->isEmpty())
            <div class="alert alert-info mb-0">
              Semua mahasiswa saat ini sudah punya data penempatan magang. Tidak ada mahasiswa
              yang perlu ditambahkan lewat form ini.
            </div>
          @else
          <div class="row">
            <!-- /.col -->
            <div class="col-md-12">
              <form method="POST" action="{{route('storepenempatanmagang')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                  
                  <div class="border border-primary p-3 rounded mb-3">
                    
                    <table class="table table-striped" id="positionsTable">
                      <thead>
                        <tr>
                          <th>Nama Mahasiswa*</th>
                          <th>Perusahaan*</th>
                          <th>Tanggal Presentasi*</th>
                          <th>Jam Mulai*</th>
                          <th>Jam Selesai</th>
                          <th>Lokasi</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody id="positionsBody">
                        <tr>
                          <td>
                            <select name="peserta[]" class="form-control" required>
                              <option value="" disabled selected>Nama Mahasiswa</option>
                              @foreach ($mahasiswa as $mhs)
                              <option value="{{ $mhs->id }}">
                              <span>({{ $mhs->details->nim }})</span> {{ $mhs->name }}
                              </option>
                              @endforeach
                            </select>
                          </td>
                          <td>
                            <select name="perusahaan[]" class="form-control" required>
                              <option value="" disabled selected>Perusahaan</option>
                              @foreach ($perusahaan as $prsh)
                              <option value="{{ $prsh->id }}">
                                {{ $prsh->nama }}
                              </option>
                              @endforeach
                            </select>
                          </td>
                          <td>
                          <div class="input-group">
                              <input type="date" class="form-control" name="tanggal_presentasi[]" placeholder="Pilih Tanggal" required>
                          </div>
                          </td>
                          <td>
                            <div class="input-group">
                              <input type="time" class="form-control" name="jam_presentasi[]" placeholder="Pilih Jam" required>
                            </div>
                          </td>
                          <td>
                            <div class="input-group">
                              <input type="time" class="form-control" name="jam_presentasi_selesai[]" placeholder="Pilih Jam">
                            </div>
                          </td>
                          <td>
                            <input type="text" class="form-control" name="lokasi[]" placeholder="Lokasi Presentasi">
                          </td>
                          <td>
                            <button type="button" class="btn btn-danger btn-sm delete-row">
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="d-flex mb-3">
                      <button type="button" id="addRow" class="btn btn-success btn-sm" style="margin-right: 10px;">
                        <i class="fas fa-plus"></i> Tambah item
                      </button>
                      <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modalTambahPerusahaan">
                        <i class="fas fa-building"></i> Tambah Perusahaan Baru
                      </button>
                    </div>
                  </div>             
            
                </div>
                <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-floppy-o"></i> Simpan Data
                </button>
                </div>
              </form>

              <div class="modal fade" id="modalTambahPerusahaan" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form action="{{ route('storeperusahaanmagang') }}" method="POST">
                      @csrf
                      <div class="modal-header">
                        <h5 class="modal-title">Tambah Perusahaan Baru</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                          <label>Nama Perusahaan <span class="text-danger">*</span></label>
                          <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                          <label>Alamat</label>
                          <textarea name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                        <small class="text-muted">
                          Setelah disimpan, perusahaan ini otomatis muncul di dropdown "Perusahaan" pada tabel di atas.
                        </small>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
      <!-- /.card -->
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>

@endsection
@section('scripts')
<!-- date-range-picker -->
<script src="{{asset('daterangepicker/daterangepicker.js')}}"></script>
<!-- InputMask -->
<script src="{{asset('moment/moment.min.js')}}"></script>
<script src="{{asset('inputmask/jquery.inputmask.min.js')}}"></script>
<!-- Select2 -->
<script src="{{asset('select2/select2/js/select2.full.min.js')}}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{asset('tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js')}}"></script>

<script>
  // Menambahkan baris baru saat tombol "Tambah item" diklik
  const addRowBtn = document.getElementById('addRow');
  if (addRowBtn) {
    addRowBtn.addEventListener('click', function() {
      const tbody = document.getElementById('positionsBody');
      const newRow = document.createElement('tr');
      newRow.innerHTML = `
               <td>
                              <select name="peserta[]" class="form-control" required>
                                <option value="" disabled selected>Nama Mahasiswa</option>
                                @foreach ($mahasiswa as $mhs)
                                <option value="{{ $mhs->id }}">
                                <span>({{ $mhs->details->nim }})</span> {{ $mhs->name }}
                                </option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                              <select name="perusahaan[]" class="form-control" required>
                                <option value="" disabled selected>Perusahaan</option>
                                @foreach ($perusahaan as $prsh)
                                <option value="{{ $prsh->id }}">
                                  {{ $prsh->nama }}
                                </option>
                                @endforeach
                              </select>
                            </td>
                            <td>
                            <div class="input-group">
                                <input type="date" class="form-control" name="tanggal_presentasi[]" placeholder="Pilih Tanggal" required>
                            </div>
                            </td>
                            <td>
                              <div class="input-group">
                                <input type="time" class="form-control" name="jam_presentasi[]" placeholder="Pilih Jam" required>
                              </div>
                            </td>
                            <td>
                              <div class="input-group">
                                <input type="time" class="form-control" name="jam_presentasi_selesai[]" placeholder="Pilih Jam">
                              </div>
                            </td>
                            <td>
                              <input type="text" class="form-control" name="lokasi[]" placeholder="Lokasi Presentasi">
                            </td>
                            <td>
                              <button type="button" class="btn btn-danger btn-sm delete-row">
                                <i class="fas fa-trash"></i>
                              </button>
                            </td>
        `;

      tbody.appendChild(newRow);
    });
  }

  // Menghapus baris saat tombol hapus diklik
  const positionsBody = document.getElementById('positionsBody');
  if (positionsBody) {
    positionsBody.addEventListener('click', function(e) {
      if (e.target && e.target.closest('.delete-row')) {
        const row = e.target.closest('tr'); // Mencari baris terdekat
        if (row) {
          row.remove(); // Menghapus baris
        }
      }
    });
  }
</script>
@endsection