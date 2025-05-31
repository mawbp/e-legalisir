@extends('user.home')

@section('style')
  <style>
    .form-check-label {
      background-color: #f8f9fa;
      cursor: pointer;
      transition: background-color 0.2s ease-in-out;
    }
    .form-check-input:checked + .form-check-label {
      background-color: #e9ecef;
      border-color: #0d6efd;
    }

    .courier-card:hover {
      transform: scale(1.05);
      transition: transform 0.3s ease-in-out;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }
    .courier-card.selected {
      background-color: #f0f8ff;
      transform: scale(1.1);
      transition: transform 0.3s ease-in-out;
      box-shadow: 0 10px 25px rgba(13, 110, 253, 0.4);
    }
  </style>
@endsection

@section('content')
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Biaya Tagihan</h1>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary step1">Rincian Biaya</h6>
        </div>
        <div class="card-body">
          <form id="form-permohonan">
            @csrf
            <div class=" step2">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th>Dokumen</th>
                      <th>Jumlah Cetak</th>
                      <th>Biaya Satuan</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody id="tabelBiaya">
                  	@foreach($permohonan as $p)
                  		<tr>
                  			<td>{{ $p->dokumen->nama_dokumen }}</td>
                  			<td>{{ $p->jumlah_cetak }}</td>
                  			<td>{{ number_format($p->harga_per_lembar, 0, ',', '.') }}</td>
                  			<td>{{ number_format($p->jumlah_cetak * $p->harga_per_lembar, 0, ',', '.')  }}</td>
                  		</tr>
                  	@endforeach
                  	<tr>
                  		<td colspan="3" class="text-right">Ongkos Kirim</td>
                  		<td>{{ number_format($pembayaran->biaya_kurir, 0, ',', '.') }}</td>
                  	</tr>
                  	<tr>
                  		<td colspan="3" class="text-right">Biaya Admin</td>
                  		<td>{{ number_format( $pengaturan['biaya_admin']->nilai, 0, ',', '.') }}</td>
                  	</tr>
                  	<tr>
                  		<td colspan="3" class="text-right"><strong>Total</strong></td>
                  		<td>Rp{{ number_format($pembayaran->jumlah_bayar, 2, ',', '.') }}</td>
                  	</tr>
                  </tbody>
                </table>
              </div>
              <div class="row">
              	<div class="col-md-12">
              		@if($pengaturan['opsi_pg']->nilai == 'on')
	              		<div class="form-group">
	              			<label for="">Metode Pembayaran</label>
	              			<select name="" id="metode_pembayaran" class="form-control" {{ $metode ? 'disabled' : '' }}>
	              				<option value="">Pilih salah satu</option>
	              				<option value="Payment Gateway" {{ $metode == 'Payment Gateway' ? 'selected' : '' }}>Payment Gateway</option>
	              				<option value="Transfer Bank" {{ $metode == 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
	              			</select>
	              		</div>
	              	@endif
              	</div>
              </div>
              <div class="row bank {{ $pengaturan['opsi_pg']->nilai == 'on' ? 'd-none' : '' }}">
              	<div class="col-md-6">
		              <div class="form-group">
		              	<label for="">Bank Tujuan</label>
		              	<input type="text" class="form-control" value="{{ $pengaturan['nama_bank']->nilai }}" readonly>
		              </div>
              	</div>
              	<div class="col-md-6">
              		<div class="form-group">
              			<label for="">No. Rekening</label>
              			<input type="text" class="form-control" value="{{ $pengaturan['no_rekening']->nilai }}" readonly>
              		</div>
              	</div>
              	<div class="col-md-12">
            			<label for="">Upload bukti permbayaran</label>
              		<input type="file" class="form-control" id="file">
              	</div>
              </div>
              @if($pembayaran->expired_at)
	              <div class="row mt-3">
	              	<div class="col-md-12">
	              		<div class="form-group">
				              <label for="" id="labelWaktu" data-expired="{{ $pembayaran->expired_at }}"><strong>Batas pembayaran :</strong></label>
	              		</div>
	              	</div>
	              </div>
	          @endif
              <div class="d-flex justify-content-between mt-4 tombolbayar">
                <a href="javascript:history.back()" class="btn btn-primary"></span>Kembali</a>
                @if($pembayaran->status_pembayaran == 'pending')
                    <button type="button" id="bayarBtn" class="btn btn-primary" onclick="bayarDokumen('{{ $token }}', '{{ $pengaturan['payment_gateway']->nilai }}', '{{ $permohonan[0]->permohonan_id }}', '{{ $pembayaran->id }}')">Bayar</button>
	            @endif
              </div>
            </div>
          </form>      
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script>
	document.addEventListener("DOMContentLoaded", function(){
        let el = $("#labelWaktu");
        let expiredAt = new Date(el.data('expired')).getTime();
    
        const interval = setInterval(function(){
          let now = new Date().getTime();
          let distance = expiredAt - now;
          if(distance < 0){
            clearInterval(interval);
            el.html(`<strong>Batas pembayaran :</strong> Waktu Habis`);
            $("#bayarBtn").remove();
            $(".tombolbayar").append(
                `<button type="button" id="ulangBtn" class="btn btn-primary" onclick="ulangBayar('{{ $permohonan[0]->permohonan_id }}', '{{ $pembayaran->metode_pengiriman }}', '{{ $pembayaran->biaya_kurir }}', '{{ $pembayaran->jumlah_bayar }}', '{{ $pembayaran->alamat_id }}')">Ulangi Pembayaran</button>`
            );
            return;
          }
          let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          let seconds = Math.floor((distance % (1000 * 60)) / (1000));
    
          el.html(`<strong>Batas pembayaran :</strong> ${hours} jam ${minutes} menit ${seconds} detik.`);
        }, 1000);
    
        $("#metode_pembayaran").on('change', function(e){
        	if($("#metode_pembayaran").val() == 'Transfer Bank'){
        		$(".bank").removeClass('d-none');
        	} else {
        		$(".bank").addClass('d-none');
        	}
        });
	});
	
	function ulangBayar(mohonId, metodeKirim, biayaKurir, jumlahBayar, alamatId){
	    $("#ulangBtn").prop("disabled", true).text("Loading...");
	    fetch("{{ route('api.bayarulang') }}", {
	        method: "POST",
	        headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({id: mohonId, metodeKirim: metodeKirim, biayaKurir: biayaKurir, jumlahBayar: jumlahBayar, alamatId: alamatId})
          })
          .then(res => res.json())
          .then(data => {
              if(data.success){
                window.location.href = "{{ route('user.dashboard') }}";
              } else {
                  $("#ulangBtn").prop("disabled", false).text("Bayar");
                Swal.fire("Gagal", "Terjadi kesalahan", "error");  
              }
             
          });
	}

	function bayarDokumen(token, pg, mohonId, bayarId){
		let opsi = "{{ $pengaturan['opsi_pg']->nilai }}";
        let metode = $("#metode_pembayaran").val();
        if(metode == "" && opsi == "on"){
        	Swal.fire("Pilih metode pembayaran", "", "warning");
        	return;
        } else if(metode == "" && opsi == "off"){
            metode = "Transfer Bank";
        }
    	$("#bayarBtn").prop("disabled", true);
    	$("#bayarBtn").text("Loading...");
    	
    	if(token){
    	    if(pg == 'midtrans'){
    	        snap.pay(token, {
    	          onSuccess: function(result){
    	            Swal.fire({
    	              title: "Loading..",
    	              html: 'Sedang memproses data',
    	              allowOutsideClick: false,
    	              didOpen: () => {
    	                Swal.showLoading();
    	              }
    	            });
    	            fetch("{{ route('api.updatePaymentPg') }}", {
    	              method: "POST",
    	              headers: {
    	                'Content-Type': 'application/json',
    	                'X-CSRF-TOKEN': '{{ csrf_token() }}'
    	              },
    	              body: JSON.stringify({permohonan_id: mohonId, pembayaran_id: bayarId})
    	            })
    	            .then(res => res.json())
    	            .then(data => {
    	              if(data.success){
    	                Swal.close();
    	                Swal.fire({
    	                  title: `Pembayaran anda berhasil`,
    	                  icon: 'success',
    	                  showDenyButton: false,
    	                  showCancelButton: false,
    	                  confirmButtonText: "Ok",
    	                }).then((result) => {
    	                  if(result){
    	                    window.location.href = "{{ route('user.dashboard') }}";
    	                  }
    	                });
    	              } else {
    	                Swal.close();
    	                $("#bayarBtn").prop("disabled", false);
    	                $("#bayarBtn").text("Bayar");
    	                Swal.fire("ERROR", "", "error");
    	              }
    	            });
    	          },
    	          onPending: function(result){
    	            $("#bayarBtn").prop("disabled", false);
    	            alert("Pending transaction");
    	            window.location.href = "{{ route('user.dashboard') }}";
    	          },
    	          onClose: function(){
    	            $("#bayarBtn").prop("disabled", false);
    	            Swal.fire({
    	              title: `Pembayaran anda tertunda, harap segera selesaikan`,
    	              showDenyButton: false,
    	              showCancelButton: false,
    	              confirmButtonText: "Ya",
    	            }).then((result) => {
    	              if(result){
    	                window.location.href = "{{ route('user.dashboard') }}";
    	              }
    	            });
    	          }
    	        });
    	    } else if(pg == 'doku'){
    	        loadJokulCheckout(token);
    	    }
    	}
    	
        if(metode == 'Payment Gateway'){
          fetch("{{ route('api.paymentpg') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({id: mohonId})
          })
          .then(res => res.json())
          .then(data => {
            if(data.error){
                $("#bayarBtn").prop("disabled", false);
            	$("#bayarBtn").text("Bayar");
                Swal.fire("Gagal", "Terjadi kesalahan", "error");
            }
            
          	if(data.metode == 'midtrans'){
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
    	            fetch("{{ route('api.updatePaymentPg') }}", {
    	              method: "POST",
    	              headers: {
    	                'Content-Type': 'application/json',
    	                'X-CSRF-TOKEN': '{{ csrf_token() }}'
    	              },
    	              body: JSON.stringify({permohonan_id: mohonId, pembayaran_id: bayarId})
    	            })
    	            .then(res => res.json())
    	            .then(data => {
    	              if(data.success){
    	                Swal.close();
    	                Swal.fire({
    	                  title: `Pembayaran anda berhasil`,
    	                  icon: 'success',
    	                  showDenyButton: false,
    	                  showCancelButton: false,
    	                  confirmButtonText: "Ok",
    	                }).then((result) => {
    	                  if(result){
    	                    window.location.href = "{{ route('user.dashboard') }}";
    	                  }
    	                });
    	              } else {
    	                Swal.close();
    	                $("#bayarBtn").prop("disabled", false);
    	                $("#bayarBtn").text("Bayar");
    	                Swal.fire("ERROR", "", "error");
    	              }
    	            });
    	          },
    	          onPending: function(result){
    	            $("#bayarBtn").prop("disabled", false);
    	            alert("Pending transaction");
    	            window.location.href = "{{ route('user.dashboard') }}";
    	          },
    	          onClose: function(){
    	            $("#bayarBtn").prop("disabled", false);
    	            Swal.fire({
    	              title: `Pembayaran anda tertunda, harap segera selesaikan`,
    	              showDenyButton: false,
    	              showCancelButton: false,
    	              confirmButtonText: "Ya",
    	            }).then((result) => {
    	              if(result){
    	                window.location.href = "{{ route('user.dashboard') }}";
    	              }
    	            });
    	          }
    	        })
          	} else if(data.metode == 'doku'){
          		if(data.url){
    	      		const url = data.url;
    	      		loadJokulCheckout(url);
          		} else {
          			$("#bayarBtn").prop("disabled", false);
          			$("#bayarBtn").text("Bayar");
          			Swal.fire('Terjadi Kesalahan', "", "error");
          		}
          	}
          });
        } else if(metode == 'Transfer Bank'){
          let formData = new FormData();
    		  formData.append('id', mohonId);
    		  formData.append('file', $("#file")[0].files[0]);
    		  Swal.fire({
    		    title: "Loading..",
    		    html: 'Sedang memproses data',
    		    allowOutsideClick: false,
    		    didOpen: () => {
    		      Swal.showLoading();
    		    }
    		  });
    		  fetch("{{ route('api.paymenttf') }}", {
    		    method: 'POST',
    		    headers: {
    		      'X-CSRF-TOKEN': '{{ csrf_token() }}'
    		    },
    		    body: formData
    		  })
    		  .then(res => res.json())
    		  .then(data => {
    		    Swal.close();
    		    if(data.success){
    		      Swal.fire({
    		        title: data.success,
    		        icon: "success",
    		        showDenyButton: false,
    		        showCancelButton: false,
    		        confirmButtonText: "Ok",
    		      }).then((result) => {
    		        if(result){
    		          window.location.href = "{{ route('user.dashboard') }}";
    		        }
    		      });
    		    } else if(data.failed){
    		      Swal.fire(data.failed, "", "error");
    		      $("#bayarBtn").prop("disabled", false);
        			$("#bayarBtn").text("Bayar");
    		    } else if(data.validation){
    		      Swal.fire(data.validation, "", "error");
    		      $("#bayarBtn").prop("disabled", false);
        			$("#bayarBtn").text("Bayar");
    		    }
    		  });
        }
  }
</script>
@endsection