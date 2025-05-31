<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Sistem Legalisir Online</title>

  <!-- Custom fonts for this template-->
  <link href="{{ asset('sba/css/all.min.css') }}" rel="stylesheet" type="text/css">
  <link
      href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
      rel="stylesheet">

  <!-- Custom styles for this template-->
  <link href="{{ asset('sba/css/sb-admin-2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('sba/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
  <script src="{{ asset('storage/atlantis/js/plugin/sweetalert/sweetalert.js') }}"></script>
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $ckey ? $ckey : ''}}"></script>
  <script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
  @yield('style')
</head>
<body id="page-top">
  <!-- Page Wrapper -->
  <div id="wrapper">

	  <!-- Sidebar -->
	  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

	      <!-- Sidebar - Brand -->
	      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
	          <div class="sidebar-brand-icon rotate-n-15">
	              <i class="fas fa-laugh-wink"></i>
	          </div>
	          <div class="sidebar-brand-text mx-3">Alumni</div>
	      </a>
	      <hr class="sidebar-divider my-0">

	      <li class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
	          <a class="nav-link" href="{{ route('user.dashboard') }}">
	              <i class="fas fa-fw fa-tachometer-alt"></i>
	              <span>Beranda</span></a>
	      </li>
	      <hr class="sidebar-divider">


	      <div class="sidebar-heading">
	          Permohonan Legalisir
	      </div>
	      <li class="nav-item {{ request()->routeIs('user.pengajuan') ? 'active' : '' }}">
	          <a class="nav-link" href="{{ route('user.pengajuan') }}">
	              <i class="fas fa-fw fa-chart-area"></i>
	              <span>Pengajuan</span></a>
	      </li>
	      <li class="nav-item {{ request()->routeIs('user.riwayat') ? 'active' : '' }}">
	          <a class="nav-link" href="{{ route('user.riwayat') }}">
	              <i class="fas fa-fw fa-table"></i>
	              <span>Riwayat</span></a>
	      </li>
	      <hr class="sidebar-divider d-none d-md-block">

	      <div class="text-center d-none d-md-inline">
	          <button class="rounded-circle border-0" id="sidebarToggle"></button>
	      </div>
	  </ul>
    <!-- End of Sidebar -->

	  <!-- Content Wrapper -->
	  <div id="content-wrapper" class="d-flex flex-column">
	    <!-- Main Content -->
	    <div id="content">
	      <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

	          <!-- Sidebar Toggle (Topbar) -->
	          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
	              <i class="fa fa-bars"></i>
	          </button>

	          <!-- Topbar Navbar -->
	          <ul class="navbar-nav ml-auto">

	              <!-- Nav Item - Search Dropdown (Visible Only XS) -->
	              <li class="nav-item dropdown no-arrow d-sm-none">
	                  <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
	                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                      <i class="fas fa-search fa-fw"></i>
	                  </a>
	                  <!-- Dropdown - Messages -->
	                  <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
	                      aria-labelledby="searchDropdown">
	                      <form class="form-inline mr-auto w-100 navbar-search">
	                          <div class="input-group">
	                              <input type="text" class="form-control bg-light border-0 small"
	                                  placeholder="Search for..." aria-label="Search"
	                                  aria-describedby="basic-addon2">
	                              <div class="input-group-append">
	                                  <button class="btn btn-primary" type="button">
	                                      <i class="fas fa-search fa-sm"></i>
	                                  </button>
	                              </div>
	                          </div>
	                      </form>
	                  </div>
	              </li>

	              <div class="topbar-divider d-none d-sm-block"></div>

	              <!-- Nav Item - User Information -->
	              <li class="nav-item dropdown no-arrow">
	                  <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
	                      data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                      <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ auth()->user()->alumni->nama }}</span>
	                      <img class="img-profile rounded-circle"
	                          src="{{asset('sba/img/undraw_profile.svg')}}">
	                  </a>
	                  <!-- Dropdown - User Information -->
	                  <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
	                      aria-labelledby="userDropdown">
	                      <a class="dropdown-item" href="{{ route('user.akun') }}">
	                          <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
	                          Profile
	                      </a>
	                      <div class="dropdown-divider"></div>
	                      <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
	                          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
	                          Logout
	                      </a>
	                  </div>
	              </li>

	          </ul>
	      </nav>
        <div class="container-fluid">
        	@yield('content');
        </div>
	    </div>
	    <!-- End of Main Content -->

	    <!-- Footer -->
	    <footer class="sticky-footer bg-white">
	        <div class="container my-auto">
	            <div class="copyright text-center my-auto">
	                <span>
	                    Butuh bantuan?, <a href="https://wa.me/6285156124269?text=Halo%20Admin%2C%20saya%20butuh%20bantuan" target="_blank">Hubungi Admin</a><br><br>
	                    Copyright &copy; Legalisir Online 2025
	                </span>
	                <span></span>
	            </div>
	        </div>
	    </footer>
	    <!-- End of Footer -->
	  </div>
	  <!-- End of Content Wrapper -->
  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
      <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                  <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">×</span>
                  </button>
              </div>
              <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
              <div class="modal-footer">
            		<form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">@csrf</form>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
              </div>
          </div>
      </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="{{ asset('js/jquery.min.js') }}"></script>
  <script src="{{ asset('sba/js/bootstrap.bundle.min.js') }}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{ asset('sba/js/jquery.easing.min.js') }}"></script>

  <script src="{{ asset('sba/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('sba/dataTables.bootstrap4.min.js') }}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{ asset('sba/js/sb-admin-2.min.js') }}"></script>

  <!-- Page level plugins -->
  <script src="{{ asset('sba/js/Chart.min.js') }}"></script>
  @yield('script')
</body>
</html>