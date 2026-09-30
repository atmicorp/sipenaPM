<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Penilaian Mahasiswa | Masuk</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,700&display=fallback">
  <link rel="stylesheet" href="{{ asset('css/fontawesome-free/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
  <link rel="icon" href="{{ asset('images/atmi.png') }}?v=2" type="image/x-icon">

  <style>
    .login-box { width: 380px; max-width: 92%; }
    .login-card { border: 0; border-radius: 12px; box-shadow: 0 4px 24px rgba(0, 0, 0, .08); }
    .login-card .card-body { padding: 2.25rem 2rem; }
    .login-logo-img { width: 72px; height: auto; }
    .login-title { font-weight: 700; font-size: 1.35rem; color: #212529; margin: 1rem 0 .25rem; }
    .login-subtitle { color: #6c757d; font-size: .95rem; margin-bottom: 1.75rem; }
    .btn-google {
      display: flex; align-items: center; justify-content: center;
      width: 100%; padding: .7rem 1rem;
      background: #fff; color: #3c4043;
      border: 1px solid #dadce0; border-radius: 8px;
      font-weight: 600; transition: background .15s, box-shadow .15s;
    }
    .btn-google:hover { background: #f8f9fa; color: #202124; text-decoration: none; box-shadow: 0 1px 4px rgba(0, 0, 0, .12); }
    .btn-google i { color: #db4437; font-size: 1.15rem; margin-right: .65rem; }
    .login-note { margin-top: 1.5rem; font-size: .82rem; color: #868e96; line-height: 1.5; }
    .login-footer { margin-top: 1rem; text-align: center; font-size: .78rem; color: #adb5bd; }
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="card login-card">
    <div class="card-body text-center">

      <img src="{{ asset('images/atmi.png') }}" alt="ATMI" class="login-logo-img">
      <h1 class="login-title">Penilaian Mahasiswa</h1>
      <p class="login-subtitle">Masuk untuk melanjutkan</p>

      @if (session('success'))
        <div class="alert alert-success text-left">{{ session('success') }}</div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger text-left">{{ session('error') }}</div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger text-left">
          @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      @endif

      <a href="{{ url('/auth/google') }}" class="btn-google">
        <i class="fab fa-google"></i> Masuk dengan Google
      </a>

      <p class="login-note">
        Gunakan email institusi Anda<br>
        (<strong>@atmi.ac.id</strong> atau <strong>@student.atmi.ac.id</strong>).
      </p>
    </div>
  </div>
  <div class="login-footer">&copy; {{ date('Y') }} Politeknik ATMI Surakarta</div>
</div>

<script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/adminlte.min.js') }}"></script>
</body>
</html>