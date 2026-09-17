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
          <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahUser">
            <i class="fas fa-plus"></i> Tambah User
          </button>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <p class="text-muted">
            Untuk mengisi data awal Mahasiswa/Perusahaan/Peserta Magang lewat Excel di awal
            periode, gunakan menu <strong>Konfigurasi Magang &rarr; Import Peserta Magang</strong>.
            Halaman ini khusus untuk menambah/mengelola user satu per satu.
          </p>
          <div class="modal fade" id="modalTambahUser" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <form action="{{ route('storeusermanual') }}" method="POST">
                  @csrf
                  <div class="modal-header">
                    <h5 class="modal-title">Tambah User Manual</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                  </div>

                  <div class="modal-body">
                    <div class="form-group">
                      <label>Role <span class="text-danger">*</span></label>
                      <select name="role" id="selectRole" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="Admin">Admin</option>
                        <option value="Dosen">Dosen</option>
                        <option value="Mahasiswa">Mahasiswa</option>
                      </select>
                    </div>

                    <div class="form-group">
                      <label>Nama Lengkap <span class="text-danger">*</span></label>
                      <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label>Email <span class="text-danger">*</span></label>
                      <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label>Password <span class="text-danger">*</span></label>
                      <input type="password" name="password" class="form-control" required>
                    </div>

                    <div id="fieldDosen" style="display:none;">
                      <div class="form-group">
                        <label>NIK <span class="text-danger">*</span></label>
                        <input type="text" name="nik" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>NIDN</label>
                        <input type="text" name="nidn" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Gelar Depan</label>
                        <input type="text" name="gelar_depan" class="form-control">
                      </div>
                      <div class="form-group">
                        <label>Gelar Belakang</label>
                        <input type="text" name="gelar_belakang" class="form-control">
                      </div>
                    </div>

                    <div id="fieldMahasiswa" style="display:none;">
                      <div class="form-group">
                        <label>NIM <span class="text-danger">*</span></label>
                        <input type="text" name="nim" class="form-control">
                      </div>
                    </div>
                  </div>
                 
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
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
$(document).ready(function () {
    $('#selectRole').on('change', function () {
        $('#fieldDosen, #fieldMahasiswa').hide();
        $('#fieldDosen input, #fieldMahasiswa input').val(''); // reset value saat ganti role

        if ($(this).val() === 'Dosen') {
            $('#fieldDosen').show();
        } else if ($(this).val() === 'Mahasiswa') {
            $('#fieldMahasiswa').show();
        }
    });

    // reset form & pilihan role saat modal ditutup
    $('#modalTambahUser').on('hidden.bs.modal', function () {
        $(this).find('form')[0].reset();
        $('#fieldDosen, #fieldMahasiswa').hide();
    });
});
</script>

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
