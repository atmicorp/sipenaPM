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

        <!-- Nav tabs for switching between tables -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <a class="nav-link active" id="nilai-tab" data-toggle="tab" href="#nilai" role="tab" aria-controls="nilai" aria-selected="true">Nilai Praktik Kerja</a>
          </li>
          <li class="nav-item" role="presentation">
            <a class="nav-link" id="rata-rata-tab" data-toggle="tab" href="#rata-rata" role="tab" aria-controls="rata-rata" aria-selected="false">Nilai Rata-rata</a>
          </li>
        </ul>

        <!-- Tab content -->
        <div class="tab-content" id="myTabContent">
          
          <!-- Tabel Nilai Praktik Kerja (sebelum rata-rata) -->
          <div class="tab-pane fade show active" id="nilai" role="tabpanel" aria-labelledby="nilai-tab">
            <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title">Nilai Praktik Kerja Mahasiswa (Magang)</h3>

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
                      <th class="uppercase">Mahasiswa</th>
                      <th class="uppercase">NIM</th>
                      <th class="uppercase">Dosen</th>
                      @foreach ($datapengujimagang->flatten(1)->first() as $key => $value)
                          @if($key !== 'id_penguji' && $key !== 'id_mahasiswa' && $key !== 'mahasiswa' && $key !== 'nim' && $key !== 'dosen')
                              <th class="uppercase">{{ $key }}</th> <!-- Kolom dinamis dari array -->
                          @endif
                      @endforeach
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $counter = 1; @endphp <!-- Inisialisasi ulang counter -->
                    @foreach ($datapengujimagang as $pengujiGroup)
                        @foreach ($pengujiGroup as $item)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $item['mahasiswa'] }}</td>
                                <td>{{ $item['nim'] }}</td>
                                <td>{{ $item['dosen'] }}</td>

                                @foreach ($item as $key => $value)
                                    @if($key !== 'id_penguji' && $key !== 'id_mahasiswa' && $key !== 'mahasiswa'  && $key !== 'nim' && $key !== 'dosen')
                                    <td clas>{!! $value ?? '<strong style="color: red;">N/A</strong>'  !!}</td> 
                                    @endif
                                @endforeach
                                 <td>
                                  <a href="{{ route('editnilaitamagang', [
                                        'id_penguji'   => $item['id_penguji'], 
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
          </div>

          <!-- Tabel Nilai Rata-rata -->
          <div class="tab-pane fade" id="rata-rata" role="tabpanel" aria-labelledby="rata-rata-tab">
            <div class="card card-default">
              <div class="card-header">
                <h3 class="card-title">Nilai Rata-rata Praktik Kerja Mahasiswa (Magang)</h3>

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
                        <th class="uppercase">Mahasiswa</th>
                        <th class="uppercase">NIM</th>
                      
                        @foreach ($datapengujimagangModified->flatten(1)->first() as $key => $value)
                            @if($key !== 'id_penguji' && $key !== 'id_mahasiswa' && $key !== 'mahasiswa' && $key !== 'dosen' && $key !== 'nim' )
                                <th class="uppercase">{{ $key }}</th> <!-- Kolom dinamis dari array -->
                            @endif
                        @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @php $counter = 1; @endphp
                    @foreach ($datapengujimagangModified as $pengujiGroup)
                        @foreach ($pengujiGroup as $item)
                            <tr>
                                <td>{{ $counter++ }}</td>
                                <td>{{ $item['mahasiswa'] }}</td>
                                <td>{{ $item['nim'] }}</td>
                                
                              

                                @foreach ($item as $key => $value)
                                    @if($key !== 'id_penguji' && $key !== 'id_mahasiswa' && $key !== 'mahasiswa' && $key !== 'dosen' && $key !== 'nim' )
                                    <td>
                                      @if(is_numeric($value))
                                          {{ number_format($value, 2, ',', '.') }}
                                      @elseif(!empty($value))
                                          {!! $value !!}
                                      @else
                                          <strong style="color: red;">N/A</strong>
                                      @endif
                                  </td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    @endforeach
                  </tbody>
                </table>
              </div>   
            </div>
          </div>

        </div>
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
          // Inisialisasi DataTables untuk tabel pada tab "Approve"
          $("#example1").DataTable({
              "responsive": true,
              "lengthChange": false,
              "autoWidth": false,
              "buttons": [
                  {
                      extend: 'excel',
                      title: "Nilai Magang",
                  },
                  {
                      extend: 'pdf',
                      title: "Nilai Magang",
                  },
              ]
          }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
  
          // Perbarui responsivitas tabel saat tab berubah, <a> yang memiliki atribut data-toggle dengan nilai "tab"
          $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
              $.fn.dataTable.tables({visible: true, api: true}).columns.adjust().responsive.recalc();
          });
  
          // Inisialisasi DataTables untuk tabel pada tab "Not Approve"
          $("#example2").DataTable({
              "responsive": true,
              "lengthChange": false,
              "autoWidth": false,
              "buttons": [
                  {
                      extend: 'excel',
                      
                      title: "Nilai Rata-rata Magang",
                  },
                  {
                      extend: 'pdf',
                      
                      title: "Nilai Rata-rata Magang",
                  },
              ]
          }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      });
  </script>

  
@endsection
