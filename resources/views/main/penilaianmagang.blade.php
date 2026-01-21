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
      <div class="row">
        <div class="col-md-3">
          <!-- Profile Image -->
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <div class="text-center">    
                <div class="text-center position-relative">
                  @if($user->details->photo)
                      <img class="profile-user-img img-fluid img-circle"
                           src="data:image/jpeg;base64,{{ $user->details->photo }}"
                           alt="User profile picture">
                  @else
                      <img class="profile-user-img img-fluid img-circle"
                           src="{{ asset('images/pp.jpg') }}"
                           alt="Default profile picture">
                  @endif
              </div>
              </div>
              <h3 class="profile-username text-center">{{ $user->details->gelar_depan }} {{ $user->name }}@if($user->details->gelar_belakang), {{ $user->details->gelar_belakang }}@endif</h3>
              <hr>
              <div class="text-center">
                <strong>Jabatan :</strong>
                <br>
                {{ Auth::user()->details->jabatan }}
            </div>
            </div>    
          </div>      
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="card">
            <div class="card-header">
                <h3 class="card-title">Penilaian Praktik Kerja Mahasiswa</h3>   
            </div>
                <div class="card-body ">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                    
                        <tr>
                            <th style="width: 1%">
                                No
                            </th>
                            <th style="width: 30%">
                                Mahasiswa
                            </th>
                            <th style="width: 20%">
                                Status Anda
                            </th>
                            <th style="width: 20%">
                               View Laporan
                            </th>
                            <th style="width: 100%" class="text-center">
                                Penilaian
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($pesertamagang as $peserta)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                            <div class="user-block">
                              <img src="{{  $peserta->usermahasiswa->details->photo ? 'data:image/jpeg;base64,' .  $peserta->usermahasiswa->details->photo : asset('images/pp.jpg') }}" class="img-circle elevation-2 mb-2" alt="Default User Image" style="width: 40px; height: 40px;">
                                  <span class="username"><strong>{{ $peserta->usermahasiswa->name }}</strong> </span>
                                  <span class="description">NIM : {{ $peserta->usermahasiswa->details->nim }}</span>
                            </div>
                            </td>
                            <td><strong>{{ $peserta->status->status_dosen }}</strong></td>
                            <td class="project-state text-center">
                             

                               <a href="{{ asset('uploads/laporan/' . $peserta->usermahasiswa->details->nim . '.pdf') }}" 
                                class="btn btn-sm btn-info mb-2" 
                                target="_blank">
                                  <i class="fa fa-file-alt"></i> Laporan Magang
                              </a>
                            </td> 
                            <td class="project-state text-center">
                                @if($datapenilaian->contains('id_data_penguji_magangs', $peserta->id))
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#hasilPenilaianModal{{ $peserta->id_mahasiswa }}">
                                    <i class="fa fa-clipboard-check"></i> Hasil Penilaian
                                </button>                     
                                  @else
                                      <a href="{{ route('formpenilaianmagang', ['id' => $peserta->id]) }}" class="btn btn-info">
                                          <i class="fa fa-clipboard-list"></i> Penilaian
                                      </a>
                                  @endif
                            </td> 
                        </tr>  
                      @endforeach    
              </tbody>
                    </table>

                    @foreach($pesertamagang as $peserta)
                        <div class="modal fade" id="hasilPenilaianModal{{ $peserta->id_mahasiswa }}" tabindex="-1" aria-labelledby="hasilPenilaianModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="hasilPenilaianModalLabel">Hasil Penilaian - {{ $peserta->usermahasiswa->name }}</h5>
                                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Menampilkan Hasil Penilaian untuk Peserta -->
                                         <!-- Mengecek apakah ada data penilaian untuk peserta tertentu berdasarkan id_mahasiswa. -->
                                        @if(isset($datanilai[$peserta->id_mahasiswa]))
                                            @foreach($datanilai[$peserta->id_mahasiswa] as $nilai)
                                              <div class="mb-3">
                                                  <strong>{{ $nilai['aspek_penilaian'] }} : </strong> 
                                                  <span>{!! $nilai['nilai'] !!}</span>
                                              </div>
                                            @endforeach
                                        @else
                                                <p>Tidak ada penilaian untuk peserta ini.</p>
                                         @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>    
            </div> 



            
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
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
<!-- modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>


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
