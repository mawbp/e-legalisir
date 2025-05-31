<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('js/datatables.min.js') }}"></script>
  <script src="{{ asset('storage/atlantis/js/plugin/sweetalert/sweetalert.js')}}"></script>
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-kTxuYAbfVfHNi6H9"></script>
  @yield('style')
  <title>Sistem Legalisir</title>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Dashboard Mahasiswa</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">Beranda</a></li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ request()->routeIs('user.pengajuan') || request()->routeIs('user.cekstatus') ? 'active' : '' }}" href="#" id="akunDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Legalisir
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow rounded" aria-labelledby="akunDropdown">
              <li><a class="dropdown-item" href="{{ route('user.pengajuan') }}">Pengajuan</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="{{ route('user.cekstatus') }}">Riwayat</a></li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle {{ request()->routeIs('user.akun') ? 'active' : '' }}" id="akunDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              Akun
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow rounded" aria-labelledby="akunDropdown">
              <li><a class="dropdown-item" href="{{ route('user.akun') }}">Edit Profil</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a href="#" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
              <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">@csrf</form>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  @yield('content')

  <!-- Footer -->
  <footer class="text-center py-4 bg-primary text-white mt-5">
    <p class="mb-0">&copy; 2025 FSAINTEK UNIPDU - Legalisir Online</p>
  </footer>

  <script src="js/bootstrap.bundle.min.js"></script>
  @yield('script')
</body>
</html>