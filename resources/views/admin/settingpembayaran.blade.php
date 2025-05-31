@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Pengaturan Pembayaran</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengaturan pembayaran</h5>
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
            <form action="{{ route('admin.editpembayaran') }}" id="setbayForm" method="POST">
              @csrf
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="">Bank Tujuan Transfer</label>
                  <select name="bank" class="form-control @error('bank') is-invalid @enderror">
                    <option value="">Pilih salah satu</option>
                    <option value="BCA" {{ $pengaturan['nama_bank']->nilai == 'BCA' ? 'selected' : '' }}>BCA</option>
                    <option value="BNI" {{ $pengaturan['nama_bank']->nilai == 'BNI' ? 'selected' : '' }}>BNI</option>
                    <option value="BRI" {{ $pengaturan['nama_bank']->nilai == 'BRI' ? 'selected' : '' }}>BRI</option>
                    <option value="BSI" {{ $pengaturan['nama_bank']->nilai == 'BSI' ? 'selected' : '' }}>BSI</option>
                    <option value="Mandiri" {{ $pengaturan['nama_bank']->nilai == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                  </select>
                  <!-- <input type="text" class="form-control @error('bank') is-invalid @enderror" name="bank" value="{{ old('bank') ? old('bank') : $pengaturan['nama_bank']->nilai }}"> -->
                  @error('bank')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-group col-md-6">
                  <label for="">No. Rekening</label>
                  <input type="text" class="form-control @error('norek') is-invalid @enderror" name="norek" value="{{ old('norek') ? old('norek') : $pengaturan['no_rekening']->nilai }}">
                  @error('norek')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-12">
                  <div class="d-flex justify-content-between align-items-center">
                    <label for="">Payment Gateway</label>
                    <input type="hidden" name="switch" value="0">
                    <input name="switch" id="switch" type="checkbox" value="1" data-toggle="toggle" data-on="Aktif" data-off="Tidak Aktif" data-onstyle="success" data-offstyle="danger" data-size="sm" {{ old('switch') ? 'checked' : ($pengaturan['opsi_pg']->nilai == 'on' ? 'checked' : '') }}>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6 pg d-none">
                  <label for="">Pilihan Payment Gateway</label>
                  <select name="pg" id="pg" class="form-control @error('pg') is-invalid @enderror">
                    <option value="">Pilih Salah Satu</option>
                    <option value="midtrans" {{ $pengaturan["payment_gateway"]->nilai == 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                    <option value="doku" {{ $pengaturan["payment_gateway"]->nilai == 'doku' ? 'selected' : '' }}>Doku</option>
                  </select>
                  @error('pg')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="row d-none" id="midtransConfig">
                <div class="form-group col-md-6">
                  <label for="">Server Key</label>
                  <input type="text" class="form-control @error('skey_midtrans') is-invalid @enderror" name="skey_midtrans" value="{{ old('skey_midtrans', $pengaturan['skey_midtrans']->nilai) }}" placeholder="Masukkan Server Key Midtrans">
                  @error('skey_midtrans')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-group col-md-6">
                  <label for="">Client Key</label>
                  <input type="text" class="form-control @error('ckey_midtrans') is-invalid @enderror"" name="ckey_midtrans" value="{{ old('ckey_midtrans', $pengaturan['ckey_midtrans']->nilai) }}" placeholder="Masukkan Client Key Midtrans">
                  @error('ckey_midtrans')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="row d-none" id="dokuConfig">
                <div class="form-group col-md-6">
                  <label for="">Secret Key</label>
                  <input type="text" class="form-control @error('skey_doku') is-invalid @enderror"" name="skey_doku" value="{{ old('skey_doku', $pengaturan['skey_doku']->nilai) }}" placeholder="Masukkan Secret Key Doku">
                  @error('skey_doku')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
                <div class="form-group col-md-6">
                  <label for="">Client ID</label>
                  <input type="text" class="form-control @error('ckey_doku') is-invalid @enderror"" name="ckey_doku" value="{{ old('ckey_doku',  $pengaturan['ckey_doku']->nilai) }}" placeholder="Masukkan Client ID Doku">
                  @error('ckey_doku')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>
              <div class="form-group">
                <button class="btn btn-primary mt-3" type="submit" id="setbayBtn">Simpan Perubahan</button>
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
        const alert = document.getElementById('successAlert');
        if (alert) {
            setTimeout(() => {
                alert.classList.remove('show');
                
                setTimeout(() => {
                    alert.remove();
                }, 500); 
            }, 1500);
        }
      $("#setbayForm").on("submit", function(){
        $("#setbayBtn").prop("disabled", true);
        $("#setbayBtn").text("Loading..");
      });

       $("#switch").on("change", function(){
        if($(this).prop('checked')){
            $(".pg").removeClass('d-none')
            if($("#pg").val() == 'midtrans'){
                $("#midtransConfig").removeClass('d-none');
            } else if($("#pg").val() == 'doku'){
                $("#dokuConfig").removeClass('d-none');
            } else {
                $("#midtransConfig").addClass('d-none');
                $("#dokuConfig").addClass('d-none');
            }
        } else {
            $(".pg").addClass('d-none');
            $("#midtransConfig").addClass('d-none');
            $("#dokuConfig").addClass('d-none');
        }
    });

    if($("#switch").prop('checked')){
        $(".pg").removeClass('d-none');
        if($("#pg").val() == 'midtrans'){
            $("#midtransConfig").removeClass('d-none');
        } else if($("#pg").val() == 'doku'){
            $("#dokuConfig").removeClass('d-none');
        } else {
            $("#midtransConfig").addClass('d-none');
            $("#dokuConfig").addClass('d-none');
        }
    } else {
        $(".pg").addClass('d-none');
        $("#midtransConfig").addClass('d-none');
        $("#dokuConfig").addClass('d-none');
    }

      $("#pg").on("change", function(){
        if($(this).val() == 'midtrans'){
          $("#midtransConfig").removeClass('d-none');
          $("#dokuConfig").addClass('d-none');
        } else if($(this).val() == 'doku'){
          $("#midtransConfig").addClass('d-none');
          $("#dokuConfig").removeClass('d-none');
        } else {
            $("#midtransConfig").addClass('d-none');
            $("#dokuConfig").addClass('d-none');
        }
      })
    });
  </script>
@endsection
