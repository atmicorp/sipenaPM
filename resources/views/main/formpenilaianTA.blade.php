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
        <div class="col-md-12">
            <!-- Profile Image -->
            <div class="row">
                <div class="col-md-6">
                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center"></div>
                            <h3 class="profile-username text-center">
                                <strong>{{ $datapenguji->KelompokTA->nama_kelompok }}</strong>
                            </h3>
                            <p class="text-center"><strong>{{ $kategoriTA->nama_kategori }}</strong></p>
                                <div class="form-group mt-3">
                                @if (
                                  Auth::check() &&isset($datapenguji->userdosenTA, $datapenguji->KelompokTA, $kategoriTA) &&
                                  file_exists(public_path('uploads/laporan/' . $datapenguji->userdosenTA->id . '-' . 'REV' . '-' . $datapenguji->KelompokTA->nama_kelompok . '-' . $kategoriTA->nama_kategori . '-' . $datapenguji->userdosenTA->name . '.pdf'
                                      ))

                                    )
                                    <h6>Revisi {{$kategoriTA->nama_kategori}} {{$datapenguji->KelompokTA->nama_kelompok}} :</h6>
                                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#pdfModal">
                                        view
                                    </button>
                                @else
                                    <p>Silahkan upload file catatan revisi.</p>
                                @endif
                                <form action="{{ route('uploadRevisiTA') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group mt-3">
                                        <label for="file">Upload File .pdf (maksimal 2MB)</label>
                                        <div class="custom-file">
                                        
                                          <input type="hidden" name="id_kelompok_ta" value="{{$datapenguji->KelompokTA->id}}">
                                          <input type="hidden" name="id_kategori_ta" value="{{$kategoriTA->id}}">
                                          <input type="hidden" name="id_dosen" value="{{$datapenguji->userdosenTA->id}}">
                                          <input type="hidden" name="nama_dosen" value="{{$datapenguji->userdosenTA->name}}">
                                          <input type="file" name="file" class="custom-file-input" id="file" accept=".pdf" required>
                                            <label class="custom-file-label" for="file">Choose file</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </form>
                                @if (Auth::user() && file_exists(public_path('uploads/laporan/' . $datapenguji->userdosenTA->id . '-' . 'REV' . '-' . $datapenguji->KelompokTA->nama_kelompok . '-' . $kategoriTA->nama_kategori . '-' . $datapenguji->userdosenTA->name . '.pdf')))
                                    <div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="pdfModalLabel">Revisi {{$kategoriTA->nama_kategori}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <embed src="{{ asset('uploads/laporan/' . $datapenguji->userdosenTA->id . '-' . 'REV' . '-' . $datapenguji->KelompokTA->nama_kelompok . '-' . $kategoriTA->nama_kategori . '-' . $datapenguji->userdosenTA->name . '.pdf') }}" type="application/pdf" width="100%" height="500px">
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

                <div class="col-md-6">
                    <!-- Range Penilaian -->
                    <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                      <div class="text-center">
                          <strong>Range Penilaian</strong>
                      </div>
                      <hr>
                      <div class="row text-center">
                          <div class="col-md-3 col-6">
                              <p><strong>Unggul:</strong> <br> 81 - 90</p>
                          </div>
                          <div class="col-md-3 col-6">
                              <p><strong>Baik:</strong> <br> 71 - 80</p>
                          </div>
                          <div class="col-md-3 col-6">
                              <p><strong>Cukup:</strong> <br> 61 - 70</p>
                          </div>
                          <div class="col-md-3 col-6">
                              <p><strong>Kurang:</strong> <br> 50 - 60</p>
                          </div>
                      </div>
                  </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
      @if($datapenguji->status_dosen == 3 && $statusnow->status == "1")
      <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">Validasi Kelompok {{ $datapenguji->KelompokTA->nama_kelompok }}</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <p>Silakan pilih salah satu tindakan berikut:</p>
          
            <div class="mb-3">
                <form action="{{ route('lanjutPenilaian') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="id_kelompok_ta" value="{{ $datapenguji->id_kelompok_ta }}">
                    <input type="hidden" name="id_kategori_ta" value="{{ $kategoriTA->id }}">
                    <button type="submit" class="btn btn-success">Lanjut</button>
                </form>

                <form action="{{ route('tolakPenilaian') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="id_kelompok_ta" value="{{ $datapenguji->id_kelompok_ta }}">
                    <input type="hidden" name="id_kategori_ta" value="{{ $kategoriTA->id }}">
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </form>
            </div>

            <div class="alert alert-info mt-3" role="alert">
              <strong>Keterangan:</strong><br>
              - Jika memilih <strong>Lanjut</strong>, maka anda dapat melakukan penilaian dan kelompok dapat melanjutkan ke event berikutnya.<br>
              - Jika memilih <strong>Tolak</strong>, maka kelompok akan dijadwalkan untuk presentasi ulang.
            </div>
          </div>
        </div>
        </div>
      
      </div>
      @elseif($statusnow->status == "2")
      <!-- gabungan form -->
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Penilaian Tugas Akhir (Kelompok & Individu)</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>

            <div class="card-body">
              <form action="{{ route('penilaianTAstoreGabungan') }}" method="POST">
                @csrf

                <!-- =========================================
                    BAGIAN 1 — PENILAIAN KELOMPOK
                ========================================== -->
                <h4 class="mb-3 text-primary">
                  <i class="fas fa-users"></i> Penilaian Kelompok
                </h4>

                @if ($hasilpenilaianTA->isNotEmpty())
                  <div class="card p-3 shadow-sm mb-4">
                    <h5 class="text-primary">Hasil Penilaian TA Kelompok</h5>
                    <ul class="list-group list-group-flush">
                      @foreach ($hasilpenilaianTA as $hasil)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span class="fw-bold">{{ $hasil->aspekpenilaianTA->aspek_penilaian }}</span>
                          @if ($hasil->aspekpenilaianTA->tipedata == 'Deskripsi')
                            <div class="p-2 w-100 border border-secondary bg-white" style="white-space: pre-line;">
                              {!! $hasil->nilai !!}
                            </div>
                          @else
                            <span class="badge bg-success text-white" style="font-size: 1rem;">{{ $hasil->nilai }}</span>
                          @endif
                        </li>
                      @endforeach
                    </ul>
                  </div>
                @else
                  <div class="mb-4">
                    @foreach ($aspekpenilaian as $item)
                      <div class="mb-3">
                        <label class="form-label">
                          <strong>{{ $item->aspek_penilaian }}</strong>,
                          <span class="text-muted">{{ $item->porsi_penilaian }}%</span>
                        </label>
                        <div class="text-secondary" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                          {!! $item->deskripsi_penilaian !!}
                        </div>

                        <input type="hidden" name="data_penguji_ta" value="{{ $datapenguji->id }}">
                        <input type="hidden" name="id_dosen" value="{{ $datapenguji->id_dosen }}">
                        <input type="hidden" name="id_kelompok_ta" value="{{ $datapenguji->id_kelompok_ta }}">
                        <input type="hidden" name="id_kategori_ta" value="{{ $kategoriTA->id }}">

                        @if($item->tipedata == 'Deskripsi')
                          <textarea id="compose-textarea-{{ $item->id }}"  name="penilaian_kelompok[{{ $item->id }}]" class="form-control" style="height: 200px" required></textarea>
                        @else
                          <input type="number" name="penilaian_kelompok[{{ $item->id }}]" class="form-control" placeholder="{{ $item->aspek_penilaian }}" min="10" max="100" required>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @endif

                <hr class="my-4">

                <!-- =========================================
                    BAGIAN 2 — PENILAIAN INDIVIDU
                ========================================== -->
                <h4 class="mb-3 text-primary">
                  <i class="fas fa-user-graduate"></i> Penilaian Individu
                </h4>

                @if (!empty($nilaiIndividu))
                  <div class="card p-3 shadow-sm">
                    <h5 class="text-primary">Hasil Penilaian Individu</h5>
                    <ul class="list-group list-group-flush">
                      @foreach ($nilaiIndividu as $hasil)
                        <div class="mb-3">
                          <div class="d-flex align-items-start mb-2">
                            <img src="{{ !empty($hasil['photo']) && $hasil['photo'] !== 'Tidak Ditemukan' ? 'data:image/jpeg;base64,' . $hasil['photo'] : asset('images/pp.jpg') }}" 
                              class="img-circle elevation-2 mt-1"
                              style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                            <div class="ps-2">
                              <strong>{{ $hasil['Nama'] }}</strong>
                              <div class="text-muted" style="font-size: 12px;">{{ $hasil['NIM'] }}</div>
                            </div>
                          </div>

                          <ul class="list-group">
                           @foreach ($hasil['Penilaian'] as $penilaian)
                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{ $penilaian['Aspek_penilaian'] ?? '-' }}</span>

                                @if (($penilaian['tipedata'] ?? '') == 'Deskripsi')
                                  <div class="p-2 w-100 border border-secondary bg-white" style="white-space: pre-line;">
                                    {!! $penilaian['Nilai'] ?? '' !!}
                                  </div>
                                @else
                                  <span class="badge bg-success text-white" style="font-size: 1rem;">
                                    {{ $penilaian['Nilai'] ?? '-' }}
                                  </span>
                                @endif
                              </li>
                            @endforeach
                          </ul>
                        </div>
                        @if (!$loop->last)
                          <hr style="border: 1px solid #ccc; margin: 1.5rem 0;">
                        @endif
                      @endforeach
                    </ul>
                  </div>
                @else
                  @foreach ($pesertaTA as $pta)
                    <div class="mb-4">
                      <div class="d-flex align-items-start mb-2">
                        <img src="{{ !empty($pta->usermahasiswaTA->details->photo) && $pta->usermahasiswaTA->details->photo !== 'Tidak Ditemukan' ? 'data:image/jpeg;base64,' . $pta->usermahasiswaTA->details->photo : asset('images/pp.jpg') }}" 
                          class="img-circle elevation-2 mt-1"
                          style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                        <div class="ps-2">
                          <strong>{{ $pta->usermahasiswaTA->name }}</strong>
                          <div class="text-muted" style="font-size: 12px;">{{ $pta->usermahasiswaTA->details->nim }}</div>
                        </div>
                      </div>

                      <input type="hidden" name="id_mahasiswa[]" value="{{ $pta->usermahasiswaTA->id }}">

                      @foreach ($aspekpenilaianindividu as $item)
                        <div class="mb-3">
                          <label class="form-label">
                            <strong>{{ $item->aspek_penilaian }}</strong>,
                            <span class="text-muted">{{ $item->porsi_penilaian }}%</span>
                          </label>
                          <div class="text-secondary" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                            {!! $item->deskripsi_penilaian !!}
                          </div>

                          @if($item->tipedata == 'Deskripsi')
                            <textarea name="penilaian_individu[{{ $pta->usermahasiswaTA->id }}][{{ $item->id }}]" class="form-control" style="height: 200px" required></textarea>
                          @else
                            <input type="number" class="form-control" name="penilaian_individu[{{ $pta->usermahasiswaTA->id }}][{{ $item->id }}]" placeholder="{{ $item->aspek_penilaian }}" min="10" max="100" required>
                          @endif  
                        </div>
                      @endforeach

                      @if (!$loop->last)
                        <hr style="border: 1px solid #ccc; margin: 1.5rem 0;">
                      @endif
                    </div>
                  @endforeach
                @endif

                <!-- =========================================
                    TOMBOL SUBMIT
                ========================================== -->
                <div class="text-center mt-5">
                  <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-paper-plane"></i> Submit Semua Penilaian
                  </button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>

      @elseif($statusnow->status == "3")
      <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">Validasi Kelompok {{ $datapenguji->KelompokTA->nama_kelompok }}</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">

            <div class="alert alert-danger mt-3" role="alert">
              <strong>Keterangan:</strong><br>
              Pengujian ditolak, Kelompok {{ $datapenguji->KelompokTA->nama_kelompok }} akan dijadwalkan untuk presentasi ulang.
            </div>
          </div>
        </div>
        </div>
      
      </div>
      @elseif($statusnow->status == "0")
      <div class="row">
        <div class="col-md-12">
        <div class="card card-primary card-outline">
          <div class="card-header">
            <h3 class="card-title">Validasi Kelompok {{ $datapenguji->KelompokTA->nama_kelompok }}</h3>
            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="alert alert-info mt-3" role="alert">
              <strong>Keterangan:</strong><br>
              Penilaian Presentasi {{ $kategoriTA->nama_kategori }} untuk Kelompok {{ $datapenguji->KelompokTA->nama_kelompok }} belum dibuka.
            </div>
          </div>
        </div>
        </div>
      
      </div>
      @else
       <!-- gabungan form -->
      <div class="row">
        <div class="col-md-12">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Penilaian Tugas Akhir (Kelompok & Individu)</h3>
              <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                  <i class="fas fa-minus"></i>
                </button>
              </div>
            </div>

            <div class="card-body">
              <form action="{{ route('penilaianTAstoreGabungan') }}" method="POST">
                @csrf

                <!-- =========================================
                    BAGIAN 1 — PENILAIAN KELOMPOK
                ========================================== -->
                <h4 class="mb-3 text-primary">
                  <i class="fas fa-users"></i> Penilaian Kelompok
                </h4>

                @if ($hasilpenilaianTA->isNotEmpty())
                  <div class="card p-3 shadow-sm mb-4">
                    <h5 class="text-primary">Hasil Penilaian TA Kelompok</h5>
                    <ul class="list-group list-group-flush">
                      @foreach ($hasilpenilaianTA as $hasil)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                          <span class="fw-bold">{{ $hasil->aspekpenilaianTA->aspek_penilaian }}</span>
                          @if ($hasil->aspekpenilaianTA->tipedata == 'Deskripsi')
                            <div class="p-2 w-100 border border-secondary bg-white" style="white-space: pre-line;">
                              {!! $hasil->nilai !!}
                            </div>
                          @else
                            <span class="badge bg-success text-white" style="font-size: 1rem;">{{ $hasil->nilai }}</span>
                          @endif
                        </li>
                      @endforeach
                    </ul>
                  </div>
                @else
                  <div class="mb-4">
                    @foreach ($aspekpenilaian as $item)
                      <div class="mb-3">
                        <label class="form-label">
                          <strong>{{ $item->aspek_penilaian }}</strong>,
                          <span class="text-muted">{{ $item->porsi_penilaian }}%</span>
                        </label>
                        <div class="text-secondary" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                          {!! $item->deskripsi_penilaian !!}
                        </div>

                        <input type="hidden" name="data_penguji_ta" value="{{ $datapenguji->id }}">
                        <input type="hidden" name="id_dosen" value="{{ $datapenguji->id_dosen }}">
                        <input type="hidden" name="id_kelompok_ta" value="{{ $datapenguji->id_kelompok_ta }}">
                        <input type="hidden" name="id_kategori_ta" value="{{ $kategoriTA->id }}">

                        @if($item->tipedata == 'Deskripsi')
                          <textarea id="compose-textarea-{{ $item->id }}"  name="penilaian_kelompok[{{ $item->id }}]" class="form-control" style="height: 200px" required></textarea>
                        @else
                          <input type="number" name="penilaian_kelompok[{{ $item->id }}]" class="form-control" placeholder="{{ $item->aspek_penilaian }}" min="10" max="100" required>
                        @endif
                      </div>
                    @endforeach
                  </div>
                @endif

                <hr class="my-4">

                <!-- =========================================
                    BAGIAN 2 — PENILAIAN INDIVIDU
                ========================================== -->
                <h4 class="mb-3 text-primary">
                  <i class="fas fa-user-graduate"></i> Penilaian Individu
                </h4>

                @if (!empty($nilaiIndividu))
                  <div class="card p-3 shadow-sm">
                    <h5 class="text-primary">Hasil Penilaian Individu</h5>
                    <ul class="list-group list-group-flush">
                      @foreach ($nilaiIndividu as $hasil)
                        <div class="mb-3">
                          <div class="d-flex align-items-start mb-2">
                            <img src="{{ !empty($hasil['photo']) && $hasil['photo'] !== 'Tidak Ditemukan' ? 'data:image/jpeg;base64,' . $hasil['photo'] : asset('images/pp.jpg') }}" 
                              class="img-circle elevation-2 mt-1"
                              style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                            <div class="ps-2">
                              <strong>{{ $hasil['Nama'] }}</strong>
                              <div class="text-muted" style="font-size: 12px;">{{ $hasil['NIM'] }}</div>
                            </div>
                          </div>

                          <ul class="list-group">
                           @foreach ($hasil['Penilaian'] as $penilaian)
                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="fw-bold">{{ $penilaian['Aspek_penilaian'] ?? '-' }}</span>

                                @if (($penilaian['tipedata'] ?? '') == 'Deskripsi')
                                  <div class="p-2 w-100 border border-secondary bg-white" style="white-space: pre-line;">
                                    {!! $penilaian['Nilai'] ?? '' !!}
                                  </div>
                                @else
                                  <span class="badge bg-success text-white" style="font-size: 1rem;">
                                    {{ $penilaian['Nilai'] ?? '-' }}
                                  </span>
                                @endif
                              </li>
                            @endforeach
                          </ul>
                        </div>
                        @if (!$loop->last)
                          <hr style="border: 1px solid #ccc; margin: 1.5rem 0;">
                        @endif
                      @endforeach
                    </ul>
                  </div>
                @else
                  @foreach ($pesertaTA as $pta)
                    <div class="mb-4">
                      <div class="d-flex align-items-start mb-2">
                        <img src="{{ !empty($pta->usermahasiswaTA->details->photo) && $pta->usermahasiswaTA->details->photo !== 'Tidak Ditemukan' ? 'data:image/jpeg;base64,' . $pta->usermahasiswaTA->details->photo : asset('images/pp.jpg') }}" 
                          class="img-circle elevation-2 mt-1"
                          style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                        <div class="ps-2">
                          <strong>{{ $pta->usermahasiswaTA->name }}</strong>
                          <div class="text-muted" style="font-size: 12px;">{{ $pta->usermahasiswaTA->details->nim }}</div>
                        </div>
                      </div>

                      <input type="hidden" name="id_mahasiswa[]" value="{{ $pta->usermahasiswaTA->id }}">

                      @foreach ($aspekpenilaianindividu as $item)
                        <div class="mb-3">
                          <label class="form-label">
                            <strong>{{ $item->aspek_penilaian }}</strong>,
                            <span class="text-muted">{{ $item->porsi_penilaian }}%</span>
                          </label>
                          <div class="text-secondary" style="font-size: 0.9rem; margin-bottom: 0.5rem;">
                            {!! $item->deskripsi_penilaian !!}
                          </div>

                          @if($item->tipedata == 'Deskripsi')
                            <textarea name="penilaian_individu[{{ $pta->usermahasiswaTA->id }}][{{ $item->id }}]" class="form-control" style="height: 200px" required></textarea>
                          @else
                            <input type="number" class="form-control" name="penilaian_individu[{{ $pta->usermahasiswaTA->id }}][{{ $item->id }}]" placeholder="{{ $item->aspek_penilaian }}" min="10" max="100" required>
                          @endif  
                        </div>
                      @endforeach

                      @if (!$loop->last)
                        <hr style="border: 1px solid #ccc; margin: 1.5rem 0;">
                      @endif
                    </div>
                  @endforeach
                @endif

                <!-- =========================================
                    TOMBOL SUBMIT
                ========================================== -->
                <div class="text-center mt-5">
                  <button type="submit" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-paper-plane"></i> Submit Semua Penilaian
                  </button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>

      @endif
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
