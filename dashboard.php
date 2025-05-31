<div class="content">
	<div class="container-fluid">
			<!-- Statistik Ringkas -->
			<div class="row">
					<div class="col-md-3">
							<div class="card card-stats card-round">
									<div class="card-body">
											<div class="row">
													<div class="col-3">
															<div class="icon-big text-center">
																	<i class="flaticon-file text-primary"></i>
															</div>
													</div>
													<div class="col-9 col-stats">
															<div class="numbers">
																	<p class="card-category">Hari Ini</p>
																	<h4 class="card-title">12 Permohonan</h4>
															</div>
													</div>
											</div>
									</div>
							</div>
					</div>
					<div class="col-md-3">
							<div class="card card-stats card-round">
									<div class="card-body">
											<div class="row">
													<div class="col-3">
															<div class="icon-big text-center">
																	<i class="flaticon-calendar text-success"></i>
															</div>
													</div>
													<div class="col-9 col-stats">
															<div class="numbers">
																	<p class="card-category">Bulan Ini</p>
																	<h4 class="card-title">98 Permohonan</h4>
															</div>
													</div>
											</div>
									</div>
							</div>
					</div>
					<div class="col-md-3">
							<div class="card card-stats card-round">
									<div class="card-body">
											<div class="row">
													<div class="col-3">
															<div class="icon-big text-center">
																	<i class="flaticon-interface-5 text-warning"></i>
															</div>
													</div>
													<div class="col-9 col-stats">
															<div class="numbers">
																	<p class="card-category">Menunggu Verifikasi</p>
																	<h4 class="card-title">7 Permohonan</h4>
															</div>
													</div>
											</div>
									</div>
							</div>
					</div>
					<div class="col-md-3">
							<div class="card card-stats card-round">
									<div class="card-body">
											<div class="row">
													<div class="col-3">
															<div class="icon-big text-center">
																	<i class="flaticon-success text-info"></i>
															</div>
													</div>
													<div class="col-9 col-stats">
															<div class="numbers">
																	<p class="card-category">Selesai</p>
																	<h4 class="card-title">35 Permohonan</h4>
															</div>
													</div>
											</div>
									</div>
							</div>
					</div>
			</div>

			<!-- Grafik Status Permohonan -->
			<div class="row">
					<div class="col-md-6">
							<div class="card">
									<div class="card-header">
											<div class="card-title">Grafik Permohonan (7 Hari Terakhir)</div>
									</div>
									<div class="card-body">
											<canvas id="permohonanChart"></canvas>
									</div>
							</div>
					</div>

					<div class="col-md-6">
							<div class="card">
									<div class="card-header">
											<div class="card-title">Status Permohonan</div>
									</div>
									<div class="card-body">
											<canvas id="statusChart"></canvas>
									</div>
							</div>
					</div>
			</div>

			<!-- Tabel Permohonan Terbaru -->
			<div class="card">
					<div class="card-header">
							<div class="card-title">Permohonan Terbaru</div>
					</div>
					<div class="card-body">
							<table class="table table-hover">
									<thead>
											<tr>
													<th>Nama</th>
													<th>NIM</th>
													<th>Dokumen</th>
													<th>Status</th>
													<th>Tanggal</th>
											</tr>
									</thead>
									<tbody>
											<tr>
													<td>Rani Permata</td>
													<td>12345678</td>
													<td>Ijazah</td>
													<td><span class="badge badge-warning">Menunggu</span></td>
													<td>18 April 2025</td>
											</tr>
											<tr>
													<td>Agus Saputra</td>
													<td>87654321</td>
													<td>Transkrip</td>
													<td><span class="badge badge-success">Selesai</span></td>
													<td>17 April 2025</td>
											</tr>
											<tr>
													<td>Sinta Lestari</td>
													<td>11223344</td>
													<td>Ijazah & Transkrip</td>
													<td><span class="badge badge-info">Diproses</span></td>
													<td>16 April 2025</td>
											</tr>
									</tbody>
							</table>
					</div>
			</div>
	</div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
		const ctx = document.getElementById('permohonanChart').getContext('2d');
		const permohonanChart = new Chart(ctx, {
				type: 'line',
				data: {
						labels: ['12 Apr', '13 Apr', '14 Apr', '15 Apr', '16 Apr', '17 Apr', '18 Apr'],
						datasets: [{
								label: 'Permohonan',
								data: [5, 6, 8, 4, 7, 9, 12],
								borderColor: 'blue',
								fill: false
						}]
				}
		});

		const ctx2 = document.getElementById('statusChart').getContext('2d');
		const statusChart = new Chart(ctx2, {
				type: 'pie',
				data: {
						labels: ['Menunggu', 'Diproses', 'Ditolak', 'Selesai'],
						datasets: [{
								data: [7, 10, 3, 35],
								backgroundColor: ['orange', 'skyblue', 'red', 'green']
						}]
				}
		});
</script>