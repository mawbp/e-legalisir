@extends('auth.layout')

@section('content')
  <div class="login-card">
    <h4 class="mb-3 text-center">Daftar Akun</h4>
    <div id="error" class="alert alert-danger d-none"></div>
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <form id="registerForm" action="{{ route('user.register') }}" method="POST">
      @csrf
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="mb-3 d-flex flex-column">
            <label class="form-label required">NIM</label>
            <input type="text" class="form-control" name="nim" id="nim" placeholder="Masukkan nim" required>
          </div>
        </div>
        <div class="col-md-6 d-flex align-items-end">
          <button type="button" id="carinim" onclick="cariAlumni()" class="btn btn-primary mb-3">Cari Data</button>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label required">Nama</label>
            <input type="text" class="form-control" name="nama" id="nama" required readonly>
          </div>
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label required">Prodi</label>
            <input type="text" class="form-control" name="prodi" id="prodi" required readonly>
          </div>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="mb-3 ">
            <label class="form-label required">Email</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Masukkan email" required>
          </div>    
        </div>
        <div class="col-md-6">
          <div class="mb-3">
            <label class="form-label required">No. Ijazah</label>
            <input type="text" class="form-control" name="ijazah" id="ijazah" placeholder="Contoh: 12342/U/FST/XI/2017" required>
          </div>
        </div>
      </div>
      <button id="regbtn" type="submit" class="btn btn-primary w-100 d-none">Daftar</button>
    </form>
  </div>
@endsection
@section('script')
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      $("#registerForm").on('submit', function(){
        $("#regbtn").prop('disabled', true);  
      });
    });
    function cariAlumni(){
      const nim = $("#nim").val();
      const btn = $("#carinim");
      const regbtn = $("#regbtn");
      const loader = `
        <span id="spinnercari" class="spinner-border spinner-border-sm"></span>
        <span role="status">Loading...</span>
      `;
      btn.html(loader);
      btn.prop('disabled', true);
      fetch("{{ route('user.ceknim') }}", {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: JSON.stringify({
          nim: nim
        })
      })
      .then(res => res.json())
      .then(data => {
        btn.html("Cari Data")
        btn.prop('disabled', false);
        const errormsg = $("#error");
        const nama = $("#nama");
        const prodi = $("#prodi");
        if(data.error){
          regbtn.addClass('d-none');
          errormsg.removeClass('d-none');
          errormsg.html(data.error);
          nama.val("");
          prodi.val("");
        } else {
          errormsg.addClass('d-none');
          regbtn.removeClass('d-none');
          nama.val(data.nama);
          prodi.val(data.prodi);
        }
      });
    }
  </script>
@endsection
