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
          <h3 class="card-title">Penempatan (Magang)</h3>

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
                     <th>NIM</th>
                    <th>Perusahaan</th>
                    <th>Tanggal Presentasi</th>
                    <th>Jam Presentasi</th>
                    <th>Lokasi Presentasi</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                
                  @foreach($pesertamagang as $peserta)
                    <div class="modal fade" id="editModal-{{ $peserta->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                <!-- Form edit data -->
                                <form id="form-edit-{{ $peserta->id }}" action="{{ route('pesertaupdate', $peserta->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="nama">Nama Mahasiswa</label>
                                        <input type="text" class="form-control" id="nama" value="{{ $peserta->usermahasiswa->name }}" placeholder="Nama" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="perusahaan">Perusahaan</label>
                                        <select class="form-control" id="perusahaan" name="perusahaan_id">
                                            @foreach($perusahaanmagang as $perusahaan)
                                                <option value="{{ $perusahaan->id }}" {{ $peserta->perusahaanmagang->id == $perusahaan->id ? 'selected' : '' }}>{{ $perusahaan->nama }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                      <label for="tanggal">Tanggal Presentasi</label>
                                      <input type="date" class="form-control" name="tanggal_presentasi" placeholder="Pilih Tanggal" required value="{{ \Carbon\Carbon::parse($peserta->tanggal_presentasi)->format('Y-m-d') }}">
                                    </div> 
                                    <div class="form-group">
                                        <label for="jam_presentasi">Jam Presentasi</label>
                                        <input type="time" class="form-control" name="jam_presentasi" placeholder="Pilih Jam" required value="{{ \Carbon\Carbon::parse($peserta->jam_presentasi)->format('H:i') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="jam_presentasi">Jam Selesai Presentasi</label>
                                        <input type="time" class="form-control" name="jam_presentasi_selesai" placeholder="Pilih Jam" required value="{{ \Carbon\Carbon::parse($peserta->jam_presentasi_selesai)->format('H:i') }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="jam_presentasi">Lokasi Presentasi</label>
                                        <input type="text" class="form-control" id="lokasi" name="lokasi" value="{{ $peserta->lokasi }}" placeholder="Lokasi Presentasi" >
                                    </div>
                                     <div class="form-group">
                                        <label for="sk">SK</label>
                                        <input type="text" class="form-control" id="sk" name="sk" value="{{ $peserta->sk }}" placeholder="Nomor SK" >
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" form="form-edit-{{ $peserta->id }}" class="btn btn-primary">Simpan</button>
                            </div>
                            </div>
                        </div>
                    </div>

                    <!-- ---------------------->
                  


                    <tr>
                      <td>{{$loop->iteration}}</td>
                      <td>{{ $peserta->usermahasiswa->name }}</td>
                      <td>{{ $peserta->usermahasiswa->details->nim }}</td>
                      <td><strong>{{ $peserta->perusahaanmagang->nama }}</strong></td>
                      <td>{{ \Carbon\Carbon::parse($peserta->tanggal_presentasi)->translatedFormat('d F Y') }}</td>
                      <td>{{ \Carbon\Carbon::parse($peserta->jam_presentasi)->format('H:i') }} - {{ \Carbon\Carbon::parse($peserta->jam_presentasi_selesai)->format('H:i') }} WIB</td>
                      <td>{{$peserta->lokasi}}</td>
                      <td>
                          <!-- Membungkus tombol dalam div dengan kelas flex-column untuk responsivitas -->
                          <div class="btn-group-vertical">
                              <!-- Tombol Laporan Magang -->
                              

                              <a href="{{ asset('uploads/laporan/' . $peserta->usermahasiswa->details->nim . '.pdf') }}" 
                                class="btn btn-sm btn-info mb-2" 
                                target="_blank">
                                  <i class="fa fa-file-alt"></i> Laporan Magang
                              </a>

                              <!-- Tombol Edit -->
                              <button class="btn btn-sm btn-primary btn-edit mb-2" data-toggle="modal" data-target="#editModal-{{ $peserta->id }}">
                                  <i class="fas fa-edit"></i> Edit Presentasi
                              </button>

                              <!-- Tombol Set Up Data -->
                              <a href="{{ route('setupdatamagang', $peserta->id) }}" class="btn btn-sm btn-success btn-setup-data mb-2">
                                  <i class="fas fa-tools"></i> Set Up Data
                              </a>
                              <div class="button-container">
                     
                                  
                                  <form action="{{ route('berita_acara_magang') }}" method="GET" target="_blank">
                                      <!-- Button to submit the form -->
                                      <!-- Hidden Inputs -->
                                       <input type="hidden" name="id_mahasiswa" value="{{ $peserta->usermahasiswa->id}}">
                                       <input type="hidden" name="name" value="{{ $peserta->usermahasiswa->name}}">
                                       <input type="hidden" name="nim" value="{{ $peserta->usermahasiswa->details->nim}}">
                                       <input type="hidden" name="lokasi" value="{{ $peserta->lokasi}}">
                                       <input type="hidden" name="tanggal" value="{{ $peserta->tanggal_presentasi}}">
                                       <input type="hidden" name="sk" value="{{ $peserta->sk}}">
                                       <input type="hidden" name="in" value="{{ $peserta->jam_presentasi}}">
                                       <input type="hidden" name="out" value="{{ $peserta->jam_presentasi_selesai}}">
                                       <input type="hidden" name="judul" value="{{ $peserta->judul_laporan}}">
                                       <input type="hidden" name="tahun" value="{{ $peserta->tahun}}">
                                        
                                       <button type="submit" class="btn btn-sm btn-info" >
                                            <i class="fas fa-file-pdf"></i> Berita Acara
                                        </button>
                                  </form>
                                            </div>
                
                                               
                                     
                          </div>
                      </td>

                    </tr>
                  @endforeach
                  </tbody>
                </table>

                
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
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $('#example2').DataTable({
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
@endsection
