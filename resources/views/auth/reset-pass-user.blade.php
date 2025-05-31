@extends('auth.layout')

@section('content')
  <!-- Header -->
  <div class="text-center mb-4">
    <h1 class="fw-bold text-white">Halaman Reset Password</h1>
    <p class="lead mb-1 text-white">Masukkan password baru anda</p><br>
  </div>

  <!-- Login Card -->
  <div class="login-card">
    <h4 class="mb-3 text-center">Reset Password Anda</h4>
      @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
      @elseif(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
    <form action="{{ route('user.ganti') }}" method="POST">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      <input type="hidden" name="nim" value="{{ $nim }}">
      <div class="mb-3"> 
        <label for="nim" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
          <input type="password" class="form-control" name="password" placeholder="Masukkan password baru" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Reset Password</button>
    </form>
  </div>
@endsection