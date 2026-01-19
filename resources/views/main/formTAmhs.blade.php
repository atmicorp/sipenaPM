@extends('master.layoutsmaster')
@section('styles')
<link rel="stylesheet" href="{{asset('dtable/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
 <link rel="stylesheet" href="{{asset('dtable/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
 <link rel="stylesheet" href="{{asset('dtable/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

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
        <!-- Info boxes -->
        
        <!-- /.row -->

        <div class="row">
          
        <div class="col-md-12">
             
           
                <div class="card">
                    <div class="card-header text-center">
                        <h3 class="card-title">Form {{$kategorita->nama_kategori}}</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                          
                        </div>
                    </div>

                    <div class="card-body text-center">
                        <!-- Bagian Dosen -->
                        <div class="row justify-content-center">
                        @foreach ($dosenta as $ds)
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="p-3 border rounded bg-light">
            @if($ds->userdosenTA->details->photo)
                <img class="profile-user-img img-fluid img-circle" 
                    src="data:image/jpeg;base64,{{ $ds->userdosenTA->details->photo }}" 
                    alt="User Image" style="width: 80px; height: 80px;">
            @else
                <img class="profile-user-img img-fluid img-circle" 
                    src="{{ asset('images/pp.jpg') }}" 
                    alt="User Image" style="width: 80px; height: 80px;">
            @endif

            <p class="mt-2 mb-1">
                <strong>
                    {{ $ds->userdosenTA->details->gelar_depan }} 
                    {{ $ds->userdosenTA->name }},
                    {{ $ds->userdosenTA->details->gelar_belakang }}
                </strong>
            </p>
            <span class="text-muted">{{ $ds->statusdosenTA->status_dosen }}</span>

            @php
                $fileName = $ds->userdosenTA->id . '-REV-' . $jadwalta->kelompokTA->nama_kelompok . '-' . $kategorita->nama_kategori . '-' . $ds->userdosenTA->name . '.pdf';
                $filePath = public_path('uploads/laporan/' . $fileName);
            @endphp

            @if (Auth::check() && file_exists($filePath))
                <h6></h6>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#pdfModalrev{{ $ds->userdosenTA->id }}">
                    Download Revisi
                </button>

                <!-- Modal -->
                <div class="modal fade" id="pdfModalrev{{ $ds->userdosenTA->id }}" tabindex="-1" role="dialog" aria-labelledby="pdfModalrevLabel{{ $ds->userdosenTA->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="pdfModalrevLabel{{ $ds->userdosenTA->id }}">
                                    Laporan {{ $kategorita->nama_kategori }}
                                </h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <embed src="{{ asset('uploads/laporan/' . $fileName) }}" type="application/pdf" width="100%" height="500px">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <br>
                <span class="text-muted small bg-warning px-2 py-1 rounded d-inline-block">Revisi belum di-upload.</span>
            @endif
        </div>
    </div>
