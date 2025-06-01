<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Sistem Legalisir Online</title>
	<meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
	<link rel="icon" href="{{ asset('atlantis/img/icon.ico')}}" type="image/x-icon"/>
	<!-- Fonts and icons -->
	<script src="{{ asset('atlantis/js/plugin/webfont/webfont.min.js')}}"></script>
	<script>
		WebFont.load({
			google: {"families":["Lato:300,400,700,900"]},
			custom: {"families":["Flaticon", "Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"], urls: ["{{ asset('atlantis/css/fonts.min.css') }}" ]},
			active: function() {
				sessionStorage.fonts = true;
			}
		});
	</script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js" integrity="sha512-3P8rXCuGJdNZOnUx/03c1jOTnMn3rP63nBip5gOP2qmUh5YAdVAvFZ1E+QLZZbC1rtMrQb+mah3AfYW11RUrWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="{{ asset('atlantis/js/core/popper.min.js')}}"></script>
  <script src="{{ asset('atlantis/js/core/bootstrap.min.js')}}"></script>
  <!-- DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <!-- Buttons extension -->
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.0/jszip.min.js"></script>

	<link rel="stylesheet" href="{{ asset('atlantis/css/bootstrap.min.css')}}">
	<link rel="stylesheet" href="{{ asset('atlantis/css/atlantis.min.css')}}">
	<link rel="stylesheet" href="{{ asset('css/lightbox.min.css')}}">
	<link rel="stylesheet" href="{{ asset('css/bootstrap4-toggle.min.css')}}">
	@yield('style')
