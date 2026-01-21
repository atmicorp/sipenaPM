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
          <h3 class="card-title">Set Up Data Praktik Kerja (Magang)</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <div class="row">
            <!-- /.col -->
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                    <div class="text-center">    
                        <div class="text-center position-relative">
                        @if($pesertamagang->usermahasiswa->details->photo)
                            <img class="profile-user-img img-fluid img-circle"
                                src="data:image/jpeg;base64,{{ $pesertamagang->usermahasiswa->details->photo }}"
                                alt="User profile picture">
                        @else
                            <img class="profile-user-img img-fluid img-circle"
                                src="{{ asset('images/pp.jpg') }}"
                                alt="Default profile picture">
                        @endif
                    </div>
                    </div>
                    <h3 class="profile-username text-center">{{ $pesertamagang->usermahasiswa->name }}
                        </h3>
                    <hr>
                    <div class="text-center">
                        <strong>NIM : {{ $pesertamagang->usermahasiswa->details->nim }}</strong>  
                    </div>
                    <hr>
                    <div class="text-center">
                        <strong> {{ $pesertamagang->perusahaanmagang->nama }}</strong>  
                    </div>
                    </div>    
                </div>      
            </div>
            <div class="col-md-9">
              <table class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th>Dosen</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                  <tbody>
                  @foreach ( $pengujimagang as $item)
                  <tr>
                      <td>
                        <div class="user-block">
                        <img src="{{ $item->userdosen->details->photo ? 'data:image/jpeg;base64,' . $item->userdosen->details->photo : asset('images/pp.jpg') }}" class="img-circle elevation-2 mb-2" alt="Default User Image" style="width: 40px; height: 40px;">
                            <span class="username">
                            {{$item->userdosen->details->gelar_depan }} {{ $item->userdosen->name }}@if($item->userdosen->details->gelar_belakang), {{ $item->userdosen->details->gelar_belakang }}@endif
                            </span>
                            <span class="description">NIDN : {{$item->userdosen->details->nidn }}</span>
                        </div>
                      </td>
                      <td>
                        <span class="username"><strong> {{$item->status->status_dosen}}</strong></span>  
                        </td>  
                      <td>
                        

                      <a href="{{ route('deletedatamagang', ['id' => $item->id]) }}" class="btn btn-sm btn-danger">
                          <i class="fas fa-trash"></i> Delete
                      </a>
                      </td>            
                  </tr>
                  @endforeach
              </table>


              <form method="POST" action="{{route('storedatapembimbing')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                  
                  <div class="border p-3 rounded mb-3">
                    
                    <table class="table table-striped" id="positionsTable">
                      <thead>
                        <tr>
                          <th>Dosen*</th>
                          <th>Status*</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody id="positionsBody">
                    
                        
                        <tr>
                          <td>
                            <select name="dosen[]" class="form-control" required>
                              <option value="" disabled selected>Dosen</option>
                              @foreach ($dosen as $dsn)
                              <option value="{{ $dsn->id }}">
                              <span>(NIDN : {{ $dsn->details->nidn }})</span> {{$dsn->details->gelar_depan}} {{ $dsn->name }}, {{$dsn->details->gelar_belakang}}
                              </option>
                              @endforeach
                            </select>
                          </td>
                          <td>
                            <select name="statusdosen[]" class="form-control" required>
                              <option value="" disabled selected>Status</option>
                              @foreach ($statusdosen as $stats)
                              <option value="{{ $stats->id }}">
                                {{ $stats->status_dosen }}
                              </option>
                              @endforeach
                            </select>
                            <input type="hidden" class="form-control" id="exampleInput" name="mahasiswa" value="{{ $pesertamagang->usermahasiswa->id }}">
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

                    </div>
                  </div>             
            
                </div>
                <div class="form-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-floppy-o"></i> Simpan Data
                </button>
                </div>
              </form>

              


              
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
  $(function() {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', {
      'placeholder': 'dd/mm/yyyy'
    })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', {
      'placeholder': 'mm/dd/yyyy'
    })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date picker
    $('#reservationdate').datetimepicker({
      format: 'DD/MM/YYYY'
    });
    $('#reservationdate1').datetimepicker({
      format: 'DD/MM/YYYY'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({
      icons: {
        time: 'far fa-clock'
      }
    });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker({
        ranges: {
          'Today': [moment(), moment()],
          'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days': [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month': [moment().startOf('month'), moment().endOf('month')],
          'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate: moment()
      },
      function(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )


    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })

  })
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('customFile');
    fileInput.addEventListener('change', function(event) {
      const fileName = event.target.files[0] ? event.target.files[0].name : 'No file chosen';
      const fileLabel = fileInput.nextElementSibling;
      fileLabel.textContent = fileName;
    });
  });
</script>

<script>
  // Menambahkan baris baru saat tombol "Tambah item" diklik
  document.getElementById('addRow').addEventListener('click', function() {
    const tbody = document.getElementById('positionsBody');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
             <td>
                            <select name="dosen[]" class="form-control" required>
                              <option value="" disabled selected>Dosen</option>
                              @foreach ($dosen as $dsn)
                              <option value="{{ $dsn->id }}">
                              <span>(NIDN : {{ $dsn->details->nidn }})</span> {{$dsn->details->gelar_depan}} {{ $dsn->name }}, {{$dsn->details->gelar_belakang}}
                              </option>
                              @endforeach
                            </select>
                          </td>
                          <td>
                            <select name="statusdosen[]" class="form-control" required>
                              <option value="" disabled selected>Status</option>
                              @foreach ($statusdosen as $stats)
                              <option value="{{ $stats->id }}">
                                {{ $stats->status_dosen }}
                              </option>
                              @endforeach
                            </select>
                            <input type="hidden" class="form-control" id="exampleInput" value="{{ $pesertamagang->usermahasiswa->id }}">
                          </td>
                          <td>
                            <button type="button" class="btn btn-danger btn-sm delete-row">
                              <i class="fas fa-trash"></i>
                            </button>
                          </td>
      `;

    tbody.appendChild(newRow);
  });

  // Menghapus baris saat tombol hapus diklik
  document.getElementById('positionsBody').addEventListener('click', function(e) {
    if (e.target && e.target.closest('.delete-row')) {
      const row = e.target.closest('tr'); // Mencari baris terdekat
      if (row) {
        row.remove(); // Menghapus baris
      }
    }
  });
</script>

@endsection