@endforeach
                        </div>

                        <!-- Garis Pembatas -->
                        <hr class="my-4 border-top border-secondary">

                        <!-- Bagian Jadwal Presentasi -->

                        <div class="mb-4">
                        @php
                          $status = (int) ($verifikasi->status ?? -1);
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
                        <div class="row justify-content-center">
                            <div class="col-md-4 col-sm-12">
                                <div class="p-3 border rounded bg-light">
                                    <p class="mb-2"><i class="fas fa-calendar-day text-primary"></i> <strong> Tanggal Presentasi:</strong></p>
                                    <h5 class="text-dark">
                                        {{ $jadwalta->tanggal_presentasi 
                                            ? \Carbon\Carbon::parse($jadwalta->tanggal_presentasi)->translatedFormat('l, d F Y') 
                                            : 'Tanggal belum ditentukan' }}
                                    </h5>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="p-3 border rounded bg-light">
                                    <p class="mb-2"><i class="fas fa-clock text-success"></i> <strong> Jam Presentasi:</strong></p>
                                    <h5 class="text-dark">
                                    @if($jadwalta->jam_presentasi && $jadwalta->jam_presentasi_selesai)
                                          {{ \Carbon\Carbon::parse($jadwalta->jam_presentasi)->format('H:i') }} - 
                                          {{ \Carbon\Carbon::parse($jadwalta->jam_presentasi_selesai)->format('H:i') }} WIB
                                      @else
                                          Jadwal belum ditentukan
                                      @endif
                                    </h5>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="p-3 border rounded bg-light">
                                    <p><i class="fas fa-map-marker-alt text-danger"></i> <strong> Lokasi:</strong></p>
                                    <h5 class="text-dark">
                                        {{ $jadwalta->lokasi ?? 'Lokasi belum ditentukan' }}
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-top border-secondary">
                      
                        <!-- PDF Laporan Magang (Rata Kiri) -->

                        @if(in_array($verifikasi->status, ['1', '2', '4']))
                        <div class="row">
                            <div class="col-12 text-left mt-3">
                                @if (Auth::user() && file_exists(public_path('uploads/laporan/' . 'LAPORAN' . '-' . $jadwalta->kelompokTA->nama_kelompok . '-' . $kategorita->nama_kategori . '.pdf')))
                                    <h6>PDF {{$kategorita->nama_kategori}} :</h6>
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#pdfModal">
                                        Lihat Laporan
                                    </button>
                                @else
                                    <p>PDF Laporan TA belum di-upload.</p>
                                @endif
                                <form action="{{ route('uploadLaporanTA') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mt-3">
                                        <label for="file">Upload File .pdf (maksimal 2MB)</label>
                                        <div class="custom-file">
                                        
                                          <input type="hidden" name="id_kelompok_ta" value="{{$jadwalta->id_kelompok_ta}}">
                                          <input type="hidden" name="id_kategori_ta" value="{{$kategorita->id}}">
                                            <input type="file" name="file" class="custom-file-input" id="file" accept=".pdf" required>
                                            <label class="custom-file-label" for="file">Choose file</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </form>
                                @if (Auth::user() && file_exists(public_path('uploads/laporan/' . 'LAPORAN' . '-' . $jadwalta->kelompokTA->nama_kelompok . '-' . $kategorita->nama_kategori . '.pdf')))
                                    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="pdfModalLabel">Laporan {{$kategorita->nama_kategori}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <embed src="{{ asset('uploads/laporan/' . 'LAPORAN' . '-' . $jadwalta->kelompokTA->nama_kelompok . '-' . $kategorita->nama_kategori . '.pdf') }}" type="application/pdf" width="100%" height="500px">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif   
                            </div>
                        </div>
                        @else
                        <div class="row">
                            <div class="col-12 text-left mt-3">
                               
                            <div class="alert alert-danger mt-3" role="alert">
                            <strong>Keterangan:</strong><br>
                            Upload Laporan belum tersedia.
                            </div>
                             
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

               
        
          
   
        <!-- /.row -->
      </div><!--/. container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
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
    // Update label when a file is selected
    $('.custom-file-input').on('change', function () {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
});
</script>

<script>
      $(function () {
      // Inisialisasi DataTables untuk tabel di "Approve"
      let table1 = $("#example1").DataTable({
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
      });

      table1.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      // Inisialisasi DataTables untuk tabel di "Not Approve"
      let table2 = $("#example2").DataTable({
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
      });

      table2.buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

      // **Fix Responsiveness saat Card di-collapse**
      $('[data-card-widget="collapse"]').on('click', function () {
          let $cardBody = $(this).closest('.card').find('.card-body');

          setTimeout(function () {
              if ($cardBody.is(':visible')) {
                  // Perbaiki ukuran tabel saat card dibuka kembali
                  table1.columns.adjust().responsive.recalc();
                  table2.columns.adjust().responsive.recalc();
              }
          }, 300); // Tunggu animasi collapse selesai sebelum menyesuaikan ulang tabel
      });

      // Perbarui responsivitas tabel saat tab berubah
      $('a[data-toggle="tab"]').on('shown.bs.tab', function () {
          $.fn.dataTable.tables({visible: true, api: true}).columns.adjust().responsive.recalc();
      });
  });
  </script>
@endsection

  