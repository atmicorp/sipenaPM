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

  @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                {{ $error }}
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
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          

        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
              <label for="file">Upload File Excel</label>
              <div class="custom-file">
                <input type="file" name="file" class="custom-file-input" id="file" accept=".xlsx, .xls, .csv" required>
                <label class="custom-file-label" for="file">Choose file</label>
              </div>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
          </form>
          
          <hr>

          <!-- Tabel Data User -->
          <table id="example1" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>No</th>                
              <th>Nama</th>
              <th>Role</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($datauser as $user)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>
                    <div class="user-block">
                      <img src="{{  $user->details->photo ? 'data:image/jpeg;base64,' .  $user->details->photo : asset('images/pp.jpg') }}" class="img-circle elevation-2 mb-2" alt="Default User Image" style="width: 40px; height: 40px;">
                          <span class="username"><strong>{{ $user->name }}</strong> </span>
                          @foreach($user->roles as $role)
                              @if($role->name == 'Dosen')
                                  <span class="description">NIDN: {{ $user->details->nidn }}</span>
                              @elseif($role->name == 'Mahasiswa')
                                  <span class="description">NIM: {{ $user->details->nim }}</span>
                              @endif
                          @endforeach
       
                    </div>
                </td>
                <td><strong> @foreach($user->roles as $role)
                      <span>{{ $role->name }}</span>
                  @endforeach</strong></td>
                <td>
                   

                <a href="{{ route('edituser', ['id' => $user->id]) }}" class="btn btn-sm btn-success btn-setup-data">
                      <i class="fas fa-tools"></i> Set Up Data
                </a>
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

<script>
$(document).ready(function () {
    // Update label when a file is selected
    $('.custom-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});
</script>

@endsection
