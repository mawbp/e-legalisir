@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Halaman Rincian Biaya</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
	  	<div class="col-md-12">
	  		<div class="card card-round">
	  			<div class="card-header">
	  				<h4 class="card-title"><strong>Rincian Biaya</strong></h4>
	  			</div>
	  			<div class="card-body">
	  				<div class="table-responsive">
	  					<table class="table table-bordered">
	  						<thead class="thead-light">
	  							<tr>
	  								<th>Jenis</th>
	  								<th>Jumlah Cetak</th>
                    <th>Biaya Satuan</th>
	  								<th>Total</th>
	  							</tr>
	  						</thead>
	  						<tbody>
	  							@foreach($permohonan as $p)
	  								<tr>
	  									<td>{{$p->dokumen->nama_dokumen}}</td>
	  									<td>{{$p->jumlah_cetak}}</td>
	  									<td>{{$p->harga_per_lembar}}</td>
                      <td>{{ $p->jumlah_cetak * $p->harga_per_lembar}}</td>
	  								</tr>
	  							@endforeach
                  <tr>
                    <td colspan="3" class="text-right">Ongkos kirim</td>
                    <td>{{$permohonan[0]->pembayaran->biaya_kurir}}</td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-right">Biaya Admin</td>
                    <td>{{$pengaturan['biaya_admin']->nilai}}</td>
                  </tr>
                  <tr>
                    <td colspan="3" class="text-right"><strong>Grand Total</strong></td>
                    <td>{{ $permohonan[0]->pembayaran->jumlah_bayar }}</td>
                  </tr>
	  						</tbody>
	  					</table>
	  				</div>
	  			</div>
	  		</div>
	  		<a href="javascript:history.back()" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>
	  	</div>
    </div>
  </div>
@endsection
@section('script')
 
@endsection