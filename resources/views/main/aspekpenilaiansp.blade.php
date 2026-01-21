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
          <h3 class="card-title">Aspek Penilaian Sidang Proposal</h3>

          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <div class="row">
            <div class="col-md-12">
            <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>No</th>
                    <th>Aspek Penilaian</th>
                    <th>Deskripsi Penilaian</th>
                    <th>Porsi Penilaian</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach ($dataaspek as $item)
                    <tr>
                      <td>{{$loop->iteration}}</td>
                      <td>
                        <div class="user-block">
                          <span class="username">
                            {{$item->aspek_penilaian }}
                          </span>
                          <span class="description">{{$item->tipedata }}</span>
                        </div>
                      </td>
                      <td><p>{!! $item->deskripsi_penilaian !!}</p></td>
                      <td>{{$item->porsi_penilaian }} %</td>     
                      <td>
                        <!-- Form Delete hanya muncul jika ID bukan 1 -->
                        @if ($item->id != 1)
                          <form action="{{ route('deleteAspeksp', $item->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                              <i class="fas fa-trash"></i> Hapus
                            </button>
                          </form>
                        @endif
                      </td>            
                    </tr>
                  @endforeach
                  </tbody>
                </table>
                @foreach ($dataaspek as $item)
                  <div class="modal fade" id="aspek{{ $item->id }}" tabindex="-1" aria-labelledby="aspekLabel{{ $item->id }}" aria-hidden="true">
                      <div class="modal-dialog">
                          <div class="modal-content">
                              <div class="modal-header">
                                  <h5 class="modal-title" id="aspekLabel{{ $item->id }}">Edit Aspek: {{ $item->aspek_penilaian }}</h5>
                                  <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                  </button>
                              </div>
                              <div class="modal-body">
                                  <!-- Form untuk Edit Aspek -->
                                  <form action="#" method="POST">
                                      @csrf
                                      @method('PUT')
                                      <div class="mb-3">
                                          <label for="aspek_penilaian_{{ $item->id }}" class="form-label">Aspek Penilaian</label>
                                          <input type="text" class="form-control" id="aspek_penilaian_{{ $item->id }}" name="aspek_penilaian" value="{{ $item->aspek_penilaian }}" required>
                                      </div>
                                      <div class="mb-3">
                                          <label for="deskripsi_penilaian_{{ $item->id }}" class="form-label">Deskripsi Penilaian</label>
                                          <textarea name="deskripsi_penilaian" id="deskripsi_penilaian_{{ $item->id }}" class="form-control" style="height: 300px" required>{!! $item->deskripsi_penilaian !!}</textarea>
                                        </div>
                                      <div class="mb-3">
                                          <label for="porsi_penilaian_{{ $item->id }}" class="form-label">Porsi Penilaian (%)</label>
                                          <input type="number" class="form-control" id="porsi_penilaian_{{ $item->id }}" name="porsi_penilaian" value="{{ $item->porsi_penilaian }}" required>
                                      </div>
                                      <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                                  </form>
                              </div>
                          </div>
                      </div>
                  </div>
                  @endforeach
                


              <form method="POST" action="{{route('storeaspekdatasp')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                  
                  <div class="border p-3 rounded mb-3">
                  <h3 class="card-title mb-3">Tambah Data</h3>
                    <table class="table table-striped" id="positionsTable">
                      <thead>
                        <tr>
                          <th>Aspek Penilaian*</th>
                          <th style="width: 40%;">Deskripsi Penilaian*</th>
                          <th>Porsi Penilaian (%)*</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody id="positionsBody">
                        <tr>
                          <td>
                            <input type="text" class="form-control" name="aspek[]" placeholder="Aspek Penilaian" required>
                          </td>
                          <td>
                          <textarea name="desk[]" id="compose-textarea" class="form-control" style="height: 300px" required></textarea>
                          </td>
                          <td>
                          <input type="number" class="form-control" name="porsi[]" placeholder="Porsi Penilaian" required>
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
<!-- modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>


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
  document.getElementById('addRow').addEventListener('click', function () {
  const tbody = document.getElementById('positionsBody');
  const newRow = document.createElement('tr');
  const uniqueId = `compose-textarea-${Date.now()}`; // Membuat ID unik untuk textarea

  newRow.innerHTML = `
    <td>
      <input type="text" class="form-control" name="aspek[]" placeholder="Aspek Penilaian" required>
    </td>
    <td>
      <textarea name="desk[]" id="${uniqueId}" class="form-control" style="height: 300px" required></textarea>
    </td>
    <td>
      <input type="number" class="form-control" name="porsi[]" placeholder="Porsi Penilaian" required>
    </td>
    <td>
      <button type="button" class="btn btn-danger btn-sm delete-row">
        <i class="fas fa-trash"></i>
      </button>
    </td>
  `;

  tbody.appendChild(newRow);

  // Inisialisasi Summernote pada textarea yang baru ditambahkan
  $(`#${uniqueId}`).summernote({
    toolbar: [
      ['style', ['ul', 'ol']] // Menampilkan hanya tombol UL dan OL
    ]
  });
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

<script>
  $(function () {
    // Add text editor dengan toolbar khusus
    $('#compose-textarea').summernote({
      toolbar: [
        ['style', ['ul', 'ol']] // Menampilkan hanya tombol UL dan OL
      ]
    });
  });
</script>

@endsection