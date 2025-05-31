@extends('user.home')

@section('content')
  <h1>Selamat, permohonanmu berhasil diajukan, silahkan tunggu konfirmasi dari admin untuk melakukan pembayaran.</h1>
  <p>Anda akan dialihkan ke halaman beranda dalam beberapa detik, jika tidak silahkan klik <a href="{{ route('user.dashboard') }}">link ini</a></p>
@endsection
@section('script')
  <script>
    setTimeout(() => {
      window.location.href = "{{ route('user.dashboard') }}";
    }, 4000);
  </script>
@endsection