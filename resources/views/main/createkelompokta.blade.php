@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Kelompok TA</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($sudahAdaPenilaian)
                <div class="alert alert-warning">
                    <strong>Tidak bisa membuat kelompok TA baru.</strong>
                    Sudah ada data penilaian TA (kelompok maupun individu) yang tersimpan pada tahun ajaran berjalan.
                    Silakan lakukan RESET DATA terlebih dahulu sebelum membuat kelompok TA yang baru.
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    Generate Kelompok TA
                </div>
                <div class="card-body">
                    <fieldset id="fieldsetGenerate" @if($sudahAdaPenilaian) disabled @endif>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label>Jumlah Kelompok</label>
                                <input type="number" id="jumlahKelompok" class="form-control" min="1">
                            </div>
                            <div class="col-md-3">
                                <label>Tahun Ajaran</label>
                                <input type="text" id="tahunAjaran" class="form-control" placeholder="2026/2027">
                            </div>
                            <div class="col-md-3">
                                <button type="button" onclick="generatePreview()" class="btn btn-primary">Generate</button>
                                <button type="button" onclick="resetPreview()" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </fieldset>

                    <table id="tabelPreview" class="table mt-3" style="display:none;">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Kelompok</th>
                            </tr>
                        </thead>
                        <tbody id="tabelPreviewBody"></tbody>
                    </table>

                    <form id="formSimpan" method="POST" action="{{ route('kelompokta.store') }}" style="display:none;">
                        @csrf
                        <input type="hidden" name="tahun_perkuliahan" id="hiddenTahun">
                        <div id="hiddenInputs"></div>
                        <button type="submit" id="btnSimpan" class="btn btn-success mt-2">Simpan</button>
                    </form>
                </div>
            </div>

        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
const sudahAdaPenilaian = @json($sudahAdaPenilaian);

function generatePreview() {
    if (sudahAdaPenilaian) {
        alert('Tidak bisa membuat kelompok TA baru. Silakan reset data penilaian terlebih dahulu.');
        return;
    }

    const jumlah = parseInt(document.getElementById('jumlahKelompok').value);
    const tahun = document.getElementById('tahunAjaran').value;

    if (!jumlah || jumlah < 1) {
        alert('Isi jumlah kelompok terlebih dahulu.');
        return;
    }
    if (!tahun) {
        alert('Isi tahun ajaran terlebih dahulu.');
        return;
    }

    const tbody = document.getElementById('tabelPreviewBody');
    tbody.innerHTML = '';
    document.getElementById('hiddenTahun').value = tahun;

    for (let i = 1; i <= jumlah; i++) {
        const nomor = i.toString().padStart(2, '0');
        const namaDefault = `Kelompok TA ${nomor}`;

        tbody.innerHTML += `
            <tr>
                <td>${nomor}</td>
                <td><input type="text" class="form-control nama-kelompok" value="${namaDefault}"></td>
            </tr>`;
    }

    document.getElementById('tabelPreview').style.display = 'table';
    document.getElementById('formSimpan').style.display = 'block';
}

document.getElementById('formSimpan').addEventListener('submit', function (e) {
    e.preventDefault();

    if (sudahAdaPenilaian) {
        alert('Tidak bisa membuat kelompok TA baru. Silakan reset data penilaian terlebih dahulu.');
        return;
    }

    const namaInputs = document.querySelectorAll('.nama-kelompok');
    const hiddenInputs = document.getElementById('hiddenInputs');
    hiddenInputs.innerHTML = '';

    namaInputs.forEach((input) => {
        hiddenInputs.innerHTML += `<input type="hidden" name="nama_kelompok[]" value="${input.value}">`;
    });

    document.getElementById('btnSimpan').disabled = true;
    document.getElementById('btnSimpan').innerText = 'Menyimpan...';

    this.submit();
});

function resetPreview() {
    document.getElementById('jumlahKelompok').value = '';
    document.getElementById('tahunAjaran').value = '';
    document.getElementById('tabelPreviewBody').innerHTML = '';
    document.getElementById('tabelPreview').style.display = 'none';
    document.getElementById('formSimpan').style.display = 'none';
}
</script>
@endsection