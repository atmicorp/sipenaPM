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
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Jadwal Presentasi : {{$kategoriTa->nama_kategori}}</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
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
                            <th>Tanggal Presentasi</th>
                            <th>Jam</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                       
                    @foreach($jadwalArray as $jta)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{ $jta['nama_kelompok_ta'] ?? '-' }}</td>
                            <td>
                                {{ 
                                    $jta['tanggal_presentasi'] 
                                    ? \Carbon\Carbon::parse($jta['tanggal_presentasi'])->translatedFormat('l, d F Y') 
                                    : '-' 
                                }}
                            </td>
                            <td>
                                {{ $jta['jam_presentasi'] ?? '-' }} - {{ $jta['jam_presentasi_selesai'] ?? '-' }} WIB
                            </td>
                            <td>{{ $jta['lokasi'] ?? '-' }}</td>
                            <td>@php
                                            $status = (int) ($jta['status'] ?? -1);
                                            $statusLabel = match($status) {
                                                0 => 'Ditutup',
                                                1 => 'Dibuka',
                                                2 => 'Disetujui',
                                                3 => 'Ditolak',
                                                4 => 'Disetujui Final',
                                                default => 'Status Tidak Diketahui',
                                            };

                                            $textClass = match($status) {
                                                0 => 'text-secondary',
                                                1 => 'text-primary',
                                                2 => 'text-success',
                                                3 => 'text-danger',
                                                4 => 'text-success',
                                                default => 'text-dark',
                                            };
                                        @endphp
                                         <label for="namakel_ta-peserta" class="form-label {{ $textClass }}">
                                             {{ $statusLabel }}
                                        </label></td>
                                        <td>
                                            <div class="button-container">
                                                <button class="btn btn-sm btn-primary btn-edit-ta p-1 px-2 mt-2 mb-2" data-toggle="modal" data-target="#editta{{$jta['id']}}">
                                                    <i class="fas fa-file-alt"></i> Edit Jadwal TA
                                                </button>
                                                
                                                <form action="{{ route('berita_acara') }}" method="GET">
                                                    <!-- Hidden Inputs -->
                                                    <input type="hidden" name="id_kelompok_ta" value="{{ $jta['id_kelompok_ta'] ?? '' }}">
                                                    <input type="hidden" name="id_kategori_ta" value="{{ $jta['id_kategori_ta'] ?? '' }}">

                                                    <!-- Button to submit the form -->
                                                    <button type="submit" class="btn btn-sm btn-info">
                                                        <i class="fas fa-file-pdf"></i> Berita Acara
                                                    </button>
                                                </form>
                                            </div>       
                                        </td>
                        </tr>
                         <!-- modal jadwal ta -->
                         <div class="modal fade" id="editta{{$jta['id']}}" tabindex="-1" role="dialog" aria-labelledby="edittaLabel" aria-hidden="true">
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
                                        <label for="namakel_ta-peserta" class="form-label">Jadwal Presentasi : {{$kategoriTa->nama_kategori}}, {{ $jta['nama_kelompok_ta'] ?? '-' }}</label>
                                        <br>
                                        @php
                                            $status = (int) ($jta['status'] ?? -1);
                                            $statusLabel = match($status) {
                                                0 => 'Ditutup',
                                                1 => 'Dibuka',
                                                2 => 'Disetujui',
                                                3 => 'Ditolak',
                                                4 => 'Disetujui Final',
                                                default => 'Status Tidak Diketahui',
                                            };

                                            $textClass = match($status) {
                                                0 => 'text-secondary',
                                                1 => 'text-primary',
                                                2 => 'text-success',
                                                3 => 'text-danger',
                                                4 => 'text-success',
                                                default => 'text-dark',
                                            };
                                        @endphp
                                        <label for="namakel_ta-peserta"> Status :</label> <label for="namakel_ta-peserta" class="form-label {{ $textClass }}">
                                             {{ $statusLabel }}
                                        </label>
                                    </div>
                                    
                                    </div>
                                </div>
                                <form action="{{ route('updatejadwalta', ['id' => $jta['id']]) }}" method="POST" id="edittaForm">
                                        @csrf
                                        @method('PUT')
                                      
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label for="tanggal">Tanggal Presentasi</label>
                                                        <input type="date" class="form-control" name="tanggal_presentasi" 
                                                            value="
                                                            {{ 
                                                                $jta['tanggal_presentasi'] 
                                                                ? \Carbon\Carbon::parse($jta['tanggal_presentasi'])->translatedFormat('l, d F Y') 
                                                                : '-' 
                                                            }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="jam_presentasi">Jam Mulai</label>
                                                    <input type="time" class="form-control" name="jam_presentasi"
                                                    value="{{ $jta['jam_presentasi'] ?? '-' }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="jam_presentasi_selesai">Jam Selesai</label>
                                                    <input type="time" class="form-control" name="jam_presentasi_selesai"
                                                    value="{{ $jta['jam_presentasi_selesai'] ?? '-' }}">
                                                </div>

                                                <div class="col-md-3 mb-3">
                                                    <label for="lokasi">Lokasi</label>
                                                    <input type="text" class="form-control" name="lokasi"
                                                    value="{{ $jta['lokasi'] ?? '-' }}" placeholder="Lokasi Presentasi">
                                                </div> 
                                                <div class="col-md-3 mb-3">
                                                <button type="button" class="btn btn-secondary" onclick="clearFormInputs(this)">Clear</button>
                                                  <button type="submit" class="btn btn-primary">Simpan Perubahan</button>     
                                                </div>                 
                                            </div>  
  
                                    </form>
                            </div>
                                </div>
                            </div>
                        </div> 
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
    function clearFormInputs(button) {
        const modal = button.closest('.modal'); // ambil modal terdekat
        const inputs = modal.querySelectorAll('input');

        inputs.forEach(input => {
            if (input.type !== 'hidden' && input.type !== 'submit' && input.type !== 'button') {
                input.value = '';
            }
        });
    }
</script>

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


