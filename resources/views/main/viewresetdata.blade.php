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
          <h3 class="card-title">Reset Database</h3>

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
            <!-- Peringatan -->
                <div class="alert alert-warning">
                    <strong>Perhatian!</strong> Mengklik tombol reset akan mereset <strong>Database</strong>. Data akan kembali ke pengaturan awal.
                </div>

                <!-- Tombol Reset -->
                <form action="{{ route('reset.database') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data?')">
                    @csrf
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Reset Database
                    </button>
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