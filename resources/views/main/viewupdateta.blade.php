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
        
            <div class="card card-default">
                <div class="card-header">
                    <h3 class="card-title">Edit Peserta TA</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-12 mb-3">
                        <!-- Kelompok -->
                        <div class="mb-2">
                            <label class="fw-bold">Kelompok:</label>
                            <div class="border p-2">{{$kelompokTA->nama_kelompok}}</div>
                        </div>
                        <!-- Judul -->
                        <div>
                        <label class="fw-bold">Judul:</label>
                        <div class="border p-2">{!! $kelompokTA->judul_ta !!}</div>
                        </div>
                    </div>
                </div>         
                <form method="POST" action="{{ route('pesertata.update', $kelompokTA->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                   
                    <div class="row">
                        <!-- Tabel Mahasiswa -->
                        <div class="col-md-6">
                            <label class="fw-bold">Peserta Mahasiswa</label>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Mahasiswa</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pesertaTA as $pta)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-start mb-2">
                                                    <img src="{{  $pta->usermahasiswaTA->details->photo ? 'data:image/jpeg;base64,' .  $pta->usermahasiswaTA->details->photo : asset('images/pp.jpg') }}" 
                                                                class="img-circle elevation-2 mt-1" 
                                                                alt="Foto Penguji" 
                                                                style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                        <div class="d-flex flex-column ps-3">  <!-- Padding kiri untuk beri jarak -->
                                                            <strong class="d-block mb-1">{{ $pta->usermahasiswaTA->name }}</strong>
                                                            <span class="text-muted" style="font-size: 12px;">NIM : {{ $pta->usermahasiswaTA->details->nim }}</span>                            
                                                        </div>                                   
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        <a href="{{ route('pesertata.destroy', ['id' => $pta->id]) }}"  onclick="return confirm('Apakah Anda yakin ingin menghapus ini?');"
                                            class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                        </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Input Mahasiswa -->
                            <div class="border p-3 rounded mb-3">
                                <table class="table table-striped" id="positionsTablemhs">
                                    <thead>
                                        <tr>
                                            <th>Mahasiswa</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="positionsBodymhs">
                                        <tr>
                                            <td>
                                                <select name="mahasiswa[]" class="form-control" >
                                                    <option value="" disabled selected>Mahasiswa</option>
                                                    @foreach ($mahasiswa as $mhs)
                                                    <option value="{{ $mhs->usermahasiswaTA->id }}">
                                                   ({{ $mhs->usermahasiswaTA->details->nim }}) {{ $mhs->usermahasiswaTA->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" id="addRowmhs" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Mahasiswa
                                </button>
                            </div>
                        </div>

                        <!-- Tabel Dosen -->
                        <div class="col-md-6">
                            <label class="fw-bold">Dosen Pembimbing / Penguji</label>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Dosen</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pengujiTA as $pjta)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-start mb-2">
                                                    <img src="{{  $pjta->userdosenTA->details->photo ? 'data:image/jpeg;base64,' .  $pjta->userdosenTA->details->photo : asset('images/pp.jpg') }}" 
                                                                class="img-circle elevation-2 mt-1" 
                                                                alt="Foto Penguji" 
                                                                style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                        <div class="d-flex flex-column ps-3">  <!-- Padding kiri untuk beri jarak -->
                                                            <strong class="d-block mb-1">{{ $pjta->userdosenTA->details->gelar_depan }} {{ $pjta->userdosenTA->name }}, {{ $pjta->userdosenTA->details->gelar_belakang }}</strong>                 
                                                            <span class="text-muted" style="font-size: 12px;">{{ $pjta->statusdosenTA->status_dosen }}</span>  
                                                            <span class="text-muted" style="font-size: 12px;">NIDN : {{ $pjta->userdosenTA->details->nidn }}</span>        
                                                                                 
                                                        </div>                                   
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                        <a href="{{ route('pengujita.destroy', ['id' => $pjta->id]) }}"  onclick="return confirm('Apakah Anda yakin ingin menghapus ini?');"
                                            class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-trash-alt"></i>
                                        </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Input Dosen -->
                            <div class="border p-3 rounded mb-3">
                                <table class="table table-striped" id="positionsTable">
                                    <thead>
                                        <tr>
                                            <th>Dosen</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="positionsBody">
                                        <tr>
                                            <td>
                                                <select name="dosen[]" class="form-control">
                                                    <option value="" disabled selected>Dosen</option>
                                                    @foreach ($dosen as $dsn)
                                                    <option value="{{ $dsn->id }}">
                                                        (NIDN : {{ $dsn->details->nidn }}) 
                                                        {{ $dsn->details->gelar_depan }} {{ $dsn->name }}, {{ $dsn->details->gelar_belakang }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select name="statusdosen[]" class="form-control">
                                                    <option value="" disabled selected>Status</option>
                                                    @foreach ($statusdosen as $stats)
                                                    <option value="{{ $stats->id }}">{{ $stats->status_dosen }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" id="addRow" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Dosen
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Submit -->
                    <div class="text-left mt-3">
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
                    </div>
                </form>

                </div>
            </div>

        </div>

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
        "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 5,
        "buttons": ["excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      $("#example2").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false, "pageLength": 5,
        "buttons": ["excel", "pdf", "print"]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      $('#example3').DataTable({
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
    setTimeout(function () {
        $('.compose-textarea').each(function () {
            let textareaId = $(this).attr('id'); // Ambil ID unik

            console.log("Mengaktifkan Summernote untuk:", textareaId);

            $('#' + textareaId).summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                ],
                height: 150
            });
        });
    }, 500); // Beri jeda 500ms agar semua elemen termuat dulu
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // **Tambah Mahasiswa**
    document.getElementById("addRowmhs").addEventListener("click", function () {
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td>
                <select name="mahasiswa[]" class="form-control">
                    <option value="" disabled selected>Mahasiswa</option>
                      @foreach ($mahasiswa as $mhs)
                        <option value="{{ $mhs->usermahasiswaTA->id }}">
                        ({{ $mhs->usermahasiswaTA->details->nim }}) {{ $mhs->usermahasiswaTA->name }}
                     </option>
                     @endforeach
                  </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm delete-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        document.getElementById("positionsBodymhs").appendChild(newRow);
    });

    // **Tambah Dosen**
    document.getElementById("addRow").addEventListener("click", function () {
        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td>
                <select name="dosen[]" class="form-control">
                    <option value="" disabled selected>Pilih Dosen</option>
                    @foreach ($dosen as $dsn)
                    <option value="{{ $dsn->id }}">
                        (NIDN : {{ $dsn->details->nidn }}) 
                        {{ $dsn->details->gelar_depan }} {{ $dsn->name }}, {{ $dsn->details->gelar_belakang }}
                    </option>
                    @endforeach
                </select>
            </td>
            <td>
                <select name="statusdosen[]" class="form-control">
                    <option value="" disabled selected>Pilih Status</option>
                    @foreach ($statusdosen as $stats)
                    <option value="{{ $stats->id }}">{{ $stats->status_dosen }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm delete-row">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        document.getElementById("positionsBody").appendChild(newRow);
    });

    // **Hapus Baris**
    document.addEventListener("click", function (event) {
        if (event.target.closest(".delete-row")) {
            event.target.closest("tr").remove();
        }
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form'); // Ganti selector jika ada form lebih dari satu

        form.addEventListener('submit', function (e) {
            let isValid = true;

            // Ambil semua pasangan dosen dan status
            const dosenSelects = form.querySelectorAll('select[name="dosen[]"]');
            const statusSelects = form.querySelectorAll('select[name="statusdosen[]"]');

            dosenSelects.forEach((dosenSelect, index) => {
                const statusSelect = statusSelects[index];

                // Jika dosen dipilih, status harus diisi
                if (dosenSelect.value && !statusSelect.value) {
                    isValid = false;
                    statusSelect.classList.add('is-invalid'); // Tambah class untuk styling error (optional)
                } else {
                    statusSelect.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault(); // Batalkan submit
                alert('Jika memilih dosen, status dosen juga wajib diisi.');
            }
        });
    });
</script>




@endsection


