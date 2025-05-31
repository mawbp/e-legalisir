@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Pengaturan Biaya</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengaturan biaya legalisir</h5>
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
              <div class="alert alert-success alert-dismissible fade show" id="successAlert" role="alert">{{ session('success') }}</div>
            @endif
            <form action="{{ route('admin.editbiaya') }}" id="setbiaForm" method="POST">
              @csrf
              <div class="row">
                @foreach ($dokumen as $item)
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="biaya_{{ $item->id }}">Biaya cetak (per lembar) untuk {{ $item->nama_dokumen }}</label>
                            <input type="text" class="form-control @error('biaya.'.$item->id) is-invalid @enderror" name="biaya[{{ $item->id }}]" value="{{ old('biaya.'.$item->id, $item->harga_per_lembar) }}">
                            @error('biaya.'.$item->id)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach    
              </div>
              <div class="form-group">
                <label for="">Biaya admin</label>
                <input type="text" class="form-control @error('admin') is-invalid @enderror" name="admin" value="{{ old('admin') ? old('admin') : $pengaturan['biaya_admin']->nilai }}">
                 @error('admin')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group">
                <button class="btn btn-primary mt-3" id="setbiaBtn" type="submit">Simpan Perubahan</button>
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
        $("#setbiaForm").on("submit", function(){
            $("#setbiaBtn").prop("disabled", true);
            $("#setbiaBtn").text("Loading..");
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
