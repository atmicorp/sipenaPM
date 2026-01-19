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
                  @if($datapenguji->usermahasiswa->details->photo)
                      <img class="profile-user-img img-fluid img-circle"
                           src="data:image/jpeg;base64,{{ $datapenguji->usermahasiswa->details->photo }}"
                           alt="User profile picture">
                  @else
                      <img class="profile-user-img img-fluid img-circle"
                           src="{{ asset('images/pp.jpg') }}"
                           alt="Default profile picture">
                  @endif
              </div>
              </div>
              <h3 class="profile-username text-center">
                    <strong>{{$datapenguji->usermahasiswa->name }}</strong>
                </h3>
                <span class="text-center" style="font-size: normal; display: block;">{{$datapenguji->usermahasiswa->details->nim}}</span>
              <hr>
              <div class="text-center">
               
                <strong>{{$datapenguji->usermahasiswa->pesertamagang->perusahaanmagang->nama }}</strong>
            </div>
            </div>    
          </div>   
          
          <div class="card card-primary card-outline">
            <div class="card-body box-profile">
              <div class="text-center">     
              </div>
              <div class="text-center">
                <strong>Range Penilaian</strong>
              </div>  
              <hr>
              <div class="text-left">
                <p><strong>Unggul:</strong> 81 - 90</p>
                <p><strong>Baik:</strong> 71 - 80</p>
                <p><strong>Cukup:</strong> 61 - 70</p>
                <p><strong>Kurang:</strong> 50 - 60</p>
              </div>
            </div>    
          </div> 
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="card">
            <div class="card-header">
                <h3 class="card-title"> 
                    <strong> {{ $datapenguji->userdosen->details->gelar_depan }} {{ $datapenguji->userdosen->name }}@if($datapenguji->userdosen->details->gelar_belakang), {{ $datapenguji->userdosen->details->gelar_belakang }}@endif
                    <span style="text-transform: uppercase;">
                       - {{$datapenguji->status->status_dosen}}
                    </span></strong>   
                </h3>   
            </div>
                <div class="card-body ">
                <form action="{{route('penilaianmagangstore')}}" method="POST">
                    @foreach ($aspekpenilaian as $item)
                    @csrf
                    <div class="mb-3">
                    <label for="name" class="form-label">
                          <strong>{{$item->aspek_penilaian}}</strong>, <span class="text-muted">{{$item->porsi_penilaian}}%</span>
                      </label>
                      <div class="text-secondary" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                          {!! $item->deskripsi_penilaian !!}  
                      </div>
                        <input type="hidden" class="form-control" id="data_penguji_magang" name="data_penguji_magang" value="{{$datapenguji->id}}" required>
                        <input type="hidden" class="form-control" id="id_dosen" name="id_dosen" value="{{$datapenguji->id_dosen}}" required>
                        <input type="hidden" class="form-control" id="id_mahasiswa" name="id_mahasiswa" value="{{$datapenguji->id_mahasiswa}}" required>
                        <!-- Cek jika tipe data long_text, tampilkan textarea, jika tidak tampilkan input biasa -->
                        @if($item->tipedata == 'Deskripsi')
                        <textarea name="{{$item->id}}" id="compose-textarea-{{$item->id}}" class="form-control" style="height: 300px" required></textarea>
                        @else
                            <input type="number" class="form-control" id="name" name="{{$item->id}}" placeholder="{{$item->aspek_penilaian}}" required>
                        @endif
                        
                    </div>
                    @endforeach
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Submit
                </button>
                </form>                   
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

<script>
 $(document).ready(function() {
    // Inisialisasi Summernote pada semua textarea dengan ID yang dimulai dengan "compose-textarea"
    @foreach ($aspekpenilaian as $item)
        $('#compose-textarea-{{$item->id}}').summernote({
            height: 300, // Set the height of the textarea
            toolbar: [
                ['style', ['ul', 'ol']] // Optional: Customize toolbar if needed
            ]
        });
    @endforeach
});
</script>




@endsection
