@extends('auth.layout')

@section('content')
  <!-- Header -->
  <div class="text-center mb-4">
    <h1 class="fw-bold text-white">Sistem Legalisir Online</h1>
    <p class="lead mb-1 text-white">{{ $pengaturan['nama_fakultas']->nilai }}<br>{{ $pengaturan['nama_kampus']->nilai }}</p><br>
    <p class="text-white">Silakan login untuk melanjutkan.</p>
  </div>

  <!-- Login Card -->
  <div class="login-card">
    <h4 class="mb-3 text-center">Login Alumni</h4>
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @elseif(session('logout'))
        <div class="alert alert-success">{{ session('logout') }}</div>
      @elseif(session('register'))
        <div class="alert alert-success">{{ session('register') }}</div>
      @endif
    <form action="{{ route('user.login') }}" id="loginForm" method="POST">
      @csrf
      <div class="mb-3">
        <label for="nim" class="form-label">NIM</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
          <input type="text" class="form-control" name="nim" placeholder="Masukkan nim" required>
        </div>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
          <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
        </div>
      </div>
      <button type="submit" id="loginBtn" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="mt-3 text-center">
      <a href="{{ route('user.lupa') }}">Lupa Password?</a>
      <br>
      <small class="mb-2">Belum punya akun? <a href="{{ route('user.regispage') }}">Daftar</a></small>
    </div>
  </div>
@endsection
@section('script')
  <script>
    document.addEventListener("DOMContentLoaded", function(){
      $("#loginForm").on("submit", function(){
        $("#loginBtn").prop("disabled", true);
      });
    })
  </script>
@endsection