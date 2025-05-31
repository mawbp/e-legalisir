@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Halaman Buat Pengumuman</h2>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
	  	<div class="col-md-12">
	  		<form action="{{ route('pengumuman.store') }}" id="umumForm" method="POST">
	  			@csrf
		  		<div class="card card-round">
		  			<div class="card-header">
		  				<h4 class="card-title">Pengumuman Baru</h4>
		  			</div>
		  			<div class="card-body">
		  				<div class="row">
		  					<div class="col-md-6">
		  						<div class="form-group">
		  							<label for="">Judul</label>
		  							<input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{old('judul')}}" placeholder="Masukkan judul pengumuman">
		                @error('judul')
		                  <div class="invalid-feedback">{{ $message }}</div>
		                @enderror
		  						</div>
		  					</div>
		  					<div class="col-md-6">
		  						<div class="form-group">
		  							<label for="">Durasi penayangan (hari)</label>
		  							<input type="number" name="durasi" class="form-control @error('durasi') is-invalid @enderror" value="{{old('durasi')}}" placeholder="Tentukan durasi penayanagan">
		                @error('durasi')
		                  <div class="invalid-feedback">{{ $message }}</div>
		                @enderror
		  						</div>
		  					</div>
		  					<div class="col-md-12">
			  					<div class="mb-3">
										<div class="form-group">
											<label for="">Isi pengumuman</label>
											<textarea name="editor" class="form-control @error('editor') is-invalid @enderror" id="editor" rows="10" cols="80" required>{{old('editor')}}</textarea>
											@error('editor')
			                  <div class="invalid-feedback">{{ $message }}</div>
			                @enderror
										</div>	  						
			  					</div>
		  					</div>
		  				</div>
		  			</div>
		  		</div>
		  		<div class="d-flex justify-content-between mt-4">
			  		<a href="javascript:history.back()" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Kembali</a>
	          <button role="button" type="submit" id="umumBtn" class="btn btn-primary">Buat Pengumuman</button>
	        </div>
	  		</form>
	  	</div>	  		  	
    </div>
  </div>
@endsection
@section('script')
	<script>
		document.addEventListener("DOMContentLoaded", function(){
			$("#umumForm").on("submit", function(){
				$("#umumBtn").prop("disabled", true);
				$("#umumBtn").text("Loading...");
			})
		});
		CKEDITOR.replace('editor');
	</script>
@endsection