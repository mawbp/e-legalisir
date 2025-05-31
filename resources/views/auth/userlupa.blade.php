@extends('auth.layout')

@section('content')
  <!-- Header -->
  <div class="text-center mb-4">
    <h1 class="fw-bold text-white">Lupa Password?</h1>
    <p class="lead mb-1 text-white">Silahkan masukkan NIM, link untuk reset password akan dikirim ke email yang terdaftar pada akun anda</p><br>
  </div>

  <!-- Login Card -->
  <div class="login-card">
    <h4 class="mb-3 text-center">Reset Password Anda</h4>
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @elseif(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
    <form action="{{ route('user.reset') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="nim" class="form-label">NIM</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
          <input type="text" class="form-control" name="nim" placeholder="Masukkan nim" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Kirim Email Reset</button>
    </form>
  </div>
@endsection