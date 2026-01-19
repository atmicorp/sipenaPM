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
                <!-- USERS LIST -->
                @role('Admin')
                <div class="card">
                  <div class="card-header">
                    <h3 class="card-title">Tim Dosen dan Pengajar Perancangan Manufaktur</h3>

                    <div class="card-tools">
                      <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                      </button>
                      <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                      </button>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body p-0">
                    <ul class="users-list clearfix">
                    @foreach ($dosen as $ds)
                      <li>
                      @if($ds->details->photo)
                      <img class="profile-user-img img-fluid img-circle"src="data:image/jpeg;base64,{{ $ds->details->photo }}" alt="User Image" style="max-width: 70%;">
                      @else
                          <img class="profile-user-img img-fluid img-circle"src="{{ asset('images/pp.jpg') }}" alt="User Image" style="max-width: 70%;">
                      @endif
                        <p class="users-list-name" style="margin-bottom: 0;"><strong>{{$ds->details->gelar_depan}} {{$ds->name}}, {{$ds->details->gelar_belakang}}</strong></p>
                        <span class="users-list-date">{{$ds->details->jabatan}}</span>
                      </li>
                      @endforeach
                    </ul>
                    <!-- /.users-list -->
                  </div>
                  <!-- /.card-body -->
                  <!-- <div class="card-footer text-center">
                    <a href="javascript:">View All Users</a>
                  </div> -->
                  <!-- /.card-footer -->
                </div>
                @endrole
                <!-- ------------------- -->

                @role('Mahasiswa')
                <div class="card collapsed-card">
                    <div class="card-header text-center">
                        <h3 class="card-title">Informasi Magang</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                          
                        </div>
                    </div>

                    <div class="card-body text-center">
                        <!-- Bagian Dosen -->
                        <div class="row justify-content-center">
                            @foreach ($dosenpenguji as $ds)
                                <div class="col-md-3 col-sm-6 mb-4">
                                    <div class="p-3 border rounded bg-light">
                                        @if($ds->userdosen->details->photo)
                                            <img class="profile-user-img img-fluid img-circle" 
                                                src="data:image/jpeg;base64,{{ $ds->userdosen->details->photo }}" 
                                                alt="User Image" style="width: 80px; height: 80px;">
                                        @else
                                            <img class="profile-user-img img-fluid img-circle" 
                                                src="{{ asset('images/pp.jpg') }}" 
                                                alt="User Image" style="width: 80px; height: 80px;">
                                        @endif
                                        <p class="mt-2 mb-1">
                                            <strong>{{ $ds->userdosen->details->gelar_depan }} 
                                                    {{ $ds->userdosen->name }}, 
                                                    {{ $ds->userdosen->details->gelar_belakang }}</strong>
                                        </p>
                                        <span class="text-muted">{{ $ds->status->status_dosen }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Garis Pembatas -->
                        <hr class="my-4 border-top border-secondary">

                        <!-- Bagian Jadwal Presentasi -->
                        <div class="row justify-content-center">
                            <div class="col-md-4 col-sm-12">
                                <div class="p-3 border rounded bg-light">
                                    <p class="mb-2"><i class="fas fa-calendar-day text-primary"></i> <strong> Tanggal Presentasi:</strong></p>
                                    <h5 class="text-dark">{{ $datamagang->tanggal_presentasi }}</h5>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="p-3 border rounded bg-light">
                                    <p class="mb-2"><i class="fas fa-clock text-success"></i> <strong> Jam Presentasi:</strong></p>
                                    <h5 class="text-dark">
                                        {{ \Carbon\Carbon::parse($datamagang->jam_presentasi)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($datamagang->jam_presentasi_selesai)->format('H:i') }} WIB
                                    </h5>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-12">
                                <div class="p-3 border rounded bg-light">
                                    <p><i class="fas fa-map-marker-alt text-danger"></i> <strong> Lokasi:</strong></p>
                                    <h5 class="text-dark">{{ $datamagang->lokasi }}</h5>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4 border-top border-secondary">
                      
                        <!-- PDF Laporan Magang (Rata Kiri) -->
                        <div class="row">
                            <div class="col-12 text-left mt-3">
                               <form action="{{ route('upload.judul.magang') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                  
                                    <h6>Judul Laporan Magang</h6>
                                    <textarea name="judul_laporan" class="form-control compose-textarea" style="height: 150px" required>{{ $datamagang->judul_laporan ?? '' }}</textarea>

                                    <button type="submit" class="btn btn-primary mt-3">Simpan</button>
                                </form>
                              @php
                                  $nim = Auth::user()->details->nim ?? null;
                                  $filePath = public_path('uploads/laporan/' . $nim . '.pdf');
                                  $fileName = $nim . '.pdf';
                              @endphp

                              <form action="{{ route('upload.laporan.magang') }}" method="POST" enctype="multipart/form-data">
                                  @csrf
                                  <div class="form-group mt-3">
                                      <label for="file">Upload File .pdf (maksimal 2MB)</label>
                                      <div class="d-flex align-items-center">
                                          <div class="custom-file" style="width: 70%;">
                                              <input 
                                                  type="file" 
                                                  name="file" 
                                                  class="custom-file-input" 
                                                  id="file" 
                                                  accept=".pdf" 
                                                  {{ file_exists($filePath) ? '' : 'required' }}
                                              >
                                              <label class="custom-file-label" for="file">
                                                  {{ file_exists($filePath) ? $fileName : 'Choose file' }}
                                              </label>
                                          </div>

                                          {{-- Jika file laporan sudah ada, tampilkan tombol view --}}
                                          @if (file_exists($filePath))
                                              <a 
                                                  href="{{ asset('uploads/laporan/' . $fileName) }}" 
                                                  target="_blank" 
                                                  class="btn btn-outline-success btn-sm ml-3"
                                              >
                                                  <i class="fas fa-file-pdf"></i> View PDF
                                              </a>
                                          @endif
                                      </div>
                                  </div>

                                <button type="submit" class="btn btn-primary mt-2">
                                    {{ file_exists($filePath) ? 'Re-upload' : 'Upload' }}
                                </button>
                            </form>

                                @if (Auth::user() && file_exists(public_path('uploads/laporan/' . Auth::user()->details->nim . '.pdf')))
                                    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="pdfModalLabel">Laporan Magang</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <embed src="{{ asset('uploads/laporan/' . Auth::user()->details->nim . '.pdf') }}" type="application/pdf" width="100%" height="500px">
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

                    </div>
                </div>

                <div class="card collapsed-card">
                    <div class="card-header text-center">
                        <h3 class="card-title">Informasi Tugas Akhir</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                          
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Bagian Dosen -->
                        <div class="row justify-content-center">
                        @if (!empty($dosenta) && count($dosenta) > 0)
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
                                            <strong>{{ $ds->userdosenTA->details->gelar_depan }} 
                                                    {{ $ds->userdosenTA->name }}, 
                                                    {{ $ds->userdosenTA->details->gelar_belakang }}</strong>
                                        </p>
                                        <span class="text-muted">{{ $ds->statusdosenTA->status_dosen }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">Data dosen tidak tersedia.</p>
                        @endif
                           
                        </div>

                        <!-- Garis Pembatas -->
                        <hr class="my-4 border-top border-secondary">

                        <!-- Bagian Jadwal Presentasi -->
                        <div class="row">
                          <div class="col-md-12">
                          <form action="{{ route('judulta.update', ['id' => $pesertatamhs->id_kelompok_ta]) }}" method="POST" id="edittaForm">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" class="form-control" id="id_kel_ta" name="id_kel_ta" value="{{ $pesertatamhs->id_kelompok_ta }}" readonly>
                                        <label for="namakel_ta-peserta " class="form-label mt-3">Judul TA</label>
                                        <textarea name="judulta" class="form-control compose-textarea" style="height: 300px" required>
                                            {{ $pesertatamhs->kelompokTA->judul_ta }}
                                        </textarea>
                                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                    </form> 
                          </div>                  
                        </div>

                        <hr class="my-4 border-top border-secondary">
                      
                        <!-- PDF Laporan Magang (Rata Kiri) -->
                     

                    </div>
                </div>
  
                <!-- --------------------------TA---------------------------- -->
                @endrole
                <!--/.card -->
              </div>
        </div>
        <!-- /.row -->

        <!-- Main row -->
        @role('Admin')
          <div class="row">
            <!-- Left col -->
            <div class="col-md-12">
              <!-- TABLE: LATEST ORDERS -->
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Data Mahasiswa Praktik Kerja</h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0 px-4 py-4">
                <table id="example1" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No</th>                
                    <th>Nama Mahasiswa</th>
                    <th>Perusahaan</th>
                    <th>Tanggal Presentasi</th>
                  
                  </tr>
                  </thead>
                  <tbody>
                
                  @foreach($pesertamagang as $peserta)             
                    <tr>
                      <td>{{$loop->iteration}}</td>
                      <td>
                          <div class="user-block">
                            <img src="{{  $peserta->usermahasiswa->details->photo ? 'data:image/jpeg;base64,' .  $peserta->usermahasiswa->details->photo : asset('images/pp.jpg') }}" class="img-circle elevation-2 mb-2" alt="Default User Image" style="width: 40px; height: 40px;">
                                <span class="username"><strong>{{ $peserta->usermahasiswa->name }}</strong> </span>
                                <span class="description">NIM : {{ $peserta->usermahasiswa->details->nim }}</span>
             
                          </div>
                      </td>
                      <td><strong>{{ $peserta->perusahaanmagang->nama }}</strong></td>
                      <td>{{ \Carbon\Carbon::parse($peserta->tanggal_presentasi)->translatedFormat('d F Y') }}</td>
                    

                    </tr>
                  @endforeach
                  </tbody>
                </table>
                  <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->

                <div class="card">
                <div class="card-header">
                  <h3 class="card-title">Data Mahasiswa Tugas Akhir</h3>

                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0 px-4 py-4">
                <table id="example2" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>No</th>                
                    <th>Nama Mahasiswa</th>
                    <th>Perusahaan</th>
                    <th>Tanggal Presentasi</th>
                  
                  </tr>
                  </thead>
                  <tbody>
                
                  @foreach($pesertamagang as $peserta)             
                    <tr>
                      <td>{{$loop->iteration}}</td>
                      <td>
                          <div class="user-block">
                            <img src="{{  $peserta->usermahasiswa->details->photo ? 'data:image/jpeg;base64,' .  $peserta->usermahasiswa->details->photo : asset('images/pp.jpg') }}" class="img-circle elevation-2 mb-2" alt="Default User Image" style="width: 40px; height: 40px;">
                                <span class="username"><strong>{{ $peserta->usermahasiswa->name }}</strong> </span>
                                <span class="description">NIM : {{ $peserta->usermahasiswa->details->nim }}</span>
             
                          </div>
                      </td>
                      <td><strong>{{ $peserta->perusahaanmagang->nama }}</strong></td>
                      <td>{{ \Carbon\Carbon::parse($peserta->tanggal_presentasi)->translatedFormat('d F Y') }}</td>
                    

                    </tr>
                  @endforeach
                  </tbody>
                </table>
                  <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->

            </div>
            <!-- /.col -->
          </div>
        @endrole

      
          @role('Dosen')
            <div class="row">
              <!-- Left col -->
              <div class="col-md-12">
                <!-- TABLE: LATEST ORDERS -->
                <div class="card collapsed-card">
                  <div class="card-header">
                        <h3 class="card-title">Data Mahasiswa Magang</h3>
                        <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                        </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                  <table id="example1" class="table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th style="width: 1%">
                                  No
                              </th>
                              <th style="width: 30%">
                                  Mahasiswa
                              </th>
                              <th style="width: 20%">
                                  Status Anda
                              </th>
                              <th style="width: 20%">
                                  Tanggal Presentasi
                              </th>
                              <th style="width: 20%">
                                  Jam Presentasi
                              </th>
                              <th style="width: 20%">
                                  Lokasi
                              </th>
                            
                              
                          </tr>
                      </thead>
                      <tbody>
                        @foreach($pesertabimbingan as $peserta)
                          <tr>
                              <td>{{ $loop->iteration }}</td>
                              <td>
                              <div class="user-block">
                                <img src="{{  $peserta->usermahasiswa->details->photo ? 'data:image/jpeg;base64,' .  $peserta->usermahasiswa->details->photo : asset('images/pp.jpg') }}" class="img-circle elevation-2 mb-2" alt="Default User Image" style="width: 40px; height: 40px;">
                                    <span class="username"><strong>{{ $peserta->usermahasiswa->name }}</strong> </span>
                                    <span class="description">NIM : {{ $peserta->usermahasiswa->details->nim }}</span>
                              </div>
                              </td>
                              <td><strong>{{ $peserta->status->status_dosen }}</strong></td>
                              <td>{{ \Carbon\Carbon::parse($peserta->tanggal_presentasi)->translatedFormat('d F Y') }}</td>
                              <td>{{ \Carbon\Carbon::parse($peserta->jam_presentasi)->format('H:i') }} WIB</td>
                              <td>{{ $peserta->lokasi }}</td>
                            
                          </tr>  
                        @endforeach    
                    </tbody>
                  </table>
                
                  </div>            
                </div>
  <!-- ----------------------------------------------------------------------- -->
                <div class="card">
                  <div class="card-header">
                        <h3 class="card-title">Data Mahasiswa TA</h3>
                        <div class="card-tools">
                          <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                          </button>
                        </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                  <table id="example2" class="table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th style="width: 1%">
                                  No
                              </th>
                              <th style="width: 10%">
                                  Kelompok TA
                              </th>
                              <th style="width: 30%">
                                  Judul TA
                              </th>
                              <th style="width: 30%">
                                  Anggota Mahasiswa
                              </th>
                              <th style="width: 20%">
                                  Status Anda
                              </th>
                              <th style="width: 10%">
                                  Jadwal
                              </th>     
                          </tr>
                      </thead>
                      <tbody>
                      @foreach($pengujita as $pta)   
                          <tr>
                              <td>{{$loop->iteration}}</td>
                              <td>{{$pta->KelompokTA->nama_kelompok}}</td>
                              <td>{!! $pta->KelompokTA->judul_ta !!}</td>
                              <td>
                                  @foreach($pesertata->where('id_kelompok_ta', $pta->id_kelompok_ta) as $psta)
                                  <div class="d-flex flex-column">
                                          <div class="d-flex align-items-start mb-2">
                                              <img src="{{  $psta->usermahasiswaTA->details->photo ? 'data:image/jpeg;base64,' .  $psta->usermahasiswaTA->details->photo : asset('images/pp.jpg') }}" 
                                                          class="img-circle elevation-2 mt-1" 
                                                          alt="Foto Penguji" 
                                                          style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                  <div class="d-flex flex-column ps-3">  <!-- Padding kiri untuk beri jarak -->
                                                      <strong class="d-block mb-1">{{ $psta->usermahasiswaTA->name }}</strong>
                                                      <span class="text-muted" style="font-size: 12px;">NIM : {{ $psta->usermahasiswaTA->details->nim }} ;</span>                              
                                                  </div>                                   
                                          </div>
                                      </div>
                                  @endforeach
                              </td>
                              <td>{{$pta->statusdosenTA->status_dosen}}</td>
                              <td>
                                <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewJadwal{{ $pta->KelompokTA->id }}">
                                    <i class="fas fa-calendar-alt"></i> View Jadwal
                                </button>
                              </td>
                          </tr>  

                          <div class="modal fade" id="viewJadwal{{ $pta->KelompokTA->id }}" tabindex="-1" role="dialog" aria-labelledby="viewJadwalLabel{{ $pta->KelompokTA->id }}" aria-hidden="true">
                              <div class="modal-dialog modal-lg" role="document">
                                  <div class="modal-content">
                                      <div class="modal-header">
                                          <h5 class="modal-title" id="viewJadwalLabel{{ $pta->KelompokTA->id }}">Detail Jadwal {{$pta->KelompokTA->nama_kelompok}}</h5>
                                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                              <span aria-hidden="true">&times;</span>
                                          </button>
                                      </div>
                                      <div class="modal-body">
                                        @foreach($jadwalta->where('id_kelompok_ta', $pta->id_kelompok_ta) as $jdta)
                                        <div class="card border-0 shadow-sm mb-3">
                                            <div class="card-header bg-info ">
                                                {{ $jdta->kategoriTA->nama_kategori }}
                                            </div>
                                            <div class="card-body">
                                                <p class="mb-1"><strong>Tanggal Presentasi:</strong> 
                                                    {{ $jdta->tanggal_presentasi ? \Carbon\Carbon::parse($jdta->tanggal_presentasi)->translatedFormat('l, d F Y') : '-' }}
                                                </p>
                                                <p class="mb-1"><strong>Jam:</strong> 
                                                    {{ $jdta->jam_presentasi && $jdta->jam_presentasi_selesai 
                                                        ? \Carbon\Carbon::parse($jdta->jam_presentasi)->format('H:i') . ' - ' . \Carbon\Carbon::parse($jdta->jam_presentasi_selesai)->format('H:i') 
                                                        : '-' 
                                                    }} WIB
                                                </p>
                                                <p class="mb-1"><strong>Lokasi:</strong> 
                                                    {{ $jdta->lokasi ?? '-' }}
                                                </p>
                                            </div>
                                        </div>
                                        @endforeach       
                                    </div>

                                      <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      @endforeach
                    </tbody>
                  </table>
                
                  </div>            
                </div>
                <!-- /.card -->
              </div>
              <!-- /.col -->
            </div>
          @endrole

        


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

<script>
$(document).ready(function () {
    setTimeout(function () {
        console.log("Mengaktifkan Summernote untuk .compose-textarea");

        $('.compose-textarea').summernote({
            toolbar: [
                ['style', ['bold', 'italic', 'underline']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
            ],
            height: 150
        });
    }, 500);
});
</script>
@endsection

  