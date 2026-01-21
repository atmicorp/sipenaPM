@extends('master.layoutsmaster')

@section('styles')
  <!-- DataTables -->
  <link rel="stylesheet" href="{{asset('dtable/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('dtable/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{asset('dtable/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

  <style>
    th.uppercase {
    text-transform: uppercase;
    }
  </style>
@endsection

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <!-- Content Header if needed -->
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

      <!-- penilaian Kelompok -->
        <div class="card card-default collapsed-card">
            <div class="card-header">
              <h3 class="card-title">Nilai Kelompok</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
            </div>
            <div class="card-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th class="uppercase">NO</th>
                        <th class="uppercase">Dosen</th>
                        @foreach ($datapengujiTA->flatten(1)->first() as $key => $value)
                            @if($key !== 'id_penguji' && $key !== 'id_mahasiswa' && $key !== 'id_kelompok_ta' && $key !== 'dosen' && $key !== 'Catatan Revisi')
                                <th class="uppercase">{{ $key }}</th> <!-- Kolom dinamis dari array -->
                            @endif
                        @endforeach
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php $counter = 1; @endphp <!-- Inisialisasi ulang counter -->
                      @foreach ($datapengujiTA as $pengujiGroup)
                          @foreach ($pengujiGroup as $item)
                              <tr>
                                  <td>{{ $counter++ }}</td>
                        
                                  <td>{{ $item['dosen'] }}</td>

                                  @foreach ($item as $key => $value)
                                      @if($key !== 'id_penguji' && $key !== 'id_kelompok_ta' && $key !== 'mahasiswa' && $key !== 'dosen' && $key !== 'Catatan Revisi')
                                          <td clas>{!! $value ?? '<strong style="color: red;">N/A</strong>'  !!}</td> 
                                      @endif
                                  @endforeach
                                  <td>
                                  <a href="{{ route('editnilaitakelompok', ['id_penguji' => $item['id_penguji'], 'id_kategoriTA' => $kategoriTA->id]) }}" 
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit Nilai
                                  </a>
                                  </td>
                              </tr>
                          @endforeach
                      @endforeach
                    </tbody>
                  </table>

                  
                   <!-- end modal -->
            </div>       
        </div>
        <!-- end kelompok -->


        <!-- Penilaian Individu -->
        <div class="card card-default collapsed-card">
            <div class="card-header">
              <h3 class="card-title">Nilai Individu</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
            </div>
            <div class="card-body">
            <table id="example2" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="uppercase">NO</th>
                        <th class="uppercase">Nama Dosen</th> <!-- Nama dosen lebih dulu -->
                        <th class="uppercase">Nama Mahasiswa</th> <!-- Tambahkan nama mahasiswa setelah dosen -->
                        <th class="uppercase">NIM</th> <!-- Tambahkan nama mahasiswa setelah dosen -->

                        @foreach ($datapengujiByMahasiswa->flatten(1)->first() as $key => $value)
                            @if (!in_array($key, ['id_penguji', 'id_kelompok_ta', 'id_mahasiswa', 'id_dosen', 'nama_dosen', 'nama_mahasiswa', 'Catatan Revisi','nim']))
                                <th class="uppercase">{{ $key }}</th> <!-- Kolom aspek dinamis -->
                            @endif
                        @endforeach
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp <!-- Inisialisasi ulang counter -->
                    @foreach ($datapengujiByMahasiswa as $pengujiGroup)
                        @foreach ($pengujiGroup as $item)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $item['nama_dosen'] }}</td> <!-- Tampilkan nama dosen -->
                                <td>{{ $item['nama_mahasiswa'] }}</td> <!-- Tampilkan nama mahasiswa setelah dosen -->
                                <td>{{ $item['nim'] }}</td>
                                @foreach ($item as $key => $value)
                                    @if (!in_array($key, ['id_penguji', 'id_kelompok_ta', 'id_mahasiswa', 'id_dosen', 'nama_dosen', 'nama_mahasiswa', 'Catatan Revisi','nim']))
                                    <td clas>{!! $value ?? '<strong style="color: red;">N/A</strong>'  !!}</td>  
                                    @endif
                                @endforeach
                                <td>
                                  <a href="{{ route('editnilaitaIndividu', [
                                        'id_penguji'   => $item['id_penguji'], 
                                        'id_kategoriTA'=> $kategoriTA->id,
                                        'id_dosen'     => $item['id_dosen'],
                                        'id_mahasiswa' => $item['id_mahasiswa']
                                    ]) }}" 
                                    class="btn btn-sm btn-primary">
                                      <i class="fas fa-edit"></i> Edit Nilai
                                  </a>
                                  </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            </div>       
        </div>
        <!-- end individu -->

        <!-- Penilaian Rata2-->
        <div class="card card-default collapsed-card">
            <div class="card-header">
              <h3 class="card-title">Nilai Rata-Rata</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
            </div>
            <div class="card-body">
            <table id="example3" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th class="uppercase">NO</th>
            <th class="uppercase">Nama Mahasiswa</th> <!-- Nama Mahasiswa -->

            @foreach ($hasilGabungan->first() as $key => $value)
                @if (!in_array($key, ['id_mahasiswa', 'id_kelompok_ta', 'nama_mahasiswa','total_nilai_individu','total_nilai_kelompok']))
                    <th class="uppercase">{{ $key }}</th> <!-- Kolom aspek dinamis -->
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        @php $counter = 1; @endphp <!-- Inisialisasi ulang counter -->
        @foreach ($hasilGabungan as $item)
            <tr>
                <td>{{ $counter++ }}</td>
                <td>{{ $item['nama_mahasiswa'] }}</td> <!-- Tampilkan nama mahasiswa -->
      
                @foreach ($item as $key => $value)
                    @if (!in_array($key, ['id_mahasiswa', 'id_kelompok_ta', 'nama_mahasiswa','total_nilai_individu','total_nilai_kelompok']))
                    <td>
                    @if (is_string($value))
                        {{ $value }}  <!-- Tampilkan langsung sebagai string -->
                    @elseif (is_numeric($value))
                        {{ number_format($value, 2) }}  <!-- Tampilkan sebagai angka dengan 2 desimal -->
                    @else
                        {!! $value ?? '<strong style="color: red;">N/A</strong>' !!}
                    @endif
                    </td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

            </div>       
        </div>
        <!-- end Rata2-->


      </div>
    </section>
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
    // Inisialisasi DataTables untuk masing-masing tabel
    let table1 = $("#example1").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": [
            {
                extend: 'excel',
                title: "Nila TA Kelompok",
            },
            {
                extend: 'pdf',
                title: "Nila TA Kelompok",
            },
        ]
    });

    let table2 = $("#example2").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": [
            {
                extend: 'excel',
                title: "Nilai TA Individu",
            },
            {
                extend: 'pdf',
                title: "Nilai TA Individu",
            },
        ]
    });

    let table3 = $("#example3").DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": [
            {
                extend: 'excel',
                title: "Nilai Rata-Rata TA",
            },
            {
                extend: 'pdf',
                title: "Nilai Rata-Rata TA",
            },
        ]
    });

    // Tambahkan tombol export ke masing-masing wrapper
    table1.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    table2.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
    table3.buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');

    // **Fix Responsiveness saat Card di-collapse**
    $('[data-card-widget="collapse"]').on('click', function () {
        let $cardBody = $(this).closest('.card').find('.card-body');

        setTimeout(function () {
            if ($cardBody.is(':visible')) {
                // Perbaiki ukuran tabel saat card dibuka kembali
                table1.columns.adjust().responsive.recalc();
                table2.columns.adjust().responsive.recalc();
                table3.columns.adjust().responsive.recalc(); // Tambahkan ini
            }
        }, 300);
    });

    // Perbarui responsivitas tabel saat tab berubah
    $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
        $.fn.dataTable.tables({visible: true, api: true}).columns.adjust().responsive.recalc();
    });
});

  </script>



  
@endsection
