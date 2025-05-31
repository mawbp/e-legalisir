@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Pengaturan</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengaturan konfigurasi</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <ul class="nav nav-tabs" id="settingTabs" role="tablist">
              <li class="nav-item">
                <a href="#akun" class="nav-link active konfigurasi" data-tab="akun" id="generalTab" data-toggle="tab">Akun</a>
              </li>
              <li class="nav-item">
                <a href="#legal" class="nav-link konfigurasi" data-tab="legal" id="documentTab" data-toggle="tab">Legalisir</a>
              </li>
            </ul>
          </div>
          <div class="card-body">
            <div class="tab-content" id="settingTabsContent">
              <div class="tab-pane fade show active" id="akun" role="tabpanel">
                <form action="{{ route('admin.editprofil') }}" method="POST">
                  @csrf
                  <div class="form-group">
                    <label for="">Username</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') ? old('name') : Auth::user()->name }}">
                    @error('name')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') ? old('email') : Auth::user()->email }}">
                     @error('email')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="">No. HP</label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') ? old('phone') : Auth::user()->phone }}">
                     @error('phone')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group">
                    <label for="">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Kosongi jika tidak ingin merubah">
                    <button class="btn btn-primary mt-3" type="submit">Simpan</button>
                  </div>
                </form>
              </div>
              <div class="tab-pane fade show" id="legal" role="tabpanel">
                <form action="">
                    <h5 class="font-weight-bold mt-3">Dokumen</h5>
                    <div class="form-group">
                      <label for="">Jumlah Maksimal Cetak</label>
                      <input type="number" class="form-control" value="">
                    </div>
                    <h5 class="font-weight-bold mt-4">Pembayaran</h5>
                    <div class="form-group">
                      <label for="">Bank Tujuan</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="form-group">
                      <label for="">No Rekening</label>
                      <input type="text" class="form-control">
                    </div>
                    <div class="form-group">
                      <label for="">Payment Gateway</label>
                      <select name="" id="paymentGatewaySelect" class="form-control">
                        <option value="Midtrans">Midtrans</option>
                        <option value="DOKU">DOKU</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="">Batas waktu pembayaran</label>
                      <input type="text" class="form-control">
                    </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" role="dialog" id="addmodal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="font-size: 20px;">Tambah Data Alumni</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-12">
              <label>NIM</label>
              <input type="text" class="form-control fadd" id="nim" required="">
            </div>
            <div class="form-group col-md-12">
              <label>Nama Mahasiswa</label>
              <input type="text" class="form-control fadd" id="nama" required="">
            </div>
            <div class="form-group col-md-12">
              <label>Prodi</label>
              <select class="form-control ftambah" id="prodi" required="">
                <option value="">Pilih salah satu</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Matematika">Matematika</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" onclick="tambahAlumni()">Simpan</button>
          <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" role="dialog" id="importmodal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="font-size: 20px;">Impor Data Alumni</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-6">
              <label>Template Excel</label>
              <a href="{{ route('admin.unduh') }}" class="btn btn-primary">Unduh template</a>
            </div>
            <div class="form-group col-md-12">
              <label>File</label>
              <input type="file" class="form-control-file" id="file" required="">
              <small>Masukkan file excel berisi data alumni</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success" onclick="imporAlumni()">Impor</button>
          <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" role="dialog" id="updatemodal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="font-size: 20px;">Update Data Alumni</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-12">
              <label>NIM</label>
              <input type="hidden" id="ide">
              <input type="text" class="form-control fadd" id="nime" required="">
            </div>
            <div class="form-group col-md-12">
              <label>Nama Mahasiswa</label>
              <input type="text" class="form-control fadd" id="namae" required="">
            </div>
            <div class="form-group col-md-12">
              <label>Prodi</label>
              <select class="form-control ftambah" id="prodie" required="">
                <option value="">Pilih salah satu</option>
                <option value="Sistem Informasi">Sistem Informasi</option>
                <option value="Matematika">Matematika</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" onclick="updateAlumni()">Simpan</button>
          <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
  <script>
    document.addEventListener("DOMContentLoaded", function(){
      $(".konfigurasi").on('click', function(){
        $(".konfigurasi").removeClass("active");
        $(this).addClass("active");
        let data = $(this).data('tab');
        $(".tab-pane").removeClass("active");
        $(`#${data}`).addClass("active");
      });
    });
  </script>
@endsection
