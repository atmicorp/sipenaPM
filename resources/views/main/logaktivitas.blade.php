@extends('master.layoutsmaster')

@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Log Aktivitas</h1>
        </div>
      </div>
    </div>
  </section>

  @if ($errors->any())
    <div class="alert alert-danger">
      @foreach ($errors->all() as $error)
        {{ $error }}
      @endforeach
    </div>
  @endif

  <section class="content">
    <div class="container-fluid">
      <div class="card card-default">
        <div class="card-header">
          <form method="GET" action="{{ route('logaktivitas') }}" class="form-inline">
            <select name="jenis" class="form-control form-control-sm mr-2 mb-1">
              <option value="">Semua jenis</option>
              @foreach (['keamanan', 'data', 'nilai'] as $j)
                <option value="{{ $j }}" {{ request('jenis') === $j ? 'selected' : '' }}>{{ ucfirst($j) }}</option>
              @endforeach
            </select>

            <select name="aksi" class="form-control form-control-sm mr-2 mb-1">
              <option value="">Semua aksi</option>
              @foreach ($daftarAksi as $a)
                <option value="{{ $a }}" {{ request('aksi') === $a ? 'selected' : '' }}>{{ $a }}</option>
              @endforeach
            </select>

            <input type="text" name="akun" value="{{ request('akun') }}" placeholder="Email akun"
                   class="form-control form-control-sm mr-2 mb-1">

            <input type="date" name="dari" value="{{ request('dari') }}" class="form-control form-control-sm mr-1 mb-1">
            <span class="mr-1 mb-1">s/d</span>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="form-control form-control-sm mr-2 mb-1">

            <button type="submit" class="btn btn-primary btn-sm mr-1 mb-1">
              <i class="fas fa-search"></i> Saring
            </button>
            <a href="{{ route('logaktivitas') }}" class="btn btn-secondary btn-sm mb-1">Reset</a>
          </form>
        </div>

        <div class="card-body table-responsive p-0">
          <table class="table table-bordered table-striped table-sm">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>Jenis</th>
                <th>Aksi</th>
                <th>Akun</th>
                <th>Keterangan</th>
                <th>IP</th>
                <th>Detail</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($logs as $log)
                <tr>
                  <td class="text-nowrap">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                  <td>
                    @php
                      $warna = ['keamanan' => 'warning', 'data' => 'info', 'nilai' => 'primary'][$log->jenis] ?? 'secondary';
                    @endphp
                    <span class="badge badge-{{ $warna }}">{{ $log->jenis }}</span>
                  </td>
                  <td>{{ $log->aksi }}</td>
                  <td>{{ $log->user_email ?? '-' }}</td>
                  <td>{{ $log->deskripsi }}</td>
                  <td>{{ $log->ip_address ?? '-' }}</td>
                  <td>
                    @if ($log->data_lama || $log->data_baru || $log->url || $log->user_agent)
                      <details>
                        <summary>Lihat</summary>
                        @if ($log->target_tipe)
                          <div><strong>Target:</strong> {{ $log->target_tipe }} #{{ $log->target_id }}</div>
                        @endif
                        @if ($log->url)
                          <div><strong>URL:</strong> {{ $log->metode }} {{ $log->url }}</div>
                        @endif
                        @if ($log->user_agent)
                          <div><strong>Perangkat:</strong> {{ $log->user_agent }}</div>
                        @endif
                        @if ($log->data_lama)
                          <div><strong>Sebelum:</strong></div>
                          <pre class="mb-1">{{ json_encode($log->data_lama, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                        @endif
                        @if ($log->data_baru)
                          <div><strong>Sesudah:</strong></div>
                          <pre class="mb-1">{{ json_encode($log->data_baru, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                        @endif
                      </details>
                    @else
                      -
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted">Belum ada catatan.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card-footer">
          <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">Total {{ $logs->total() }} catatan</small>
            {{ $logs->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection