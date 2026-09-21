@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Kelola Kategori TA</h1>
        </div>
      </div>
    </div>
  </section>

  @if ($errors->any())
    <div class="alert alert-danger mx-3">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger mx-3">{{ session('error') }}</div>
  @endif

  @if(session('success'))
    <div class="alert alert-success mx-3">{{ session('success') }}</div>
  @endif

  <section class="content">
    <div class="container-fluid">
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title">Kategori TA (Tahapan Sidang)</h3>
          <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambahKategori">
              <i class="fas fa-plus"></i> Tambah Kategori
            </button>
          </div>
        </div>
        <div class="card-body">
          <p class="text-muted">
            Urutan di sini menentukan urutan tahap sidang TA (dipakai di menu sidebar & alur "lolos ke tahap berikutnya").
            Kategori yang sudah punya jadwal/aspek/nilai tidak bisa diubah urutannya atau dihapus, demi menjaga data yang sudah ada.
          </p>
          <table class="table table-bordered table-striped">
            <thead>
              <tr>
                <th style="width: 80px;">Urutan</th>
                <th>Nama Kategori</th>
                <th style="width: 160px;">Status</th>
                <th style="width: 200px;">Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($kategoriTA as $kategori)

              <div class="modal fade" id="modalEditKategori-{{ $kategori->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form action="{{ route('kategorita.update', $kategori->id) }}" method="POST">
                      @csrf
                      @method('PUT')
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Kategori: {{ $kategori->nama_kategori }}</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                          <label>Nama Kategori <span class="text-danger">*</span></label>
                          <input type="text" name="nama_kategori" class="form-control" value="{{ $kategori->nama_kategori }}" required>
                        </div>
                        <div class="form-group">
                          <label>Urutan <span class="text-danger">*</span></label>
                          @if ($kategori->dipakai)
                            <input type="number" class="form-control" value="{{ $kategori->urutan }}" disabled>
                            <small class="text-muted">Urutan tidak bisa diubah karena kategori ini sudah dipakai di data jadwal/aspek/nilai.</small>
                          @else
                            <input type="number" name="urutan" class="form-control" value="{{ $kategori->urutan }}" min="1" required>
                          @endif
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <tr>
                <td>{{ $kategori->urutan }}</td>
                <td>{{ $kategori->nama_kategori }}</td>
                <td>
                  @if ($kategori->dipakai)
                    <span class="badge badge-secondary">Sudah dipakai</span>
                  @else
                    <span class="badge badge-success">Belum dipakai</span>
                  @endif
                </td>
                <td>
                  <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalEditKategori-{{ $kategori->id }}">
                    <i class="fas fa-edit"></i> Edit
                  </button>

                  @if (!$kategori->dipakai)
                  <form action="{{ route('kategorita.destroy', $kategori->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Hapus kategori {{ $kategori->nama_kategori }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                      <i class="fas fa-trash"></i> Hapus
                    </button>
                  </form>
                  @else
                  <button type="button" class="btn btn-sm btn-danger" disabled title="Tidak bisa dihapus, sudah dipakai di data lain">
                    <i class="fas fa-trash"></i> Hapus
                  </button>
                  @endif
                </td>
              </tr>

              @empty
              <tr><td colspan="4" class="text-center text-muted">Belum ada kategori TA.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="modalTambahKategori" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('kategorita.store') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Tambah Kategori TA</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" name="nama_kategori" class="form-control" placeholder="Misal: Sidang Revisi" required>
          </div>
          <div class="form-group">
            <label>Urutan <span class="text-danger">*</span></label>
            <input type="number" name="urutan" class="form-control" min="1" value="{{ $kategoriTA->max('urutan') + 1 }}" required>
            <small class="text-muted">Urutan menentukan posisi tahap ini di antara tahap-tahap lain (dipakai di sidebar & alur kelulusan tahap).</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection