<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Akses Admin | Penilaian Mahasiswa</title>
  <link rel="stylesheet" href="{{ asset('css/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
  <link rel="icon" href="{{ asset('images/atmi.png') }}?v=2" type="image/x-icon">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <img src="{{ asset('images/atmi.png') }}" alt="ATMI" height="70">
      <h5 class="mt-2 mb-0"><strong>Akses Admin</strong></h5>
    </div>
    <div class="card-body">

      @if ($errors->any())
        <div class="alert alert-danger">
          @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @endif

      <form action="{{ route('login.manual.process') }}" method="POST">
        @csrf
        <div class="input-group mb-3">
          <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email" required autofocus>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>