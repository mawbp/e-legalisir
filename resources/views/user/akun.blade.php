@extends('user.home')

@section('content')
	<h1 class="h3 mb-4 text-gray-800">Profil Saya</h1>
	<div class="row">
		<div class="col-lg-4">
			<div class="card shadow mb-4 text-center">
				<div class="card-body">
					<img src="{{ asset('sba/img/undraw_profile.svg') }}" class="img-fluid rounded-circle mb-3" style="width: 120px;" alt="">
					<h5 class="card-title">{{ auth()->user()->alumni->nama }}</h5>
					<p class="text-muted">{{ Auth::user()->email }}</p>
				</div>
			</div>
		</div>
		<div class="col-lg-8">
			<div class="card shadow mb-4">
				<div class="card-header py-3">
					<h6 class="m-0 font-weight-bold text-primary">Edit Profil</h6>
					@if(session('warning'))
						<br><div class="alert alert-warning alert-dismissable fade show" role="alert">{{ session('warning') }}<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button></div>
					@endif
				</div>
				<div class="card-body">
					<form action="">
						<div class="form-group">
							<label for="">Nama</label>
							<input type="hidden" id="userid" class="form-control" value="{{ Auth::user()->id }}">
							<input type="text" id="name" class="form-control" value="{{ Auth::user()->alumni->nama }}">
						</div>
						<div class="form-group">
							<label for="">Alamat Email</label>
							<input type="email" id="email" class="form-control" value="{{ Auth::user()->email }}">
						</div>
						<div class="form-group">
							<label for="">No. HP</label>
							<input type="text" id="phone" class="form-control" value="{{ Auth::user()->phone }}">
						</div>
						<div class="form-group">
							<label for="">Password</label>
							<input type="password" id="password" class="form-control" placeholder="Kosongi jika tidak ingin merubah">
						</div>
						<button type="button" id="simpanBtn" class="btn btn-primary" onclick="updateProfil()">Simpan Perubahan</button>
					</form>
					<h5 class="mt-4">Daftar Alamat</h5>
					@foreach(Auth::user()->alamat as $alamat)
						<div class="card my-2 p-3 position-relative">
							@if(Auth::user()->alamat_id !== $alamat->id)
								<form action="{{ route('user.alamatutama') }}" method="POST">
									@csrf
									<input type="hidden" value="{{ $alamat->id }}" name="alamat_id">
									<button type="submit" class="btn btn-warning btn-sm position-absolute" style="top: 10px; right: 10px;">Jadikan alamat utama</button>
								</form>
							@else
								<span class="badge badge-success position-absolute" style="top: 10px; right: 10px;">Alamat Utama</span>
							@endif
							<form action="">
								<div class="form-group">
									<label for="">Label Alamat</label>
									<input type="text" name="label" class="form-control" value="{{ $alamat->label }}" readonly>
								</div>
								<div class="form-group">
									<label for="">Alamat</label>
									<textarea class="form-control" readonly>{{ $alamat->kelurahan }}, Kec. {{ $alamat->kecamatan }}, {{ $alamat->kabupaten }}, {{ $alamat->provinsi }}, {{ $alamat->kode_pos }}. 
