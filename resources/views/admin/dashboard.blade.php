@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-md-center flex-column flex-md-row justify-content-between">
        <div>
          <h1 class="text-white pb-2 fw-bold">Dashboard</h1>
          <h5 class="text-white pb-2">Halaman Dashboard Admin</h5>
        </div>
        <div class="ml-md-auto py-2 py-md-0">
        	<form action="" class="form-inline ml-auto">
            <div class="form-group mx-sm-2">
              <label for="start_date" class="mr-1 text-white">Mulai :</label>
              <input type="date" class="form-control form-control-sm" name="start_date">
            </div>
            <div class="form-group mx-sm-2">
              <label for="end_date" class="mr-1 text-white">Akhir :</label>
              <input type="date" class="form-control form-control-sm" name="end_date">
            </div>
            <button type="submit" id="filterBtn" class="btn btn-sm btn-primary ml-sm-2">Filter</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary ml-sm-2">Reset</a>
          </form>
	        <!-- <form action="" class="row">
		    		<div class="col">
		    			<label for="start_date" class="text-white">Tanggal Mulai</label>
		    			<input type="date" name="start_date" id="startDate" class="form-control" required>
		    		</div>
		    		<div class="col">
		    			<label for="end_date" class="text-white">Tanggal Akhir</label>
		    			<input type="date" name="end_date" id="endDate" class="form-control" required>
		    		</div>
		    		<div class="col-auto">
		    			<label for="" class="d-block text-white">&nbsp;</label>
		    			<button class="btn btn-success" type="submit">Filter</button>
		    		</div>
	    		<div class="col-auto">
	    			<label for="" class="d-block text-white">&nbsp;</label>
	    			<a href="{{ route('admin.dashboard') }}" class="btn btn-danger">Reset Filter</a>
	    		</div>
	        </form> -->
				</div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
			<div class="col-sm-6 col-md-3">
				<div class="card card-stats card-round">
					<div class="card-body ">
						<div class="row align-items-center">
							<div class="col-icon">
								<div class="icon-big text-center icon-primary bubble-shadow-small">
									<i class="flaticon-users"></i>
								</div>
							</div>
							<div class="col col-stats ml-3 ml-sm-0">
								<div class="numbers">
									<p class="card-category">Semua Permohonan</p>
									<h4 class="card-title">{{ $total }}</h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card card-stats card-round">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-icon">
								<div class="icon-big text-center icon-info bubble-shadow-small">
									<i class="flaticon-interface-6"></i>
								</div>
							</div>
							<div class="col col-stats ml-3 ml-sm-0">
								<div class="numbers">
									<p class="card-category">Permohonan Baru</p>
									<h4 class="card-title">{{ $baru }}</h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card card-stats card-round">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-icon">
								<div class="icon-big text-center icon-success bubble-shadow-small">
									<i class="flaticon-graph"></i>
								</div>
							</div>
							<div class="col col-stats ml-3 ml-sm-0">
								<div class="numbers">
									<p class="card-category">Sedang Diproses</p>
									<h4 class="card-title">{{ $diproses }}</h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6 col-md-3">
				<div class="card card-stats card-round">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-icon">
								<div class="icon-big text-center icon-secondary bubble-shadow-small">
									<i class="flaticon-success"></i>
								</div>
							</div>
							<div class="col col-stats ml-3 ml-sm-0">
								<div class="numbers">
									<p class="card-category">Sudah Selesai</p>
									<h4 class="card-title">{{ $selesai }}</h4>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
  	</div>
  	<div class="row">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">
						<div class="card-head-row">
							<div class="card-title">Jumlah Permohonan Berdasarkan Prodi {{ $unit }}</div>
						</div>
					</div>
					<div class="card-body">
						<div class="chart-container" style="min-height: 375px">
							<canvas id="statisticsChart"></canvas>
						</div>
						<div id="myChartLegend"></div>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="card card-secondary">
					<div class="card-header">
						<div class="card-title">Uang Masuk {{ $unit }}</div>
						<div class="card-category">{{ $rentang }}</div>
					</div>
					<div class="card-body pb-0">
						<div class="mb-4 mt-2">
							<h1>Rp{{ $total_uang }}</h1>
						</div>
						<div class="pull-in">
							<canvas id="dailySalesChart"></canvas>
						</div>
					</div>
				</div>
				<div class="card card-primary bg-primary-gradient">
					<div class="card-body">
						<h4 class="mb-1 fw-bold">Tasks Progress</h4>
						<div id="task-complete" class="chart-circle mt-4 mb-3"></div>
					</div>
				</div>
			</div>
		</div>
  </div>
@endsection

