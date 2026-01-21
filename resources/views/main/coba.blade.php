@extends('master.layoutsmaster')
@section('styles')
  <!-- DataTables -->
 <link rel="stylesheet" href="{{asset('dtable/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
 <link rel="stylesheet" href="{{asset('dtable/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
 <link rel="stylesheet" href="{{asset('dtable/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
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
            <h3 class="card-title">Jadwal Tugas Akhir</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                
            
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>                
                        <th>Nama Mahasiswa</th>
                        <th>Kategoi TA</th>
                        <th>Tanggal Presentasi</th>
                        <th>Lokasi Presentasi</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pesertaTA as $peserta)
                        <tr>
                            
                            <td>{{$loop->iteration}}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-start mb-2">
                                        <img src="{{  $peserta->usermahasiswaTA->details->photo ? 'data:image/jpeg;base64,' .  $peserta->usermahasiswaTA->details->photo : asset('images/pp.jpg') }}" 
                                                    class="img-circle elevation-2 mt-1" 
                                                    alt="Foto Penguji" 
                                                    style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                            <div class="d-flex flex-column ps-3">  <!-- Padding kiri untuk beri jarak -->
                                                <strong class="d-block mb-1">{{ $peserta->usermahasiswaTA->name }}</strong>
                                                <span class="text-muted" style="font-size: 12px;">NIM : {{ $peserta->usermahasiswaTA->details->nim }}</span>                              
                                            </div>                                   
                                    </div>
                                </div>
                            </td> 
                            <td>
                            {{ $peserta->kategoriTA->nama_kategori}}
                            </td> 
                           
                            <td>
                                <div>
                                    <i class="fas fa-calendar-alt"></i>    
                                    {{ $peserta->tanggal_presentasi 
                                        ? \Carbon\Carbon::parse($peserta->tanggal_presentasi)->translatedFormat('l, d F Y') 
                                        : '-' 
                                    }},
                                </div>
                                <hr>
                                <div>
                                    <i class="fas fa-clock"></i>  
                                    {{ $peserta->jam_presentasi && $peserta->jam_presentasi_selesai 
                                        ? \Carbon\Carbon::parse($peserta->jam_presentasi)->format('H:i') . ' - ' . 
                                        \Carbon\Carbon::parse($peserta->jam_presentasi_selesai)->format('H:i') . ' WIB'
                                        : '-' 
                                    }}
                                </div>
                            </td>
                            
                            <td>{{ $peserta->lokasi ? $peserta->lokasi : '-' }}</td>
                            <td>
                                <!-- Membungkus tombol dalam div dengan kelas flex-column untuk responsivitas -->
                                <div class="btn-group-vertical">
                                    <!-- Tombol Laporan Magang -->
                                    <button class="btn btn-sm btn-info btn-edit mb-2" data-toggle="modal" data-target="#LaporanMagangModal{{ $peserta->id_mahasiswa }}">
                                        <i class="fa fa-file-alt"></i> Laporan
                                    </button>

                                    <!-- Tombol Edit -->
                                    <button class="btn btn-sm btn-primary btn-edit mb-2" data-toggle="modal" data-target="#editjadwalModal-{{ $peserta->id }}">
                                        <i class="fas fa-edit"></i> Edit jadwal
                                    </button>
                                   

                    
                                </div>
                            </td>
                        </tr>

                         <!-- Modal jadwal    -->    
                        <div class="modal fade" id="editjadwalModal-{{ $peserta->id }}" tabindex="-1" role="dialog" aria-labelledby="editjadwalModal" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">  
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Data Tugas Akhir</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    </div>

                                    <!-- Tambahkan max-height & scroll -->
                                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                                        <div class="mb-3">
                                            <!-- Nama Mahasiswa -->
                                            <div class="form-group">
                                                    <label for="nama">Nama Mahasiswa</label>
                                                    <input type="text" class="form-control" id="nama" value="{{ $peserta->usermahasiswaTA->name }}" readonly>
                                            </div>     
                                            
                                                <div class="form-group">
                                                    <label for="nama">Kategori TA</label>
                                                    <input type="text" class="form-control" id="nama" value="{{ $peserta->kategoriTA->nama_kategori }}" readonly>
                                                </div> 
                                                
                                        </div>

                                        <!-- Form edit data -->
                                        <form id="form-edit-{{ $peserta->id }}" action="{{ route('pesertata.update', $peserta->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <!-- Baris 1: Tanggal, Jam, Lokasi -->
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="tanggal">Tanggal Presentasi</label>
                                                        <input type="date" class="form-control" name="tanggal_presentasi" 
                                                            value="{{ $peserta->tanggal_presentasi ? \Carbon\Carbon::parse($peserta->tanggal_presentasi)->format('Y-m-d') : '' }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="jam_presentasi">Jam Mulai</label>
                                                    <input type="time" class="form-control" name="jam_presentasi"
                                                    value="{{ $peserta->jam_presentasi ? \Carbon\Carbon::parse($peserta->jam_presentasi)->format('H:i') : '' }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="jam_presentasi_selesai">Jam Selesai</label>
                                                    <input type="time" class="form-control" name="jam_presentasi_selesai"
                                                    value="{{ $peserta->jam_presentasi_selesai ? \Carbon\Carbon::parse($peserta->jam_presentasi_selesai)->format('H:i') : '' }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="lokasi">Lokasi</label>
                                                    <input type="text" class="form-control" name="lokasi"
                                                    value="{{ $peserta->lokasi ?? '' }}" placeholder="Lokasi Presentasi">
                                                </div>                       
                                            </div>                     

                                        </form>
                                    </div>
                                <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" form="form-edit-{{ $peserta->id }}" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal jadwal    -->

                    @endforeach
                    </tbody>
                </table>  
                  
                <div class="modal fade" id="editdosenModal" tabindex="-1" role="dialog" aria-labelledby="editdosenModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editdosenModalLabel">Edit Dosen Pembimbing</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                    <div class="form-group">
                                        <label for="namaMahasiswa">Nama Mahasiswa</label>
                                        <input type="text" class="form-control mb-4" id="namaMahasiswa" name="nama" readonly>
                                        <label for="namaMahasiswa">Daftar Dosen</label>
                                        <div id="pengujiList"></div>
                                    </div>
                                    <form action="" method="POST" id="editDosenForm">
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" class="form-control" id="pesertaId" name="id" readonly>
                                        <input type="hidden" class="form-control" id="idMahasiswa" name="id_mhs" readonly>

                                        <!-- Container untuk form dynamic -->
                                        <div id="form-container">
                                            <div class="border p-3 mb-3 rounded shadow-sm form-entry">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="dosen">Dosen</label>
                                                        <select name="dosen[]" class="form-control dosen-select">
                                                            <option value="" disabled selected>Pilih Dosen</option>
                                                            @foreach ($dosen as $dsn)
                                                                <option value="{{ $dsn->id }}">
                                                                    {{ $dsn->details->gelar_depan }} {{ $dsn->name }}, {{ $dsn->details->gelar_belakang }}
                                                                    (NIDN: {{ $dsn->details->nidn }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="statusdosen">Status Dosen</label>
                                                        <select name="statusdosen[]" class="form-control statusdosen-select">
                                                            <option value="" disabled selected>Pilih Status</option>
                                                            @foreach ($statusdosen as $stats)
                                                                <option value="{{ $stats->id }}">{{ $stats->status_dosen }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mt-3 text-end">
                                                    <button type="button" class="btn btn-warning btn-sm remove-form d-none">Hapus</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tombol untuk menambah input baru -->
                                        <div class="text-end mb-4">
                                            <button type="button" class="btn btn-success btn-sm" id="add-form">Tambah</button>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>

        <div class="card card-default">
            <div class="card-header">
            <h3 class="card-title">Dosen Pembimbing</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
                </button>
            </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                
                <table id="example2" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>No</th>                
                        <th>Nama Mahasiswa</th>
                        <th>Dosen Pembimbing</th>
                        <th>Dosen Penguji</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($mahasiswa as $mhs)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex align-items-start mb-2">
                                            <img src="{{  $mhs->details->photo ? 'data:image/jpeg;base64,' .  $mhs->details->photo : asset('images/pp.jpg') }}" 
                                                        class="img-circle elevation-2 mt-1" 
                                                        alt="Foto Penguji" 
                                                        style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                <div class="d-flex flex-column ps-3">  <!-- Padding kiri untuk beri jarak -->
                                                    <strong class="d-block mb-1">{{ $mhs->name }}</strong>
                                                    <span class="text-muted" style="font-size: 12px;">NIM : {{ $mhs->details->nim }}</span>                              
                                                </div>                                   
                                        </div>
                                    </div>
                                </td> 
                            <td>
                            @foreach($pengujiTA->where('id_mahasiswa', $mhs->id)->where('status_dosen', 1) as $pta) 
                               
                                <div class="d-flex flex-column">
                                        <div class="d-flex align-items-start mb-2">
                                            <img src="{{  $pta->userdosenTA->details->photo ? 'data:image/jpeg;base64,' .  $pta->userdosenTA->details->photo : asset('images/pp.jpg') }}" 
                                                        class="img-circle elevation-2 mt-1" 
                                                        alt="Foto Penguji" 
                                                        style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                <div class="d-flex flex-column ps-3">  <!-- Padding kiri untuk beri jarak -->
                                                    <strong class="d-block mb-1">{{ $pta->userdosenTA->name }}</strong>
                                                    <span class="text-muted" style="font-size: 12px;">NIDN : {{ $pta->userdosenTA->details->nidn }}</span>                              
                                                </div>                                   
                                        </div>
                                    </div>
                            @endforeach
     
                            </td>
                            
                            <td>
                            @foreach($pengujiTA->where('id_mahasiswa', $mhs->id)->where('status_dosen', 2) as $pta) 
                               
                               <div class="d-flex flex-column">
                                       <div class="d-flex align-items-start mb-2">
                                           <img src="{{  $pta->userdosenTA->details->photo ? 'data:image/jpeg;base64,' .  $pta->userdosenTA->details->photo : asset('images/pp.jpg') }}" 
                                                       class="img-circle elevation-2 mt-1" 
                                                       alt="Foto Penguji" 
                                                       style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                               <div class="d-flex flex-column ps-3">  <!-- Padding kiri untuk beri jarak -->
                                                   <strong class="d-block mb-1">{{ $pta->userdosenTA->name }}</strong>
                                                   <span class="text-muted" style="font-size: 12px;">NIDN : {{ $pta->userdosenTA->details->nidn }}</span>                              
                                               </div>                                   
                                       </div>
                                   </div>
                           @endforeach
                               
                            </td>
                            <td>
                                <!-- Membungkus tombol dalam div dengan kelas flex-column untuk responsivitas -->
                                <div class="btn-group-vertical">
                                    <!-- Tombol Laporan Magang -->
                                   
                                    <button class="btn btn-sm btn-success btn-edit mb-2" data-toggle="modal" data-target="#editdosenModal"  
                                        data-id="{{ $mhs->id }}"
                                        data-nama="{{ $mhs->name }}"
                                        data-id_mhs="{{ $mhs->id}}">
                                        <i class="fas fa-edit"></i> Edit Dosen
                                    </button>

                    
                                </div>
                            </td>
                        </tr>

                         <!-- Modal jadwal    -->    
                        <div class="modal fade" id="editjadwalModal-{{ $peserta->id }}" tabindex="-1" role="dialog" aria-labelledby="editjadwalModal" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">  
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Edit Data Tugas Akhir</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                    </div>

                                    <!-- Tambahkan max-height & scroll -->
                                    <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                                        <div class="mb-3">
                                            <!-- Nama Mahasiswa -->
                                            <div class="form-group">
                                                    <label for="nama">Nama Mahasiswa</label>
                                                    <input type="text" class="form-control" id="nama" value="{{ $peserta->usermahasiswaTA->name }}" readonly>
                                            </div>     
                                            
                                                <div class="form-group">
                                                    <label for="nama">Kategori TA</label>
                                                    <input type="text" class="form-control" id="nama" value="{{ $peserta->kategoriTA->nama_kategori }}" readonly>
                                                </div> 
                                                
                                        </div>

                                        <!-- Form edit data -->
                                        <form id="form-edit-{{ $peserta->id }}" action="{{ route('pesertata.update', $peserta->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <!-- Baris 1: Tanggal, Jam, Lokasi -->
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="tanggal">Tanggal Presentasi</label>
                                                        <input type="date" class="form-control" name="tanggal_presentasi" 
                                                            value="{{ $peserta->tanggal_presentasi ? \Carbon\Carbon::parse($peserta->tanggal_presentasi)->format('Y-m-d') : '' }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="jam_presentasi">Jam Mulai</label>
                                                    <input type="time" class="form-control" name="jam_presentasi"
                                                    value="{{ $peserta->jam_presentasi ? \Carbon\Carbon::parse($peserta->jam_presentasi)->format('H:i') : '' }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="jam_presentasi_selesai">Jam Selesai</label>
                                                    <input type="time" class="form-control" name="jam_presentasi_selesai"
                                                    value="{{ $peserta->jam_presentasi_selesai ? \Carbon\Carbon::parse($peserta->jam_presentasi_selesai)->format('H:i') : '' }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="lokasi">Lokasi</label>
                                                    <input type="text" class="form-control" name="lokasi"
                                                    value="{{ $peserta->lokasi ?? '' }}" placeholder="Lokasi Presentasi">
                                                </div>                       
                                            </div>                     

                                        </form>
                                    </div>
                                <!-- Modal Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" form="form-edit-{{ $peserta->id }}" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Modal jadwal    -->

                    @endforeach
                    </tbody>
                </table>  
                  
                <div class="modal fade" id="editdosenModal" tabindex="-1" role="dialog" aria-labelledby="editdosenModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editdosenModalLabel">Edit Dosen Pembimbing</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="namaMahasiswa">Nama Mahasiswa</label>
                                    <input type="text" class="form-control mb-4" id="namaMahasiswa" name="nama" readonly>
                                    <label for="namaMahasiswa">Daftar Dosen</label>
                                    <div id="pengujiList"></div>
                                </div>
                                    <form action="" method="POST" id="editDosenForm">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" class="form-control" id="pesertaId" name="id" readonly>
                                        <input type="hidden" class="form-control" id="idMahasiswa" name="id_mhs" readonly>
                                        <!-- Container untuk form dynamic -->
                                        <div id="form-container">
                                            <div class="border p-3 mb-3 rounded shadow-sm form-entry">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <label for="dosen">Dosen</label>
                                                        <select name="dosen[]" class="form-control dosen-select">
                                                            <option value="" disabled selected>Pilih Dosen</option>
                                                            @foreach ($dosen as $dsn)
                                                                <option value="{{ $dsn->id }}">
                                                                    {{ $dsn->details->gelar_depan }} {{ $dsn->name }}, {{ $dsn->details->gelar_belakang }}
                                                                    (NIDN: {{ $dsn->details->nidn }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="statusdosen">Status Dosen</label>
                                                        <select name="statusdosen[]" class="form-control statusdosen-select">
                                                            <option value="" disabled selected>Pilih Status</option>
                                                            @foreach ($statusdosen as $stats)
                                                                <option value="{{ $stats->id }}">{{ $stats->status_dosen }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mt-3 text-end">
                                                    <button type="button" class="btn btn-warning btn-sm remove-form d-none">Hapus</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Tombol untuk menambah input baru -->
                                        <div class="text-end mb-4">
                                            <button type="button" class="btn btn-success btn-sm" id="add-form">Tambah</button>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
                
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
<!-- DataTables  & Plugins -->
<script src="{{asset('dtable/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('dtable/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('dtable/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('dtable/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
<script src="{{asset('dtable/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('dtable/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{asset('dtable/jszip/jszip.min.js')}}"></script>
<script src="{{asset('dtable/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{asset('dtable/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{asset('dtable/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{asset('dtable/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{asset('dtable/datatables-buttons/js/buttons.colVis.min.js')}}"></script>


<script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 5,
        "buttons": ["excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      $("#example2").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 5,
        "buttons": ["excel", "pdf", "print"]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      $('#example3').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
      });
    });
  </script>

<script>
    var pengujiTA = JSON.parse('{!! json_encode($pengujiTA) !!}');
</script>

<script>
   $(document).ready(function() {
    $('.btn-edit').click(function() {
        let id = $(this).data('id');
        let nama = $(this).data('nama');
        let id_mhs = $(this).data('id_mhs');

        console.log("ID Mahasiswa yang dipilih:", id_mhs); // ✅ Debug ID Mahasiswa

        $('#pesertaId').val(id);
        $('#namaMahasiswa').val(nama);
        $('#idMahasiswa').val(id_mhs);

        $('#editDosenForm').attr('action', '/manage/dosenta/' + id);

        let filteredPenguji = pengujiTA.filter(p => p.id_mahasiswa == id_mhs);
        console.log("Filtered Penguji untuk id_mhs:", filteredPenguji); // ✅ Debug hasil filter

        let pengujiList = $('#pengujiList');
        console.log("Elemen pengujiList ditemukan:", pengujiList.length); // ✅ Debug elemen ada/tidak
        pengujiList.empty(); // Kosongkan sebelum menambahkan data baru

        if (filteredPenguji.length > 0) {
            filteredPenguji.forEach(p => {
                console.log("Memproses penguji:", p); // ✅ Debug setiap penguji yang diproses
                
                // 🛑 Pastikan `userdosenTA` dan `details` ada sebelum mengakses propertinya
                let userDosen = p.userdosen_t_a ?? {}; // Gunakan userdosen_t_a sesuai hasil dd()
                let details = userDosen.details ?? {}; // Pastikan details juga ada

                let imgSrc = details.photo 
                ? 'data:image/jpeg;base64,' + details.photo 
                : "{{ asset('images/pp.jpg') }}";


                let gelarDepan = details.gelar_depan || "";
                let gelarBelakang = details.gelar_belakang || "";
                let namaDosen = userDosen.name 
                    ? `${gelarDepan} ${userDosen.name}, ${gelarBelakang}` 
                    : 'Dosen tidak ditemukan';

                let nidn = details.nidn || "-";
                let statusDosen = p.statusdosen_t_a.status_dosen || "Status Tidak Diketahui";

                let html = `
                    <div class="d-flex align-items-center justify-content-between pb-3 pt-3 border-bottom">
                        <div class="d-flex align-items-center" style="width: 40%;">
                            <div style="width: 60px; height: 60px;">
                                <img src="${imgSrc}" class="rounded-circle border" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div style="margin-left: 20px;">
                                <h6 class="mb-0 fw-bold">${namaDosen}</h6>
                                <p class="text-muted mb-0" style="font-size: 14px;">NIDN: ${nidn}</p>         
                            </div>
                        </div>
                        <div class="text-center" style="width: 20%;">
                            <p class="mb-0 text-secondary fw-semibold">${statusDosen}</p>
                        </div>
                        <div class="text-end" style="width: 10%;">
                            <form action="/manage/pengujita/${p.id}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                `;

                pengujiList.append(html);
            });
        } else {
            pengujiList.append(`<p class="text-muted">Tidak ada penguji untuk mahasiswa ini.</p>`);
        }
    });
});

</script>

<script>
    $(document).ready(function() {
        // Saat tombol Tambah diklik
        $('#add-form').click(function() {
            let formEntry = $('.form-entry').first().clone(); // Clone elemen pertama
            formEntry.find("select").val(""); // Kosongkan pilihan select
            formEntry.find(".remove-form").removeClass("d-none"); // Tampilkan tombol hapus
            $('#form-container').append(formEntry); // Tambahkan elemen baru ke dalam container
        });

        // Saat tombol Hapus diklik
        $(document).on('click', '.remove-form', function() {
            $(this).closest('.form-entry').remove(); // Hapus elemen form terkait
        });
    });
</script>




@endsection