Catatan: {{ $alamat->catatan ? $alamat->catatan : '-'}}</textarea>
								</div>
								<button class="btn btn-danger btn-sm mt-2" onclick="hapusAlamat('{{ $alamat->id }}')">
									Hapus Alamat
								</button>
							</form>
						</div>
					@endforeach
					<div class="card my-3 p-3">
						<div class="form-group">
							<label for="">Label Alamat</label>
							<input type="text" id="labelBaru" class="form-control" placeholder="Contoh: Alamat Rumah">
						</div>
						<button id="cobaKlik" class="btn btn-primary" data-toggle="modal" data-target="#modalAlamat">Tambahkan Alamat</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="modalAlamat" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAlamatLabel">Isi Alamat Pengiriman</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="provinsi" class="form-label">Provinsi</label>
            <select name="provinsi" id="provinsi" class="form-control" required>
              <option value="" disabled selected>Loading...</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="kabupaten" class="form-label">Kabupaten</label>
            <select name="kabupaten" id="kabupaten" class="form-control" required>
              <option value="" disabled selected></option>
            </select>
          </div>
          <div class="mb-3">
            <label for="kecamatan" class="form-label">Kecamatan</label>
            <select name="kecamatan" id="kecamatan" class="form-control" required>
              <option value="" disabled selected></option>
            </select>
          </div>
          <div class="mb-3">
            <label for="kelurahan" class="form-label">Kelurahan</label>
            <select name="kelurahan" id="kelurahan" class="form-control" required>
              <option value="" disabled selected></option>
            </select>
          </div>
          <div class="mb-3">
            <label for="kodepos" class="form-label">Kode Pos</label>
            <input id="kodepos" name="kodepos" type="text" class="form-control" disabled required>
          </div>
          <div class="mb-3">
            <label for="kodepos" class="form-label">Catatan</label>
            <textarea id="catatan" name="kodepos" type="text" class="form-control" placeholder="Contoh: Jl. Majapahit, No 23 / RT 01, RW 03 / Rumah cat biru" required></textarea>
          </div>
          <div class="mb-3">
            <button id="simpanAlamat" type="button" class="btn btn-primary" onclick="simpanAlamat()">Simpan</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
		<script>
		const routes = {
	    provinsi: "{{ route('api.provinsi') }}",
	    kabupaten: "{{ route('api.kabupaten', ':provinsiId') }}",
	    kecamatan: "{{ route('api.kecamatan', ':kabupatenId') }}",
	    kelurahan: "{{ route('api.kelurahan', ':kecamatanId') }}",
	  };

		document.addEventListener("DOMContentLoaded", function(){
		  fetch("{{ route('api.provinsi') }}")
		  .then(res => res.json())
		  .then(data => {
		    let options = '<option value="" disabled selected>Pilih Provinsi</option>';
		    const provinsi = data.data;
		    provinsi.forEach(function(prov) {
		      options += `<option value="${prov.name}" data-code="${prov.code}">${prov.name}</option>`
		    });
		    $('#provinsi').html(options);
		  })
		  .catch(err => {
		  	alert("Timeout");
		  	console.error(err);
		  })

			$('#provinsi').on('change', function(){
	      let provinsiId = $(this).find('option:selected').data('code');
	      let url = routes.kabupaten.replace(':provinsiId', provinsiId);
	      $('#kabupaten').prop('disabled', true).html('<option>Loading...</option>');
	      $.getJSON(url, function(data){
	        let options = '<option value="" disabled selected>Pilih Kabupaten/Kota</option>';
	        const kabupaten = data.data;
	        kabupaten.forEach(function(kab) {
	          options += `<option value="${kab.name}" data-code="${kab.code}">${kab.name}</option>`
	        });
	        $('#kabupaten').html(options).prop('disabled', false);
	      });
	    });

	    $('#kabupaten').on('change', function(){
	      let kabupatenId = $(this).find('option:selected').data('code');
	      let url = routes.kecamatan.replace(':kabupatenId', kabupatenId);
	      $('#kecamatan').prop('disabled', true).html('<option>Loading...</option>');
	      $.getJSON(url, function(data){
	        let options = '<option value="" disabled selected>Pilih Kecamatan</option>';
	        const kecamatan = data.data;
	        kecamatan.forEach(function(kec) {
	          options += `<option value="${kec.name}" data-code="${kec.code}">${kec.name}</option>`
	        });
	        $('#kecamatan').html(options).prop('disabled', false);
	      });
	    });

	    $('#kecamatan').on('change', function(){
	      let kecamatanId = $(this).find('option:selected').data('code');
	      let url = routes.kelurahan.replace(':kecamatanId', kecamatanId);
	      $('#kelurahan').prop('disabled', true).html('<option>Loading...</option>');
	      $.getJSON(url, function(data){
	        let options = '<option value="" disabled selected>Pilih Kelurahan</option>';
	        const kelurahan = data.data;
	        kelurahan.forEach(function(kel) {
	          options += `<option value="${kel.name}" data-postal="${kel.postal_code}" data-code="${kel.code}">${kel.name}</option>`
	        });
	        $('#kelurahan').html(options).prop('disabled', false);
	      });
	    });

	    $("#kelurahan").on('change', function(){
	      let kodepos = $(this).find('option:selected').data('postal');
	      $("#kodepos").val(kodepos);
	    })
		});

		function updateProfil(){
			const id = $("#userid").val();
			const email = $("#email").val();
			const phone = $("#phone").val();
			const password = $("#password").val();
			$("#simpanBtn").prop("disabled", true);
			fetch("{{ route('user.updateprofil') }}", {
				method: "POST",
				headers: {
					'X-CSRF-TOKEN': "{{ csrf_token() }}",
					'Content-Type': "application/json"
				},
				body: JSON.stringify({email: email, phone: phone, password: password, id: id})
			})
			.then(res => res.json())
			.then(data => {
				console.log(data);
				if(data.errors){
					$("#simpanBtn").prop("disabled", false);
					Swal.fire(data.errors, "", "warning");
				} else if(data.success){
					$("#simpanBtn").prop("disabled", false);
					Swal.fire({
	      		title: data.success,
	      		icon: "success",
	      		allowOutsideClick: false,
	      		showCancelButton: false,
	      		confirmButtonText: "OK"
	      	})
	      	.then((btn) => {
	      		if(btn){
			        window.location.href = "{{ route('user.akun') }}";
	      		}
	      	});
				} else {
					Swal.fire("Gagal", "Gagal memperbarui profil", "error");
				}
			});
		}	

	  function simpanAlamat(){
	  	const label = $("#labelBaru").val();
	    const phone = $("#phone").val();
	    const kodepos = $("#kodepos").val();
	    const kelurahan = $("#kelurahan").val();
	    const kecamatan = $("#kecamatan").val();
	    const kabupaten = $("#kabupaten").val();
	    const provinsi = $("#provinsi").val();
	    const catatan = $("#catatan").val();
	    const kelurahan_code = $("#kelurahan").find('option:selected').data('code');
	    const kecamatan_code = $("#kecamatan").find('option:selected').data('code');
	    const kabupaten_code = $("#kabupaten").find('option:selected').data('code');
	    const provinsi_code = $("#provinsi").find('option:selected').data('code');

	    const data = {
	      phone: phone,
	      kodepos: kodepos,
	      kelurahan: kelurahan,
	      kecamatan: kecamatan,
	      kabupaten: kabupaten,
	      provinsi: provinsi,
	      label: label,
	      catatan: catatan,
	      kelurahan_code: kelurahan_code,
	      kecamatan_code: kecamatan_code,
	      kabupaten_code: kabupaten_code,
	      provinsi_code: provinsi_code
	    }

	    $("#simpanAlamat").prop("disabled", true).text("Loading...");
	    fetch("{{ route('user.alamat') }}", {
	      method: 'POST',
	      headers: {
	        'Content-Type': 'application/json',
	        'X-CSRF-TOKEN': '{{ csrf_token() }}'
	      },
	      body: JSON.stringify(data)
	    })
	    .then(res => res.json())
	    .then(data => {
	        $("#simpanAlamat").prop("disabled", false).text("Simpan");
	      if(data.validation){
	        Swal.fire(data.validation, "", "warning");
	      } else if(data.error){
	        Swal.fire(data.error, "", "error");
	      } else {
	      	Swal.fire({
	      		title: "Alamat berhasil disimpan",
	      		icon: "success",
	      		allowOutsideClick: false,
	      		showCancelButton: false,
	      		confirmButtonText: "OK"
	      	})
	      	.then((btn) => {
	      		if(btn){
			        window.location.href = "{{ route('user.akun') }}";
	      		}
	      	})
	      }
	    });
	  }

	  function hapusAlamat(id){
	  	fetch("{{ route('user.hapusAlamat') }}",{
	  		method: "POST",
	  		headers: {
	  			'Content-Type': 'application/json',
	  			'X-CSRF-TOKEN': '{{ csrf_token() }}'
	  		},
	  		body: JSON.stringify({id: id})
	  	})
	  	.then(res => res.json())
	  	.then(data => {
	  		if(data.success){
	  			Swal.fire({
	      		title: "Alamat berhasil dihapus",
	      		icon: "success",
	      		allowOutsideClick: false,
	      		showCancelButton: false,
	      		confirmButtonText: "OK"
	      	})
	      	.then((btn) => {
	      		if(btn){
			        window.location.href = "{{ route('user.akun') }}";
	      		};
	      	});
	  		} else {
	  			console.log(data);
	  		}
	  	});
	  }
	</script>
@endsection