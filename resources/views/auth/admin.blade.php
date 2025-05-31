<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
  <style>
    body {
      background: linear-gradient(135deg, #0d6efd, #6ea8fe);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      background-color: white;
      color: black;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.2);
      padding: 30px;
      max-width: 400px;
      width: 100%;
      z-index: 2;
    }
    label.required:after { content: ' *'; color: red;}
  </style>
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <title>Sistem Legalisir Online</title>
</head>
<body>
  <!-- Header -->
  <div class="text-center mb-4">
    <h1 class="fw-bold text-white">Sistem Legalisir Online</h1>
    <p class="lead mb-1 text-white">{{ $pengaturan['nama_fakultas']->nilai }}<br>{{ $pengaturan['nama_kampus']->nilai }}</p><br>
    <p class="text-white">Silakan login untuk melanjutkan.</p>
  </div>

  <!-- Login Card -->
  <div class="login-card">
    <h4 class="mb-3 text-center">Login Admin</h4>
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @elseif(session('logout'))
        <div class="alert alert-success">{{ session('logout') }}</div>
      @endif
    <form id="loginForm" action="{{ route('admin.login') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="nim" class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
          <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
        </div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
          <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100" id="loginBtn">Login</button>
    </form>
    <div class="mt-3 text-center">
      <a href="#" data-bs-toggle="modal" data-bs-target="#resetModal">Lupa Password?</a>
    </div>
  </div>

  <div class="modal fade" id="resetModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="resetModalLabel">Reset Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form action="">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
            </div>
            <button type="submit" class="btn btn-warning w-100">Kirim link reset</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function(){
      $("#loginForm").on("submit", function(){
        $("#loginBtn").prop("disabled", true);
      });
    })
  </script>
</body>
</html>