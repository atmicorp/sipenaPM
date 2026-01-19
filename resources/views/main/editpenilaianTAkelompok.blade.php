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
        <div class="card card-default">
            <div class="card-header">
              <h3 class="card-title">Edit Nilai TA Kelompok {{$datapenguji->KelompokTA->nama_kelompok}}</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
            </div>
            <div class="card-body">
                {{-- Info dosen penguji --}}
                <div class="alert alert-info mb-4">
                    <strong>Dosen Penguji: </strong>{{ $datapenguji->userdosenTA->details->gelar_depan}} {{ $datapenguji->userdosenTA->name }}, {{ $datapenguji->userdosenTA->details->gelar_belakang}}
                </div>

                <form action="{{ route('updateNilaiKelompok', ['id_penguji' => $datapenguji->id, 'id_kategoriTA' => $kategoriTA->id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_penguji" value="{{ $datapenguji->id }}">
                    {{-- Loop aspek penilaian --}}
                    @foreach($aspekWithNilai as $aspek)
                        <div class="form-group mb-3">
                            <label for="aspek_{{ $aspek['id'] }}" class="font-weight-bold">
                                {{ $aspek['aspek_penilaian'] }}
                            </label>

                            @if($aspek['tipedata'] === 'Deskripsi')
                                <textarea 
                                    name="nilai[{{ $aspek['id'] }}]" 
                                    id="aspek_{{ $aspek['id'] }}" 
                                    class="form-control compose-textarea" 
                                    rows="3"
                                    placeholder="Tuliskan catatan di sini...">{{ $aspek['nilai'] }}</textarea>
                            @else
                                <input 
                                    type="number" 
                                    name="nilai[{{ $aspek['id'] }}]" 
                                    id="aspek_{{ $aspek['id'] }}" 
                                    class="form-control"
                                    min="10" max="100"
                                    value="{{ $aspek['nilai'] }}">
                            @endif
                        </div>
                    @endforeach

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>      
        </div>
        <!-- end kelompok -->

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
    $(document).ready(function() {
        $('textarea.compose-textarea').summernote({
            height: 300,
            toolbar: [
                ['style', ['ul', 'ol']]
            ]
        });
    });
</script>
  @endsection
