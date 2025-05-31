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
    <h1 class="h3 mb-0 text-gray-800">Pengajuan Permohonan</h1>
  </div>

  <div class="row">
    <div class="col-12">
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary step1">Formulir Permohonan</h6>
          <h6 class="m-0 font-weight-bold text-primary step2 d-none">Rincian Biaya</h6>
        </div>
        <div class="card-body">
          <form id="form-permohonan">
            @csrf
            <div class="step1">
              <div id="dokumenContainer">
                <div class="form-row align-items-end dokumen-item">
                  <div class="form-group col-md-4">
                    <label for="namadok[]"><strong>Dokumen yang diajukan: </strong></label>
                    <select id="namadok" class="form-control namadok" name="namadok[]">
                      <option value="">Pilih Salah Satu</option>
                      @foreach($dokumen as $dok)
                        <option value="{{ $dok->nama_dokumen }}">{{ $dok->nama_dokumen }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="">Jumlah Cetak</label>
                    <input type="number" class="form-control" placeholder="Jumlah cetak" name="cetak[]" min="5" max="{{ $pengaturan['maksimal_cetak']->nilai }}">
                  </div>
                  <div class="form-group col-md-3">
                    <button type="button" class="btn btn-primary mt-4" onclick="tambahDokumen()">Tambah Dokumen</button>
                  </div>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="metodePengiriman">Metode Pengiriman</label>
                  <select id="metodePengiriman" class="form-control" name="metode_pengiriman">
                    <option value="">Pilih Salah Satu</option>
                    <option value="Diambil di Kampus">Diambil di Kampus</option>
                    <option value="Dikirim ke Rumah">Dikirim ke Rumah</option>
                  </select>
                </div>
              </div>
              <div id="antar" style="display: none">
                <div class="col-md-12 mb-3 btn-container">
                  <div class="d-flex justify-content-between align-items-center">
                    <div id="alamatmu">
                      <label for="" class="font-weight-bold mb-1">Alamat : </label>
                      @if($alamat)
                          <p id="alamat_terpilih" id="{{ $alamat->kelurahan }}" data-pos="{{ $alamat->kode_pos }}" data-id="{{ $alamat->id }}" data-alamat="{{ $alamat->kelurahan }}, {{ $alamat->kecamatan }}, {{ $alamat->kabupaten }}, {{ $alamat->provinsi }}, {{ $alamat->kode_pos }}">{{ $alamat->kelurahan }}, {{ $alamat->kecamatan }}, {{ $alamat->kabupaten }}, {{ $alamat->provinsi }}, {{ $alamat->kode_pos }}</p>
                      @else
                        <p class="text-danger mb-0">Alamat belum ditambahkan.</p>
                      @endif
                    </div>
                  </div>
                  @if($alamat)
                    <button id="editAlamatBtn" type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modalUbahAlamat">Ubah Alamat</button>
                  @endif
                </div>        
                <div class="col-md-12 mb-3">
                  <h4 class="mb-4">Daftar Layanan Kurir</h4><button id="kurirBtn" type="button" class="btn btn-sm btn-outline-primary" onclick="pilihAlamat()">Cek kurir</button>
                    <div class="row" id="kurir"></div>
                </div>
              </div>
              <div class="d-flex justify-content-end mt-4">
                <button type="button" id="biayaBtn" class="btn btn-primary" onclick="step2()"></span>Lihat Rincian Biaya</button>
              </div>
            </div>
            <div class="step2 d-none">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="thead-light">
                    <tr>
                      <th>Dokumen</th>
                      <th>Jumlah Cetak</th>
                      <th>Biaya Satuan</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody id="tabelBiaya"></tbody>
                </table>
              </div>
              <div class="d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-primary" onclick="step1()"></span>Kembali</button>
                <button type="button" id="kirimBtn" class="btn btn-primary" onclick="kirim()"></span>Kirim Permohonan</button>
              </div>
            </div>
          </form>      
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalAlamat" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAlamatLabel">Isi Alamat Pengiriman</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
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
            <label for="kodepos" class="form-label">No HP</label>
            <input id="phone" name="phone" type="text" class="form-control" value="{{ Auth::user()->phone }}" required>
          </div>
          <div class="mb-3">
            <button id="simpanAlamat" type="submit" class="btn btn-primary" onclick="simpanAlamat()">Simpan</button>
          </div>
          <div class="alert alert-success" role="alert" id="notifikasi" style="display: none; position:fixed; top:20px; right:20px; z-index:1050"></div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modalUbahAlamat" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Ubah Alamat Pengiriman</h5>
          <button class="close" type="button" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          @foreach($alamatAll as $alamat)
            <div class="form-check">
              <input id="{{ $alamat->kode_pos }}" type="radio" name="alamatmu" class="form-check-input" value="{{ $alamat->id }}" {{ $alamat_id == $alamat->id ? 'checked' : '' }}>
              <label for="{{ $alamat->kode_pos }}" class="form-check-label">{{ $alamat->kelurahan }}, {{ $alamat->kecamatan }}, {{ $alamat->kabupaten }}, {{ $alamat->provinsi }}, {{ $alamat->kode_pos }}, Indonesia</label>
            </div>
          @endforeach
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
          <button class="btn btn-success" id="ubahAlamatBtn" type="button" onclick="ubahAlamat()">Simpan</button>
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

  let selected = [];

  document.addEventListener('DOMContentLoaded', function(){
    // Form Alamat
    $(document).ready(function(){
      $("#metodePengiriman").change(function(){
        if($(this).val() === 'Dikirim ke Rumah'){
          $("#antar").show();
        } else {
          $("#antar").hide();
        }
      });

      $('#provinsi').on('change', function(){
        let provinsiId = $(this).find('option:selected').data('code');
        let url = routes.kabupaten.replace(':provinsiId', provinsiId);
        selectedAlamat.provinsi = $("#provinsi option:selected").text();
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
        selectedAlamat.kabupaten = $("#kabupaten option:selected").text();
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
        selectedAlamat.kecamatan = $("#kecamatan option:selected").text();
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
        selectedAlamat.kelurahan = $("#kelurahan option:selected").text();
        selectedAlamat.kodepos = $("#kodepos").val();
      })
    });
  });

  function kirim(){
      let kirimbtn = $("#kirimBtn");
      let biayakirim = $(".courier-card.selected").data('biaya') ? $(".courier-card.selected").data('biaya') : 0;
      let totalBiaya = $("#totalBiaya").data('biaya');
      let kurir = $(".courier-card.selected").data('kurir');
      let tipe_kurir = $(".courier-card.selected").data('tipe');
      let metodekirim = $('#metodePengiriman').val();
      let metodeambil = $(".courier-card.selected").data('metode');
      let alamat = $("#alamat_terpilih").data('id') ? $("#alamat_terpilih").data('id') : '';
      let dokumenData = [];
      kirimbtn.prop("disabled", true);
      kirimbtn.text("Loading...");
      if(metodekirim == "Dikirim ke Rumah"){
        if(!kurir){
          Swal.fire("Silahkan pilih kurir untuk pengiriman", "", "warning");
          kirimbtn.prop("disabled", false);
          kirimbtn.text("Kirim Permohonan");
          return;
        }
      }

      $(".dokumen-item").each(function(){
        let namadok = $(this).find('select[name="namadok[]"]').val();
        let cetak = $(this).find('input[name="cetak[]"]').val();
        if(!namadok || !cetak){
          Swal.fire("Harap lengkapi data dokumen", "", "warning");
          kirimbtn.prop("disabled", false);
          kirimbtn.text("Kirim Permohonan");
          return;
        }
        dokumenData.push({
          namadok: namadok,
          cetak: cetak,
        });
      });
      
      $(".hargadok").each(function(index){
          let harga = $(this).text().trim();
          dokumenData[index].harga = harga;
      });
      
      fetch("{{ route('user.mohon') }}", {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({dokumen: dokumenData, alamat: alamat, biayakirim: biayakirim, jumlahbayar: totalBiaya, kurir: kurir, tipe_kurir: tipe_kurir, metodeambil: metodeambil, metodekirim: metodekirim})
      })
      .then(res => res.json())
      .then(data => {
        if(data.success){
          kirimbtn.text("Kirim Permohonan");
          window.location.href = "{{ route('user.mohon.success') }}";
        } else if(data.validation) {
          kirimbtn.prop("disabled", false);
          Swal.fire("Gagal", data.validation, "warning")
        }
      });
    }

  
  function step2(){
    $("#biayaBtn").text("Loading...");
    $("#biayaBtn").prop("disabled", true);
    let html = '';
    let validasi = true
    let totalBiayaDokumen = 0;
    let metodekirim = $("#metodePengiriman").val();
    let kurir = $(".courier-card.selected").data('kurir');
    fetch("{{ route('user.cekharga') }}", {
      method: "GET",
    })
      .then(res => res.json())
      .then(data => {
        if(data){
          $(".dokumen-item").each(function(){
            let namadok = $(this).find('select[name="namadok[]"]').val();
            let nomor = $(this).find('input[name="nomor[]"]').val();
            let cetak = $(this).find('input[name="cetak[]"]').val();
            if(!namadok || !cetak){
              validasi = false;
              return;
            }
            let dok = data.find(i => i.nama == namadok);
            let harga = dok.harga;
            totalBiayaDokumen += cetak * harga;
            html += `
              <tr>
                <td>${namadok}</td>
                <td>${cetak}</td>
                <td class="hargadok">${harga}</td>
                <td>${cetak * harga}</td>
              </tr>
            `;
          });

          if(metodekirim == "Dikirim ke Rumah"){
            if(!kurir){
              Swal.fire("Silahkan pilih kurir untuk pengiriman", "", "warning");
              $("#biayaBtn").text("Lihat Rincian Biaya");
              $("#biayaBtn").prop("disabled", false);
              return;
            }
          }

          if(!validasi || !metodekirim){
            Swal.fire("Harap lengkapi form permohonan", "", "warning");
            $("#biayaBtn").text("Lihat Rincian Biaya");
            $("#biayaBtn").prop("disabled", false);
          } else {
            $("#biayaBtn").text("Lihat Rincian Biaya");
            $("#biayaBtn").prop("disabled", false);
            let biayaKirim = $(".courier-card.selected").data('biaya') ? $(".courier-card.selected").data('biaya') : 0;
            let biayaAdmin = {{ $pengaturan['biaya_admin']->nilai }};
            let totalBiaya = totalBiayaDokumen + biayaKirim + biayaAdmin;
            html += `
              <tr>
                <td colspan="3" class="text-right">Ongkos kirim</td>
                <td>${biayaKirim}</td>
              </tr>
              <tr>
                <td colspan="3" class="text-right">Biaya Admin</td>
                <td>${biayaAdmin}</td>
              </tr>
              <tr class="font-weight-bold">
                <td colspan="3" class="text-right">Total Biaya</td>
                <td id="totalBiaya" data-biaya="${totalBiaya}">Rp${totalBiaya}</td>
              </tr>
            `;
            $("#tabelBiaya").html(html);
            $(`.step1`).addClass('d-none');
            $(`.step2`).removeClass('d-none');
          }
        } else {
          Swal.fire("ERROR", "Terjadi kesalahan", "error");
        }
      });
  }

  function step1() {
    $(".step2").addClass('d-none');
    $(".step1").removeClass('d-none');
  }


  function ubahAlamat() {
    let selected = $("input[name='alamatmu']:checked").val();
    $("#ubahAlamatBtn").prop("disabled", true);
    fetch("{{ route('user.ubahAlamat') }}", {
      method: "POST",
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({alamat_id: selected})
    })
    .then(res => res.json())
    .then(data => {
      if(data.success){
        let html = `
          <label for="" class="font-weight-bold mb-1">Alamat : </label>
          <p id="alamat_terpilih" id="${data.alamat.kelurahan}" data-pos="${data.alamat.kode_pos}" data-id="${data.alamat.id}" data-alamat="${data.alamat.kelurahan}, ${data.alamat.kecamatan}, ${data.alamat.kabupaten}, ${data.alamat.provinsi}, ${data.alamat.kode_pos}">${data.alamat.kelurahan}, ${data.alamat.kecamatan}, ${data.alamat.kabupaten}, ${data.alamat.provinsi}, ${data.alamat.kode_pos}</p>
        `;
        $("#alamatmu").html(html);
        $("#ubahAlamatBtn").prop("disabled", false);
        $("#modalUbahAlamat").modal("hide");
        Swal.fire(data.success, "", "success");
      } else {
        $("#ubahAlamatBtn").prop("disabled", false);
        Swal.fire(data.error, "", "error");
      }
    });
  }

  function tambahAlamat(){
    $("#modalAlamat").modal("show");
    fetch("{{ route('api.provinsi') }}")
    .then(res => res.json())
    .then(data => {
      let options = '<option value="" disabled selected>Pilih Provinsi</option>';
      const provinsi = data.data;
      provinsi.forEach(function(prov) {
        options += `<option value="${prov.name}" data-code="${prov.code}">${prov.name}</option>`
      });
      $('#provinsi').html(options);
    });
  }

  function simpanAlamat(){
    const phone = $("#phone").val();
    const kodepos = $("#kodepos").val();
    const kelurahan = $("#kelurahan").val();
    const kecamatan = $("#kecamatan").val();
    const kabupaten = $("#kabupaten").val();
    const provinsi = $("#provinsi").val();

    const data = {
      phone: phone,
      kodepos: kodepos,
      kelurahan: kelurahan,
      kecamatan: kecamatan,
      kabupaten: kabupaten,
      provinsi: provinsi,
    }
    $("#simpanAlamat").prop("disabled", true);
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
      if(data.validation){
        Swal.fire(data.validation, "", "warning");
      } else if(data.error){
        Swal.fire(data.error, "", "error");
      } else {
        let alamat = `
          <label for="" class="font-weight-bold mb-1">Alamat : </label>
          <p id="alamat_terpilih" id="${data.alamat.kelurahan}" data-pos="${data.alamat.kode_pos}" data-id="${data.alamat.id}" data-alamat="${data.alamat.kelurahan}, ${data.alamat.kecamatan}, ${data.alamat.kabupaten}, ${data.alamat.provinsi}, ${data.alamat.kode_pos}">${data.alamat.kelurahan}, ${data.alamat.kecamatan}, ${data.alamat.kabupaten}, ${data.alamat.provinsi}, ${data.alamat.kode_pos}</p>
        `;
        let btn = `
          <button id="editAlamatBtn" type="button" class="btn btn-sm btn-outline-primary" onclick="editAlamat()">Edit Alamat</button>
        `;
        Swal.fire(data.success, "", "success");
        $("#tambahAlamatBtn").addClass('d-none');
        $('.btn-container').append(btn);
        $("#modalAlamat").modal("hide");
        $(".alamat-container").html(alamat);        
      }
    });
  }

  function pilihAlamat(){
    let html = "";
    let kodepos = $("#alamat_terpilih").data('pos');
    let data = {
      kodepos: kodepos,
    }
    html += `
    <div class="text-center my-3">
      <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
      </div>
      <p>Memuat data, harap tunggu...</p>
    </div>
    `;
    $("#kurir").html(html);

    fetch("{{ route('api.kurir') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
      $("#kurirBtn").addClass('d-none');
      const couriers = data.pricing;
      html = '';
      couriers.forEach(kurir => {
        html += `
          <div class="col-md-12">
            <label class="d-flex align-items-center justify-content-between border rounded p-3 mb-2 courier-card" style="cursor: pointer;" data-biaya="${kurir.price}" data-kurir="${kurir.company}" data-tipe="${kurir.type}" data-metode=${JSON.stringify(kurir.available_collection_method)}>
              <div class="d-flex align-items-center">
                <img src="{{ asset('storage/kurir/${kurir.company}.240x90.png') }}" alt="${kurir.courier_name}" style="width: 50px; height: auto;" class="mr-3">
                <div class="">
                  <div class="font-weight-bold">${kurir.courier_name}</div>
                  <small class="text-muted">${kurir.courier_service_name}</small><br>
                  <small class="text-muted">Estimasi: ${kurir.shipment_duration_range} hari</small>
                </div>
              </div>
              <div class="text-right font-weight-bold text-success">Rp${kurir.price.toLocaleString()}</div>
            </label>
          </div>
        `;
      });
      $("#kurir").html(html);
      $(".courier-card").on('click', function(){
        $(".courier-card").removeClass('selected');
        $(this).addClass('selected');
      });
    });
  }
  
  
  
    function refreshOptions() {
        let selectedDok = [];
      // Ambil semua nilai yang sedang dipilih
      $("#dokumenContainer select[name='namadok[]']").each(function(){
        let val = $(this).val();
        if(val !== "") {
          selectedDok.push(val);
        }
      });
    
      // Loop semua select
      $("#dokumenContainer select[name='namadok[]']").each(function(){
        let currentSelect = $(this);
        let currentVal = currentSelect.val();
    
        // Show ALL options dulu (reset)
        currentSelect.find("option").each(function(){
          $(this).show();
        });
    
        // Hide yang sudah dipilih di select lain
        selectedDok.forEach(function(val){
          if(val !== "" && val !== currentVal){
            currentSelect.find(`option[value="${val}"]`).hide();
          }
        });
      });
    }

  function tambahDokumen(){
    let newRow = `
      <div class="form-row align-items-end dokumen-item">
        <div class="form-group col-md-4">
          <select id="namadok" class="form-control namadok" name="namadok[]">
            <option value="">Pilih Salah Satu</option>
            @foreach($dokumen as $dok)
              <option value="{{ $dok->nama_dokumen }}">{{ $dok->nama_dokumen }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group col-md-3">
          <input type="number" class="form-control" placeholder="Jumlah cetak" name="cetak[]" min="5" max="30">
        </div>
        <div class="form-group col-md-3">
          <button type="button" class="btn btn-danger" onclick="hapusDokumen(this)">Hapus</button>
        </div>
      </div>
    `;
    $("#dokumenContainer").append(newRow);
    refreshOptions();
  };
  
    function hapusDokumen(e){
      $(e).closest(".dokumen-item").remove();
      refreshOptions();
    }
</script>
@endsection