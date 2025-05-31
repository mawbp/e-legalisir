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
    <h1 class="h3 mb-0 text-gray-800">Informasi Permohonan</h1>
  </div>

  <div class="row">
    <div class="col-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary step1">Detail Pemohon</h6>
        </div>
        <div class="card-body">
	        <div class="row">
	        	<div class="col-md-12">
	        		<div class="form-group">
	        			<label for="">NIM</label>
	        			<input type="text" class="form-control" value="{{ Auth::user()->alumni->nim }}" readonly>
	        		</div>
	        		<div class="form-group">
	        			<label for="">Nama</label>
	        			<input type="text" class="form-control" value="{{ Auth::user()->alumni->nama }}" readonly>
	        		</div>
	        		<div class="form-group">
	        			<label for="">Prodi</label>
	        			<input type="text" class="form-control" value="{{ Auth::user()->alumni->prodi }}" readonly>
	        		</div>
	        	</div>
	        </div>   
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary step1">Detail Permohonan</h6>
        </div>
        <div class="card-body">
	        <div class="row">
	        	<div class="col-md-12">
	        		<div class="form-group">
	        			<label for="">Progres Permohonan</label>
	        			<input type="text" class="form-control" value="{{ $permohonan[0]->status_permohonan }}" readonly>
	        		</div>
	        		<div class="form-group">
	        			<label for="">ID Permohonan</label>
	        			<input type="text" class="form-control" value="{{$permohonan[0]->permohonan_id}}" readonly>
	        		</div>
	        	</div>
	        </div>  
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary step1">Detail Dokumen</h6>
        </div>
        <div class="card-body">
          @foreach($permohonan as $p)
            <div class="row dokumen-item">
            	<div class="col-md-6">
            		<label for="">Nama Dokumen</label>
            		<input type="text" class="form-control" value="{{ $p->dokumen->nama_dokumen }}" readonly>
            	</div>
            	<div class="col-md-6">
            		<label for="">Jumlah Cetak</label>
            		<input type="text" class="form-control" value="{{ $p->jumlah_cetak }}" readonly>
            	</div>
            </div>
          @endforeach
          <div class="d-flex justify-content-between mt-4">
            <a href="javascript:history.back()" class="btn btn-primary"></span>Kembali</a>
          </div>     
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script>
	function ajukan(mohonId) {
    let dokumenData = [];
    let validasi = true;

    $("#kirimBtn").prop("disabled", true);
    $('.dokumen-item').each(function(){
      let id = $(this).find('input[name="id"]').val();
      let nomor = $(this).find('input[name="nodok"]').val();
      if(!id || !nomor){
        Swal.fire("Harap lengkapi data dokumen", "", "warning");
        $('#kirimBtn').prop("disabled", false);
        validasi = false;
        return;
      }
      dokumenData.push({
        id: id,
        nomor: nomor,
      });
    });

    if(!validasi){
      return;
    }

		fetch("{{ route('user.ajukanulang') }}", {
			method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({mohonId: mohonId, dokumen: dokumenData})
		})
    .then(res => res.json())
    .then(data => {
      if(data.success){
        $("#kirimBtn").prop("disabled", false);
        Swal.fire(data.success, "", "success").then((result) => { window.location.href = "{{ route('user.dashboard') }}" });
      } else if(data.validation){
        $("#kirimBtn").prop("disabled", false);
        Swal.fire(data.validation, "", "warning");
      } else {
        $("#kirimBtn").prop("disabled", false);
        Swal.fire("Terjadi Kesalahan", "", "error");
      }
    });
	}
</script>
@endsection