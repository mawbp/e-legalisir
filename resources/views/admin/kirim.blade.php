@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Halaman Rincian Pengiriman</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
	  	<div class="col-md-6">
	  		<div class="card card-round">
	  			<div class="card-header">
	  				<h4 class="card-title">Data Pengirim</h4>
	  			</div>
	  			<div class="card-body">
	  				<div class="row">
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">Nama</label>
	  							<input type="text" class="form-control" value="{{ $pengaturan['nama_kampus']->nilai }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">No HP</label>
	  							<input type="text" class="form-control" value="{{ $pengaturan['phone_kampus']->nilai }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">Alamat</label>
	  							<input type="text" class="form-control" value="{{ $pengaturan['alamat_kampus']->nilai }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">Catatan (alamat)</label>
	  							<textarea name="" id="" class="form-control" readonly placeholder="{{ !$pengaturan['alamat_kampus']->nilai ? 'Tidak ada' : '' }}">{{ $pengaturan['catatan_alamat_kampus']->nilai }}</textarea>
	  						</div>
	  					</div>
	  				</div>
	  			</div>
	  		</div>
	  	</div>
	  	<div class="col-md-6">
	  		<div class="card card-round">
	  			<div class="card-header">
	  				<h4 class="card-title">Data Penerima</h4>
	  			</div>
	  			<div class="card-body">
	  				<div class="row">
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">Nama</label>
	  							<input type="text" class="form-control" value="{{ $permohonan[0]->user->alumni->nama }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">No HP</label>
	  							<input type="text" class="form-control" value="{{ $permohonan[0]->user->phone }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">Alamat</label>
	  							<input type="text" class="form-control" value="{{ $alamat_user }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">Catatan (alamat)</label>
	  							<textarea name="" id="" class="form-control" placeholder="{{ !$catatan_user ? 'Tidak ada' : '' }}" readonly>{{ $catatan_user }}</textarea>
	  						</div>
	  					</div>
	  				</div>
	  			</div>
	  		</div>
	  	</div>
	  	<div class="col-md-12">
	  		<div class="card card-round">
	  			<div class="card-header">
	  				<h4 class="card-title">Data Pengiriman</h4>
	  			</div>
	  			<div class="card-body">
	  				<div class="row">
	  					<div class="col-md-4">
	  						<div class="form-group">
	  							<label for="">Order ID</label>
	  							<input type="text" class="form-control" value="{{ $permohonan[0]->order_id ? $permohonan[0]->order_id : 'Belum Tersedia' }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-4">
	  						<div class="form-group">
	  							<label for="">Tracking ID</label>
	  							<input type="text" class="form-control" value="{{ $permohonan[0]->tracking_id ? $permohonan[0]->tracking_id : 'Belum Tersedia' }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-4">
	  						<div class="form-group">
	  							<label for="">Waybill ID</label>
	  							<input type="text" class="form-control" value="{{ $permohonan[0]->waybill_id ? $permohonan[0]->waybill_id : 'Belum Tersedia' }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-6">
	  						<div class="form-group">
	  							<label for="">Kurir</label>
	  							<input type="text" class="form-control" value="{{ $permohonan[0]->kurir }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-6">
	  						<div class="form-group">
	  							<label for="">Tipe Kurir</label>
	  							<input type="text" class="form-control" value="{{ $permohonan[0]->tipe_kurir }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-12">
	  						<div class="form-group">
	  							<label for="">Biaya pengiriman</label>
	  							<input type="text" class="form-control" value="{{ $permohonan[0]->pembayaran->biaya_kurir }}" readonly>
	  						</div>
	  					</div>
	  					<div class="col-md-6">
	  						<div class="form-group">
	  							<label for="">Metode Pengambilan Paket</label>
	  							<select name="" id="metodePengambilan" class="form-control" {{ $permohonan[0]->metode_pengambilan_terpilih ? 'disabled' : '' }}>
	  								<option value="">Pilih salah satu</option>
	  								@if($ambil)
				              @foreach($ambil as $a)
				                <option value="{{$a}}" {{ $permohonan[0]->metode_pengambilan_terpilih == $a ? 'selected' : '' }}>{{$a}}</option>
				              @endforeach
				            @endif
	  							</select>
	  						</div>
	  					</div>
	  					<div class="col-md-6">
	  						<div class="form-group">
	  							<label for="">Catatan pengiriman (Opsional)</label>
	  							<textarea name="" id="catatanKurir" class="form-control" placeholder="{{ !$permohonan[0]->catatan_pengiriman ? 'Tidak ada' : '' }}" {{ $permohonan[0]->catatan_pengiriman ? 'readonly' : '' }}>{{ $permohonan[0]->catatan_pengiriman }}</textarea>
	  						</div>
	  					</div>
	  				</div>
	  			</div>
	  		</div>
	  		<div class="d-flex justify-content-between mt-4">
		  		<a href="javascript:history.back()" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>
		  		@if(!$permohonan[0]->order_id)
	          <button type="button" id="kurirBtn" class="btn btn-primary" onclick="kirimDokumen('{{ $permohonan[0]->permohonan_id }}')">Mulai pengiriman</button>
	        @endif
        </div>
	  	</div>	  		  	
    </div>
  </div>
