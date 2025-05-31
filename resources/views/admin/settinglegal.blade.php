@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Pengaturan Legalisir</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengaturan legalisir dokumen</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            @if(session('success'))
              <div class="alert alert-success" id="successAlert">{{ session('success') }}</div>
            @endif
            <form action="{{ route('admin.editlegal') }}" id="setlegForm" method="POST">
              @csrf
              <div class="row">
                <div class="form-group">
                  <label for="">Jumlah maksimal cetak</label>
                  <input type="text" class="form-control @error('cetak') is-invalid @enderror" name="cetak" value="{{ old('cetak') ? old('cetak') : $pengaturan['maksimal_cetak']->nilai }}">
                  @error('cetak')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="form-group">
                <button class="btn btn-primary mt-3" id="setlegBtn" type="submit">Simpan Perubahan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
  <script>
    document.addEventListener("DOMContentLoaded", function(){
      $("#setlegForm").on("submit", function(){
        $("#setlegBtn").prop("disabled", true);
        $("#setlegBtn").text("Loading..");
      });
      const alert = document.getElementById('successAlert');
        if (alert) {
            setTimeout(() => {
                alert.classList.remove('show');
                
                setTimeout(() => {
                    alert.remove();
                }, 500); 
            }, 1500);
        }
    });
  </script>
@endsection
