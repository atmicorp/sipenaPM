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
      <!-- SELECT2 EXAMPLE -->
      
<!-- ------------------------------------------------------------------------------------------------------------------------ -->

        <div class="card card-default">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Aspek Penilaian Individu</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool text-white" data-card-widget="collapse">
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
                    <th>Kategori TA</th>
                    <th>Deskripsi Penilaian</th>
                    <th>Porsi Penilaian</th>
                    <th>Action</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach ($aspekindividu as $item)
                    <tr>
                      <td>{{$loop->iteration}}</td>
                      <td>
                        <div class="user-block">
                          <span class="username">
                            {{$item->aspek_penilaian}}
                          </span>
                          <span class="description">{{$item->tipedata }}</span>
                        </div>
                      </td>
                      <td>{{$item->kategoriTA->nama_kategori}}</td>
                      <td><p>{!! $item->deskripsi_penilaian !!}</p></td>
                      <td>{{$item->porsi_penilaian }} %</td>     
                      <td>
                        <!-- Form Delete hanya muncul jika ID bukan 1 -->
                        @if ($item->tipedata != "Deskripsi")
                          <form action="{{ route('deleteaspektaindividu', $item->id) }}" method="POST" style="display:inline;">
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
                <form method="POST" action="{{route('storeaspekdatataindividu')}}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                  <div class="border p-3 rounded mb-3">
                  <h3 class="card-title mb-3">Tambah Data</h3>
                    <table class="table table-striped" id="positionsTable">
                      <thead>
                        <tr>
                          <th>Aspek Penilaian*</th>
                          <th>Kategori TA*</th>
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
                         <select class="form-control" name="id_kategori_ta[]" required>
                              <option value="" disabled selected>Pilih Kategori TA</option>
                              @foreach($kategoriTa as $kategori)
                                  <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                              @endforeach
                          </select>
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
        <select class="form-control" name="id_kategori_ta[]" required>
             <option value="" disabled selected>Pilih Kategori TA</option>
             @foreach($kategoriTa as $kategori)
                 <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                @endforeach
      </select>
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


