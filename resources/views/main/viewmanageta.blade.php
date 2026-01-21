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
      
       

<!-- ------------------------------------------------------------------------------------------------------------------------ -->

        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Kelompok TA</h3>

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
                            <th>Kelompok TA</th>
                            <th>Judul TA</th>
                            <th>Nama Peserta</th>
                            <th>NIM</th>
                            <th>SK</th>
                            <th>Periode</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesertata as $kta)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{ $kta->kelompokTA?->nama_kelompok ?? 'Belum ada kelompok' }}</td>
                            <td>{!! $kta->kelompokTA?->judul_ta ?? '-' !!}</td>
                            <td>{{ $kta->usermahasiswaTA->name }}</td>
                            <td>{{ $kta->usermahasiswaTA->details->nim }}</td>
                            <td>{{ $kta->kelompokTA?->sk ?? '-' }}</td>
                            <td>{{ $kta->kelompokTA?->tahun_perkuliahan ?? '-' }}</td>
                            <td>
                              <!-- Tombol Edit TA -->
                                @if($kta->kelompokTA)
                                    <button class="btn btn-sm btn-primary btn-edit-ta p-1 px-2 mt-2 mb-2" 
                                            data-toggle="modal" data-target="#editta{{ $kta->kelompokTA->id }}">
                                        <i class="fas fa-file-alt"></i> Edit TA
                                    </button>
                                @endif

                                <!-- Tombol Edit Peserta -->
                                @if($kta->kelompokTA)
                                    <a href="{{ route('vpesertata.update', $kta->kelompokTA->id) }}" 
                                      class="btn btn-sm btn-success p-1 px-2 mt-2 mb-2">
                                        <i class="fas fa-user-graduate"></i> Edit Peserta
                                    </a>
                                @endif

                              <br> <!-- Baris baru untuk tambahan jarak -->
                          </td>
                        </tr>

                        <!-- modal ta -->
                         @if($kta->kelompokTA)
                        <div class="modal fade" id="editta{{ $kta->kelompokTA->id }}" tabindex="-1" role="dialog" aria-labelledby="edittaLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="edittaLabel">Edit Data TA</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                <div class="form-group">
                                    <!-- Nama Kelompok TA & Judul TA dalam satu baris -->
                                    <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="namakel_ta-peserta" class="form-label">Nama Kelompok TA</label>
                                        <input type="text" class="form-control" value="{{ $kta->kelompokTA->nama_kelompok }}" ="namakel_ta-peserta" readonly>
                                    </div>
                                    
                                    </div>
                                </div>
                                <form action="{{ route('kta.update', ['id' => $kta->kelompokTA->id]) }}" method="POST" id="edittaForm">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" class="form-control" id="id_kel_ta" name="id_kel_ta" value="{{ $kta->kelompokTA->id }}" readonly>
                                        <label for="namakel_ta-peserta" class="form-label">SK</label>
                                        <input type="text" class="form-control" id="sk" name="sk" value="{{ $kta->kelompokTA->sk }}" required>
                                        <label for="namakel_ta-peserta " class="form-label mt-3">Judul TA</label>
                                        <textarea name="judulta" id="compose-textarea-{{ $kta->kelompokTA->id }}" 
                                            class="form-control compose-textarea" style="height: 300px">
                                            {{ $kta->kelompokTA->judul_ta }}
                                        </textarea>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </form>
                            </div>
                                </div>
                            </div>
                        </div> 
                        @endif
                        @endforeach
                    </tbody>
                </table>  
                      
            </div>
        </div>
    </div>

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
 $(document).ready(function () {
    setTimeout(function () {
        $('.compose-textarea').each(function () {
            let textareaId = $(this).attr('id'); // Ambil ID unik

            console.log("Mengaktifkan Summernote untuk:", textareaId);

            $('#' + textareaId).summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                ],
                height: 150
            });
        });
    }, 500); // Beri jeda 500ms agar semua elemen termuat dulu
});
</script>



@endsection