@section('script')
	<script>
	    const labels = @json($label_prodi);
	    const data = @json($data_prodi);
	    const datasets = [];
	    
	    function getRandomColor(){
	        return {
	          r: Math.floor(Math.random() * 200),
	          g: Math.floor(Math.random() * 200),
	          b: Math.floor(Math.random() * 200),
	        };
	    }
	    
	    Object.entries(data).forEach(([prodi, val]) => {
	        const rgb = getRandomColor();
	        const full = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 1)`;
	        const trans = `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.4)`;
	        datasets.push({
	            label: prodi,
	            data: val,
	            pointBackgroundColor: full,
				pointRadius: 0,
				backgroundColor: trans,
				legendColor: full,
				fill: true,
				borderWidth: 2,
				borderColor: full,
	        })
	    })
	    
		Circles.create({
			id:           'task-complete',
			radius:       50,
			value:        {!! $persen !!},
			maxValue:     100,
			width:        5,
			text:         function(value){return value + '%';},
			colors:       ['#36a3f7', '#fff'],
			duration:     400,
			wrpClass:     'circles-wrp',
			textClass:    'circles-text',
			styleWrapper: true,
			styleText:    true
		})
		//Chart
		var ctx = document.getElementById('statisticsChart').getContext('2d');

		var statisticsChart = new Chart(ctx, {
			type: 'line',
			data: {
				labels: labels,
				datasets: datasets,
			},
			options : {
				responsive: true, 
				maintainAspectRatio: false,
				legend: {
					display: false
				},
				tooltips: {
					bodySpacing: 4,
					mode:"nearest",
					intersect: 0,
					position:"nearest",
					xPadding:10,
					yPadding:10,
					caretPadding:10
				},
				layout:{
					padding:{left:5,right:5,top:15,bottom:15}
				},
				scales: {
					yAxes: [{
						ticks: {
							fontStyle: "500",
							beginAtZero: false,
							maxTicksLimit: 5,
							padding: 10
						},
						gridLines: {
							drawTicks: false,
							display: false
						}
					}],
					xAxes: [{
						gridLines: {
							zeroLineColor: "transparent"
						},
						ticks: {
							padding: 10,
							fontStyle: "500"
						}
					}]
				}, 
				legendCallback: function(chart) { 
					var text = []; 
					text.push('<ul class="' + chart.id + '-legend html-legend">'); 
					for (var i = 0; i < chart.data.datasets.length; i++) { 
						text.push('<li><span style="background-color:' + chart.data.datasets[i].legendColor + '"></span>'); 
						if (chart.data.datasets[i].label) { 
							text.push(chart.data.datasets[i].label); 
						} 
						text.push('</li>'); 
					} 
					text.push('</ul>'); 
					return text.join(''); 
				}  
			}
		});

		var myLegendContainer = document.getElementById("myChartLegend");

		// generate HTML legend
		myLegendContainer.innerHTML = statisticsChart.generateLegend();

		// bind onClick event to all LI-tags of the legend
		var legendItems = myLegendContainer.getElementsByTagName('li');
		for (var i = 0; i < legendItems.length; i += 1) {
			legendItems[i].addEventListener("click", legendClickCallback, false);
		}

		var dailySalesChart = document.getElementById('dailySalesChart').getContext('2d');

		var myDailySalesChart = new Chart(dailySalesChart, {
			type: 'line',
			data: {
				labels: {!! json_encode($labels_bayar ?? []) !!},
				datasets:[ {
					label: "Pembayaran", 
					fill: !0, 
					backgroundColor: "rgba(255,255,255,0.2)", 
					borderColor: "#fff", 
					borderCapStyle: "butt", 
					borderDash: [], 
					borderDashOffset: 0, 
					pointBorderColor: "#fff", 
					pointBackgroundColor: "#fff", 
					pointBorderWidth: 1, 
					pointHoverRadius: 5, 
					pointHoverBackgroundColor: "#fff", 
					pointHoverBorderColor: "#fff", 
					pointHoverBorderWidth: 1, 
					pointRadius: 1, 
					pointHitRadius: 5, 
					data: {!! json_encode($totals_bayar ?? []) !!}
				}]
			},
			options : {
				maintainAspectRatio:!1, legend: {
					display: !1
				}
				, animation: {
					easing: "easeInOutBack"
				}
				, scales: {
					yAxes:[ {
						display:!1, ticks: {
							fontColor: "rgba(0,0,0,0.5)", fontStyle: "bold", beginAtZero: !0, maxTicksLimit: 10, padding: 0
						}
						, gridLines: {
							drawTicks: !1, display: !1
						}
					}
					], xAxes:[ {
						display:!1, gridLines: {
							zeroLineColor: "transparent"
						}
						, ticks: {
							padding: -20, fontColor: "rgba(255,255,255,0.2)", fontStyle: "bold"
						}
					}
					]
				}
			}
		});
	</script>
@endsection