</head>
<body>
	<div class="wrapper">	
		<div class="main-header">
			<!-- Logo Header -->
			<div class="logo-header" data-background-color="blue">
				
				<a href="index.html" class="logo">
					<img src="{{ asset('atlantis/img/logo.svg')}}" alt="navbar brand" class="navbar-brand">
				</a>
				<button class="navbar-toggler sidenav-toggler ml-auto" type="button" data-toggle="collapse" data-target="collapse" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon">
						<i class="icon-menu"></i>
					</span>
				</button>
				<button class="topbar-toggler more"><i class="icon-options-vertical"></i></button>
				<div class="nav-toggle">
					<button class="btn btn-toggle toggle-sidebar">
						<i class="icon-menu"></i>
					</button>
				</div>
			</div>
			<!-- End Logo Header -->

			<!-- Navbar Header -->
			<nav class="navbar navbar-header navbar-expand-lg" data-background-color="blue2"></nav>
			<!-- End Navbar -->
		</div>
		<!-- Sidebar -->
		<div class="sidebar sidebar-style-2">
			
			<div class="sidebar-wrapper scrollbar scrollbar-inner">
				<div class="sidebar-content">
					<div class="user">
						<div class="avatar-sm float-left mr-2">
							<img src="{{ asset('atlantis/img/profile.jpg')}}" alt="..." class="avatar-img rounded-circle">
						</div>
						<div class="info">
							<a data-toggle="collapse" href="#collapseExample" aria-expanded="true">
								<span>
									Admin
									<span class="user-level">Administrator</span>
									<span class="caret"></span>
								</span>
							</a>
							<div class="clearfix"></div>
							<div class="collapse in" id="collapseExample">
								<ul class="nav">
									<li><a href="#"><span class="link-collapse">Profile</span></a></li>
                  <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><span class="link-collapse">Logout</span></a></li>
								</ul>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
							</div>
						</div>
					</div>
					<ul class="nav nav-primary">
						<li id="berandaNav" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}"><i class="fas fa-chart-line"></i><p>Dashboard</p></a></li>
						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Data Menu</h4>
						</li>
						<li id="regisNav" class="nav-item {{ request()->routeIs('admin.registrasi') ? 'active' : '' }}"><a href="{{ route('admin.registrasi') }}"><i class="fas fa-user-plus"></i><p>Registrasi</p><span class="badge badge-danger">{{ $regis == '0' ? '' : $regis }}</span></a></li>
						<li id="mohonNav" class="nav-item {{ request()->routeIs('admin.pengajuan') ? 'active' : '' }}"><a href="{{ route('admin.pengajuan') }}"><i class="fas fa-stamp"></i><p>Permohonan</p><span class="badge badge-danger">{{ $mohon == '0' ? '' : $mohon }}</span></a></li>
						<li id="dokumenNav" class="nav-item {{ request()->routeIs('admin.dokumen') ? 'active' : '' }}"><a href="{{ route('admin.dokumen') }}"><i class="fas fa-file-alt"></i><p>Dokumen</p></a></li>
						<li id="alumniNav" class="nav-item {{ request()->routeIs('admin.alumni') ? 'active' : '' }}"><a href="{{ route('admin.alumni') }}"><i class="fas fa-user-graduate"></i><p>Alumni</p></a></li>
						<li id="prodiNav" class="nav-item {{ request()->routeIs('admin.prodi') ? 'active' : '' }}"><a href="{{ route('admin.prodi') }}"><i class="fas fa-book"></i><p>Prodi</p></a></li>
						<li id="laporanNav" class="nav-item {{ request()->routeIs('admin.laporan') ? 'active' : '' }}"><a href="{{ route('admin.laporan') }}"><i class="fas fa-file-export"></i><p>Riwayat</p></a></li>
						<li id="pengumumanNav" class="nav-item {{ request()->routeIs('admin.pengumuman') ?  'active' : (request()->routeIs('admin.buatpengumuman') ? 'active' : '') }}"><a href="{{ route('admin.pengumuman') }}"><i class="fas fa-bullhorn"></i><p>Pengumuman</p></a></li>
						<li class="nav-section">
							<span class="sidebar-mini-icon">
								<i class="fa fa-ellipsis-h"></i>
							</span>
							<h4 class="text-section">Settings Menu</h4>
						</li>
						<li class="nav-item {{ request()->routeIs('admin.setting.akun') ? 'active' : '' }}"><a href="{{ route('admin.setting.akun') }}"><i class="fas fa-building"></i><p>Kampus</p></a></li>
						<li class="nav-item {{ request()->routeIs('admin.setting.legal') ? 'active' : '' }}"><a href="{{ route('admin.setting.legal') }}"><i class="fas fa-certificate"></i><p>Legalisir</p></a></li>
						<li class="nav-item {{ request()->routeIs('admin.setting.biaya') ? 'active' : '' }}"><a href="{{ route('admin.setting.biaya') }}"><i class="fas fa-money-bill-wave"></i><p>Biaya</p></a></li>
						<li class="nav-item {{ request()->routeIs('admin.setting.pembayaran') ? 'active' : '' }}"><a href="{{ route('admin.setting.pembayaran') }}"><i class="fas fa-credit-card"></i><p>Pembayaran</p></a></li>
					</ul>
				</div>
			</div>
		</div>
    <div class="main-panel">
			<div class="content">
				@yield('content')
			</div>
		</div>
	</div>
  <script>
    $(document).ready(function(){
      $(".nav-item").on('click', function(){
        $(".nav-item").removeClass('active');
        $(this).addClass('active');
      });
    });
  </script>
	<!--   Core JS Files   -->
  
	<!-- jQuery UI -->
	<script src="{{ asset('atlantis/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js')}}"></script>
	<script src="{{ asset('atlantis/js/plugin/jquery-ui-touch-punch/jquery.ui.touch-punch.min.js')}}"></script>
	
	<script src="{{ asset('atlantis/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js')}}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" integrity="sha512-GsqF810cNwHMCDELTwi3YgWIBwKYQlvC1WTAJ6fk80rtB6zN3IWdpoQujBQCuOMOxXXksEWwE0k4Lrb+N87DZQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="{{ asset('atlantis/js/plugin/jquery.sparkline/jquery.sparkline.min.js')}}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/circles/0.0.6/circles.min.js" integrity="sha512-r1w3tnPCKov9Spj2bJGCQQBJ5wcJywFgL79lKMXvzBMXIPFI9xXQDmwuVs+ERh1tnL0UFT1hLrwtKh1z5/XCCQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="{{ asset('atlantis/js/plugin/sweetalert/sweetalert.js')}}"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-notify@3.1.3/bootstrap-notify.min.js"></script>
	<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
	<script src="{{ asset('js/bootstrap4-toggle.min.js')}}"></script>
	<script src="{{asset('atlantis/js/atlantis.min.js')}}"></script>
  @yield('script')
</body>
</html>