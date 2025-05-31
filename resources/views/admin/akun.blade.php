@extends('admin.dashboard')

@section('style')
	<style>
		.profile-card {
			max-width: 700px;
			margin: auto;
			padding: 20px;
			background: #fff;
			border-radius: 15px;
			box-shadow: 0 4px 8px rgba(0,0,0,0.5);
		}
		.profile-img {
			width: 120px;
			height: 120px;
			border-radius: 50%;
			object-fit: cover;
		}
	</style>
@endsection

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Profil</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengelolaan profil</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">
              <button class="btn btn-primary" data-toggle="modal" data-target="#addmodal" data-backdrop="static" data-keyboard="false">Tambah data</button>
            </div>
          </div>
          <div class="card-body">
      			<div class="profile-card">
							<img src="" alt="">
							<h3 class="mt-3">{{ auth()->user()->name }}</h3>
							<p class="text-muted"></p>
							<p>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Enim fugit nobis, tenetur sit accusantium obcaecati rerum harum molestiae, qui. Tenetur, minus cumque eum velit unde dolores, iure ducimus consectetur quis.</p>
							<div class="d-flex justify-content-around my-3">
								<div>
									<h5>120</h5>
									<p class="text-muted">Post</p>
								</div>
								<div>
									<h5>250</h5>
									<p class="text-muted">Follower</p>
								</div>
								<div>
									<h5>180</h5>
									<p class="text-muted">Following</p>
								</div>
							</div>
							<form action="">
								<div class="row">
									<div class="mb-3 col-md-6">
										<label for="" class="form-label">Username</label>
										<input type="text" class="form-control" placeholder="Username" value="{{ auth()->user()->name }}" readonly>
									</div>
									<div class="mb-3 col-md-6">
										<label for="" class="form-label">Email</label>
										<input type="text" class="form-control" id="emailku" placeholder="Username" value="{{ auth()->user()->email }}" readonly> 
									</div>
									<div class="mb-3 col-md-6">
										<label for="" class="form-label">Nomor HP</label>
										<input type="text" class="form-control" id="phoneku" placeholder="Username" value="{{ auth()->user()->phone }}" readonly>
									</div>
								</div>
							</form>
							<div class="row">
								<div class="col-md-6"><button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#updatemodal">Edit Profil</button></div>
								<div class="col-md-6"><button class="btn btn-outline-secondary w-100">Ubah Password</button></div>
							</div>
						</div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
	<script>
		$("#email, #phone").on('input', function(){
			let id = $(this).attr('id');
			$(`.error-${id}`).text("");
		})

		function updateProfil(){
			const id = $("#id").val();
			const email = $("#email").val();
			const phone = $("#phone").val();
			fetch("{{ route('user.updateprofil') }}", {
				method: "POST",
				headers: {
					'X-CSRF-TOKEN': "{{ csrf_token() }}",
					'Content-Type': "application/json"
				},
				body: JSON.stringify({email: email, phone: phone, id: id})
			})
			.then(res => res.json())
			.then(data => {
				console.log(data);
				if(data.errors){
					console.log(data.errors);
					for(let field in data.errors){
						$(`.error-${field}`).text(data.errors[field][0]);
					}
				} else if(data.success){
					$("#updatemodal").modal("hide");
					Swal.fire("Berhasil", data.success, "success");
					$("#emailku").val(data.email);
					$("#phoneku").val(data.phone);
				} else {
					Swal.fire("Gagal", "Gagal memperbarui profil", "error");
				}
			});
		}
	</script>
@endsection