@endsection
@section('script')
	<script>
		function bayarKurir(mohonId) {
			let metodeAmbil = $("#metodePengambilan").val();
      let catatan = $("#catatanKurir").val();
      console.log(metodeAmbil);
      if(!metodeAmbil){
        Swal.fire("Harap pilih metode pengambilan paket", "", "warning");
        return;
      }

			$("#kurirBtn").prop("disabled", true);
			fetch("{{ route('admin.bayarkurir') }}", {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-CSRF-TOKEN': '{{ csrf_token() }}'
				},
				body: JSON.stringify({mohonId: mohonId})
			})
			.then(res => res.json())
			.then(data => {
				if(data.snapToken){
					snap.pay(data.snapToken, {
			      onSuccess: function(result){
			        Swal.fire({
			          title: "Loading..",
			          html: 'Sedang memproses data',
			          allowOutsideClick: false,
			          didOpen: () => {
			            Swal.showLoading();
			          }
			        });
			        fetch("{{ route('api.order') }}", {
			          method: "POST",
			          headers: {
			            'Content-Type': 'application/json',
			            'X-CSRF-TOKEN': '{{ csrf_token() }}'
			          },
			          body: JSON.stringify({permohonan_id: mohonId, metodeAmbil: metodeAmbil, catatan: catatan})
			        })
			        .then(res => res.json())
			        .then(data => {
			          if(data.response.success){
			          	const data_pengiriman = { 
			          		permohonanId: mohonId, 
			          		metodeAmbil: metodeAmbil, 
			          		orderId: data.response.id, 
			          		trackingId: data.response.courier.tracking_id, 
			          		waybillId: data.response.courier.waybill_id, 
			          		catatan: data.response.note 
			          	};
			          	fetch("{{ route('api.store.tracking') }}", {
			          		method: 'POST',
			          		headers: {
			          			'Content-Type': 'application/json',
			          			'X-CSRF-TOKEN': '{{ csrf_token() }}'
			          		},
			          		body: JSON.stringify(data_pengiriman)
			          	})
			          	.then(res => res.json())
			          	.then(data => {
			          		if(data.success){
			          			Swal.close();
			          			Swal.fire(data.success, "", "success").then((result) => { window.location.href = "{{ route('admin.pengajuan') }}" })
			          		} else {
			          			Swal.fire("Terjadi kesalahan", "", "error");
			          		}
			          	});
		            } else {
		              Swal.close();
		              Swal.fire("Gagal", "Data pengiriman gagal dikirimkan ke kurir.", "error");
		            }
			        });
			      },
			      onPending: function(result){
			      	Swal.close();
			      	$("#kurirBtn").prop("disabled", false);
			      },
			      onClose: function(){
			        Swal.fire({
			          title: `Pembayaran anda tertunda, harap segera selesaikan`,
			          showDenyButton: false,
			          showCancelButton: false,
			          confirmButtonText: "Ya",
			        }).then((result) => {
			          if(result){
			            window.location.href = "{{ route('admin.pengajuan') }}";
			          }
			        });
			      }
			    });
				} else {
					Swal.close();
					Swal.fire("Terjadi kesalahan", "", "error");
					$("#kurirBtn").prop("disabled", false);
				}
			})
		}

		function kirimDokumen(permohonanId){
      let metodeAmbil = $("#metodePengambilan").val();
      let catatan = $("#catatanKurir").val();
      if(!metodeAmbil){
        Swal.fire("Harap pilih metode pengambilan paket", "", "warning");
        return;
      }

      Swal.fire({
        title: 'Yakin memulai proses pengiriman?',
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
      }).then((btn) => {
        if(btn.isConfirmed){
        	$("#kurirBtn").prop("disabled", true);
		      $("#kurirBtn").text("Loading...");
          Swal.fire({
            title: 'Loading...',
            html: 'Sedang memproses data',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          const data = {permohonanId: permohonanId, metodeAmbil: metodeAmbil, catatan: catatan};
          fetch("{{ route('api.order') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
          })
          .then(res => res.json())
          .then(data => {
            if(data.response.success){
                console.log(data);
              const tracking = {
                trackingId: data.response.courier.tracking_id,
                orderId: data.response.id,
                waybillId: data.response.courier.waybill_id,
                catatan: data.response.note,
                permohonanId: permohonanId,
                metodeAmbil: metodeAmbil,
              };

              fetch("{{ route('api.store.tracking') }}", {
                method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(tracking)
              })
              .then(res => res.json())
              .then(data => {
                if(data.success){
                  Swal.close();
                  $("#kurirBtn").prop("disabled", false);
						      $("#kurirBtn").text("Mulai Pengiriman");
                  Swal.fire("Berhasil", data.success, "success").then((result) => {window.location.href = "{{ route('admin.pengajuan') }}"});
                } else {
                  Swal.close();
                  $("#kurirBtn").prop("disabled", false);
						      $("#kurirBtn").text("Mulai Pengiriman");
                  Swal.fire("Gagal", "Data pengiriman gagal dikirimkan ke kurir.", "error");
                }
              }); 
            } else {
              Swal.close();
              $("#kurirBtn").prop("disabled", false);
				      $("#kurirBtn").text("Mulai Pengiriman");
              Swal.fire("Gagal", "Data pengiriman gagal dikirimkan ke kurir.", "error");
            }
          });
        }
      });
    }
	</script>
@endsection