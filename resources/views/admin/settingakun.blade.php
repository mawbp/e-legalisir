@extends('admin.home')

@section('style')
  <style>
    #inputAlamat {
      cursor: pointer;
    }
  </style>
@endsection

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Pengaturan Kampus</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengaturan informasi kampus</h5>
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
            <form action="{{ route('admin.editkampus') }}" id="setakForm" method="POST">
              @csrf
              <div class="form-group">
                <label for="">Nama Kampus</label>
                <input type="text" class="form-control @error('kampus') is-invalid @enderror" name="kampus" value="{{ old('kampus') ? old('kampus') : $pengaturan['nama_kampus']->nilai }}">
                @error('kampus')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group">
                <label for="">Nama Fakultas</label>
                <input type="text" class="form-control @error('fak') is-invalid @enderror" name="fak" value="{{ old('fak') ? old('fak') : $pengaturan['nama_fakultas']->nilai }}">
                @error('fak')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group">
                <label for="">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ? old('email') : $pengaturan['email_kampus']->nilai }}">
                 @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group">
                <label for="">No. HP</label>
                <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') ? old('phone') : $pengaturan['phone_kampus']->nilai }}">
                 @error('phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group">
                <label for="">Alamat</label><br>
                <small class="text-danger">Klik kolom alamat dibawah jika ingin mengubah</small>
                <input type="text" class="form-control @error('alamat') is-invalid @enderror" name="address" value="{{ $pengaturan['alamat_kampus']->nilai ? $pengaturan['alamat_kampus']->nilai : 'Belum Diatur' }}" id="inputAlamat" data-toggle="modal" data-target="#modalAlamat" readonly>
                 @error('alamat')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="form-group">
                <button class="btn btn-primary mt-3" id="setakBtn" type="submit">Simpan Perubahan</button>
              </div>
            </form>
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
          <form action="{{ route('admin.simpanalamat') }}" id="alamatForm" method="POST">
            @csrf
            <div class="mb-3">
              <label for="provinsi" class="form-label">Provinsi</label>
              <select name="provinsi" id="provinsi" class="form-control @error('provinsi') is-invalid @enderror" required>
                <option value="" readonly selected>Loading...</option>
              </select>
              @error('provinsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label for="kabupaten" class="form-label">Kabupaten</label>
              <select name="kabupaten" id="kabupaten" class="form-control @error('kabupaten') is-invalid @enderror" required>
                <option value="" readonly selected></option>
              </select>
               @error('kabupaten') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label for="kecamatan" class="form-label">Kecamatan</label>
              <select name="kecamatan" id="kecamatan" class="form-control @error('kecamatan') is-invalid @enderror" required>
                <option value="" readonly selected></option>
              </select>
               @error('kecamatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label for="kelurahan" class="form-label">Kelurahan</label>
              <select name="kelurahan" id="kelurahan" class="form-control @error('kelurahan') is-invalid @enderror" required>
                <option value="" readonly selected></option>
              </select>
               @error('kelurahan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label for="kodepos" class="form-label">Kode Pos</label>
              <input id="kodepos" name="kodepos" type="text" class="form-control @error('kodepos') is-invalid @enderror" readonly required>
               @error('kodepos') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <label for="catatan" class="form-label">Catatan</label>
              <textarea id="catatan" name="catatan" type="text" class="form-control @error('catatan') is-invalid @enderror" placeholder="Contoh: Jl. Majapahit, No 23 / RT 01, RW 03 / Rumah cat biru" required></textarea>
               @error('catatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
              <button id="simpanAlamat" type="submit" class="btn btn-primary">Simpan alamat</button>
            </div>
          </form>
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
      const alert = document.getElementById('successAlert');
        if (alert) {
            setTimeout(() => {
                alert.classList.remove('show');
                
                setTimeout(() => {
                    alert.remove();
                }, 500); 
            }, 1500);
        }
      $("#setakForm").on("submit", function(){
        $("#setakBtn").prop("disabled", true);
        $("#setakBtn").text("Loading..");
      });

      $("#alamatForm").on("submit", function(){
        $("#simpanAlamat").prop("disabled", true);
        $("#simpanAlamat").text("Loading..");
      });

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

      });

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
  </script>
@endsection
