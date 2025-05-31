@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Halaman Detail Permohonan</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
	  	<div class="col-md-8">
	  		<div class="card card-round">
	  			<div class="card-header">
	  				<h4 class="card-title"><strong>Informasi Permohonan</strong></h4>
	  			</div>
	  			<div class="card-body">
            <h3 class="card-title mt-3">Data Diri Pemohon</h3>
            <div class="form-group">
              <label for="">NIM</label>
              <input type="text" id="nim" class="form-control text-capitalize" value="{{ $permohonan[0]->user->alumni->nim }}" readonly>
            </div>
            <div class="form-group">
              <label for="">Nama</label>
              <input type="text" class="form-control text-capitalize" value="{{ $permohonan[0]->user->alumni->nama }}" readonly>
            </div>
            <div class="form-group">
              <label for="">Prodi</label>
              <input type="text" class="form-control text-capitalize" value="{{ $permohonan[0]->user->alumni->prodi }}" readonly>
            </div>
            <h3 class="card-title">Permohonan</h3>
            <div class="form-group">
              <label for="">Progres</label>
              <input type="text" class="form-control text-capitalize" value="{{ $permohonan[0]->status_permohonan }}" readonly>
            </div>
            <div class="form-group">
              <label for="">Tanggal Masuk</label>
              <input type="text" class="form-control" value="{{ $permohonan[0]->created_at }}" readonly>
            </div>
            <div class="form-group">
              <label for="">ID</label>
              <input type="text" id="permohonanId" class="form-control" value="{{ $permohonan[0]->permohonan_id }}" readonly>
            </div>
	  			</div>
	  		</div>
	  		<div class="card card-round">
	  			<div class="card-body">
	  				<h4 class="card-title"><strong>Dokumen yang diajukan</strong></h4>
	  				<div class="table-responsive">
	  					<table class="table table-bordered">
	  						<thead>
	  							<tr>
	  								<th>Jenis</th>
	  								<th>Jumlah Cetak</th>
	  							</tr>
	  						</thead>
	  						<tbody>
	  							@foreach($permohonan as $p)
	  								<tr>
	  									<td>{{$p->dokumen->nama_dokumen}}</td>
	  									<td>{{$p->jumlah_cetak}}</td>
	  								</tr>
	  							@endforeach
	  						</tbody>
	  					</table>
	  				</div>
	  			    @if($valid)
                        <div class="alert alert-success mt-3">
                            Dokumen berhasil divalidasi sistem.
                        </div>
                    @else
                        <div class="alert alert-danger mt-3">
                            Dokumen gagal divalidasi sistem.
                        </div>
                    @endif
                
                    {{-- Link ke file --}}
                    <div class="mt-3 d-flex flex-wrap">
                        @foreach($permohonan as $p)
                            <a href="{{ $dokumen[Str::slug($p->dokumen->nama_dokumen, '_')] }}" target="_blank" role="button" class="btn btn-primary mr-2 mb-2" style="color: white;">
                                Lihat File: {{ $p->dokumen->nama_dokumen }}
                            </a><br>
                        @endforeach
                    </div>
	  			</div>
	  		</div>
	  		<a href="javascript:history.back()" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>
	  	</div>
	  	<div class="col-md-4">
	  		<div class="card card-round">
	  			<div class="card-header">
		  			<h4 class="card-title"><strong>Pengiriman</strong></h4>
	  			</div>
	  			<div class="card-body">
		  			<p><strong>Metode Pengiriman:</strong> {{$permohonan[0]->pembayaran->metode_pengiriman}}</p>
            @if($permohonan[0]->order_id)
              <a href="{{ route('admin.kirim', ['id' => $permohonan[0]->permohonan_id]) }}" class="btn btn-info btn-block">Lihat Rincian Pengiriman</a>
            @endif
	  			</div>
	  		</div>
	  		<div class="card card-round">
	  			<div class="card-header">
		  			<h4 class="card-title"><strong>Pembayaran</strong></h4>
	  			</div>
	  			<div class="card-body">
		  			<p><strong>Metode Pembayaran:</strong> {{$permohonan[0]->pembayaran->metode_pembayaran ? $permohonan[0]->pembayaran->metode_pembayaran : 'Belum Dipilih'}}</p>
		  			<p><strong>Status Pembayaran:</strong>
              @if($permohonan[0]->pembayaran->status_pembayaran == 'pending')
                <span class="badge badge-warning">pending</span>
              @elseif($permohonan[0]->pembayaran->status_pembayaran == 'success')
                <span class="badge badge-success">success</span>
              @else
                <span class="badge badge-info">{{$permohonan[0]->pembayaran->status_pembayaran}}</span>
              @endif
            </p>
	  			  <a href="{{ route('admin.biaya', ['id' => $permohonan[0]->permohonan_id]) }}" class="btn btn-info btn-block">Lihat Rincian Biaya</a>
	  			  @if($permohonan[0]->pembayaran->metode_pembayaran == "Transfer Bank" && $permohonan[0]->status_permohonan == "Validasi Pembayaran")
		  			  <a href="{{ asset('uploads/' . $permohonan[0]->pembayaran->bukti_pembayaran) }}" class="btn btn-info btn-block" data-lightbox="image-1" data-title="Bukti Pembayaran">Lihat Bukti Pembayaran</a>
            @endif
	  			</div>
	  		</div>
        @if(!(($permohonan[0]->status_permohonan == "Pengiriman / Pengambilan Dokumen" && $permohonan[0]->pembayaran->metode_pengiriman == "Diambil di Kampus") || $permohonan[0]->status_permohonan == "Selesai" || $permohonan[0]->status_permohonan == "Menunggu Pembayaran" || $permohonan[0]->status_permohonan == "Dokumen Ditolak"))
		  		<div class="card card-round">
		  			<div class="card-header">
			  			<h4 class="card-title"><strong>Aksi</strong></h4>
		  			</div>
		  			<div class="card-body">
		  				@if($permohonan[0]->status_permohonan == "Validasi Dokumen")
		  					<p>Dokumen sudah valid?</p>
		  				@elseif($permohonan[0]->status_permohonan == "Validasi Pembayaran")
		  					<p>Pembayaran sudah valid?</p>
              @elseif($permohonan[0]->status_permohonan == "Proses Legalisir Dokumen")
                @if($permohonan[0]->pembayaran->metode_pengiriman == "Dikirim ke Rumah")
                  <p>Dokumen sudah siap dikirim?</p>
                @elseif($permohonan[0]->pembayaran->metode_pengiriman == "Diambil di Kampus")
                  <p>Dokumen sudah siap diambil?</p>
                @endif
		  				@endif
              @if($permohonan[0]->status_permohonan != "Pengiriman / Pengambilan Dokumen" && $permohonan[0]->status_permohonan != "Proses Legalisir Dokumen")
                <div class="form-group">
                  <label for="">Pesan admin: (Opsional)</label>
                  <textarea class="form-control" placeholder="Contoh: nomor dokumen tidak valid." id="pesanAdmin"></textarea>
                </div>
              @endif
		  				@if($permohonan[0]->status_permohonan == "Proses Legalisir Dokumen")
                @if($permohonan[0]->pembayaran->metode_pengiriman == "Dikirim ke Rumah")
                  <a href="{{ route('admin.kirim', ['id' => $permohonan[0]->permohonan_id]) }}" class="btn btn-success btn-block">Lihat data pengiriman</a>
                @elseif($permohonan[0]->pembayaran->metode_pengiriman == "Diambil di Kampus")
  			  			  <button class="btn btn-success" onclick="setuju()">Update Progres Permohonan</button>
                @endif
			  			@elseif($permohonan[0]->status_permohonan == "Pengiriman / Pengambilan Dokumen" && $permohonan[0]->pembayaran->metode_pengiriman == "Dikirim ke Rumah")
			  			  <button class="btn btn-success" onclick="trackDokumen()">Lacak Pengiriman</button>
			  			@else
			  				<div class="d-flex justify-content-around">
				  			  <button class="btn btn-danger w-50 mr-2" onclick="tolak()">Tolak</button>
				  			  <button class="btn btn-success w-50" onclick="setuju()">Setujui</button>
			  				</div>
		  				@endif
		  			</div>
		  		</div>
		  	@endif
	  	</div>
    </div>
  </div>
  <div class="modal fade" id="modalKirim" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pengiriman Dokumen</h5>
          <button class="close" type="button" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-check">
            <label>Pilih metode pengambilan</label><br>
            @if($ambil)
              @foreach($ambil as $a)
                <label class="form-radio-label">
                  <input class="form-radio-input" type="radio" name="metode_ambil" value="{{$a}}">
                  <span class="form-radio-sign">{{$a}}</span>
                </label>
              @endforeach
            @endif
          </div>
          <div class="form-group">
            <label>Catatan untuk kurir (Opsional): </label>
            <input type="text" id="catatanKurir" class="form-control" placeholder="Contoh: Hati-hati...">
            <input type="hidden" name="permohonan_id" id="mohonId">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-success" id="bayarDokumen" type="button" onclick="kirimDokumen()">Kirim</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
  <script>
  	document.addEventListener("DOMContentLoaded", function(){
	  	$("#mohonNav").addClass('active');
  		$(".nav-bread").on('click', function(){
        $(`#${$(this).data('nav')}`).addClass('active');
      });	
  	});

  	function setuju(){
      let nim = $("#nim").val();
      let permohonanId = $("#permohonanId").val();
      let pesanAdmin = $("#pesanAdmin").val();
      Swal.fire({
        title: 'Loading...',
        html: 'Sedang memproses data',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      fetch("{{ route('admin.update') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({nim: nim, permohonanId: permohonanId, pesanAdmin: pesanAdmin})
      })
      .then(res => res.json())
      .then(data => {
        if(data.success){
	        Swal.close();
	        Swal.fire({title: "Berhasil", text: data.success, icon: "success"}).then((result) => {window.location.href = "{{ route('admin.pengajuan') }}"});
        } else if(data.failed){
	        Swal.close();
          Swal.fire({title: "Gagal", text: data.failed, icon: "warning"});
        } else if(data.error) {
	        Swal.close();
          Swal.fire({title: "Gagal", text: data.error, icon: "error"});
        } else {
          alert('ERROR');
        }
      })
    }

    function tolak(){
    	let permohonanId = $("#permohonanId").val();
      let pesanAdmin = $("#pesanAdmin").val();
    	Swal.fire({
        title: 'Loading...',
        html: 'Sedang memproses data',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      fetch("{{ route('admin.tolak') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}",
          'Content-Type': "application/json",
        },
        body: JSON.stringify({permohonanId: permohonanId, pesanAdmin: pesanAdmin}),
      })
      .then(res => res.json())
      .then(data => {
        if(data.success){
        	Swal.close();
        	Swal.fire({title: "Berhasil", text: data.success, icon: "success"}).then((result) => {window.location.href = "{{ route('admin.pengajuan') }}"});
        } else {
        	Swal.close();
          console.log(data);
        }
      });
    }

    function tampilMetode(id) {
      $("#mohonId").val(id);
      $("#modalKirim").modal("show");
    }

    function trackDokumen(){
      let permohonanId = $("#permohonanId").val();
      fetch("{{ route('api.track.order') }}", {
        method: 'POST',
        headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({id: permohonanId})
      })
      .then(res => res.json())
      .then(data => {
        window.open(data.url, "_blank");
      });
    }
  </script>
  <script src="{{ asset('js/lightbox.js')}}"></script>
  <script>
    lightbox.option({
      'positionFromTop': 200,
      'maxWidth': 500,
      'maxHeight': 500,
    });
  </script>
@endsection