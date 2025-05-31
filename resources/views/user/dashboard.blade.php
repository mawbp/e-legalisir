@extends('user.home')
@section('style')
  <style>
    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      background-color: rgba(0, 0, 0, 0.5);
      background-size: 100% 100%;
      border-radius: 50%;
      width: 25px;
      height: 25px;
    }
  </style>
@endsection
@section('content')
  <div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
  </div>

  <div class="row">
    <div class="col-xl-12 col-lg-12">
      <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Permohonan Anda</h6>
                </div>
                <div class="card-body">
                  @if($permohonanku)
                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead class="thead-light">
                          <tr>
                            <th>Tanggal Permohonan</th>
                            <th>Status Permohonan</th>
                            <th>Total biaya</th>
                            <th>Catatan admin</th>
                            <th>Aksi</th>
                          </tr>
                        </thead>
                        <tbody id="tabelBiaya">
                          @foreach($permohonanku as $p)
                            <tr>
                              <td>{{$p['tanggal']}}</td>
                              <td>{{$p['status']}}</td>
                              <td>Rp{{ number_format($p['total_biaya'], 0, ',', '.') }}</td>
                              <td>{{$p['catatan'] ? $p['catatan'] : 'Tidak ada'}}</td>
                              <td>
                                @if($p['status'] == "Menunggu Pembayaran")
                                  <a href="{{ route('user.biaya', ['id' => $p['permohonan_id']]) }}" class="btn btn-sm btn-info">Bayar</a>
                                @elseif($p['status'] == "Dokumen Ditolak")
                                  <a href="{{ route('user.detail', ['id' => $p['permohonan_id']]) }}" class="btn btn-sm btn-info">Perbaiki Dokumen</a>
                                @else
                                  <a href="{{ route('user.detail', ['id' => $p['permohonan_id']]) }}" class="btn btn-sm btn-info">Lihat Detail</a>
                                @endif
                                @if($p['status'] == "Pengiriman / Pengambilan Dokumen" && $p['pengiriman'] == "Dikirim ke Rumah")
                                  <a onclick="trackDokumen('{{ $p['permohonan_id'] }}')" id="lacakBtn" class="btn btn-sm btn-info">Lacak</a>
                                @endif
                                @if($p['status'] == "Pengiriman / Pengambilan Dokumen")
                                  <a onclick="selesai('{{ $p['permohonan_id'] }}')" id="selesaiBtn" class="btn btn-sm btn-success">Selesai</a>
                                @endif
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  @else
                    <p class="mb-3">Anda belum memiliki permohonan yang diproses, silahkan ajukan permohonan terlebih dahulu.</p>
                    <a href="{{ route('user.pengajuan') }}" class="btn btn-primary">Ajukan Permohonan</a>
                  @endif
                </div>
            </div>
        </div>   
      </div>
    </div>
    <div class="col-xl-4 col-lg-5 d-flex flex-column justify-content-between">
      <div class="card shadow mb-4 flex-fill">
        <!-- Card Header - Dropdown -->
        <div
            class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Tarif Legalisir Dokumen</h6>
        </div>
        <!-- Card Body -->
        <div class="card-body">
          <ul class="list-group">
            @foreach($dokumen as $d)
              <li class="list-group-item d-flex justify-content-between align-items-center">
                {{ $d->nama_dokumen }}
                <span class="badge badge-primary badge-pill">Rp{{ number_format($d->harga_per_lembar, 0, ',','.') }}</span>
              </li>
            @endforeach
          </ul>
          <small class="text-muted">Harga berlaku per lembar dokumen.</small>
          <div class="mt-3 text-left">
            <strong>Total Jenis Dokumen: {{ count($dokumen) }}</strong>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Pengumuman Terbaru</h6>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="list-group">
                    <!-- Item Pengumuman 1 -->
                    @foreach($pengumuman as $p)
                        <a role="button" onclick="getUmum({{ $p->id }})" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div class="text-truncate" style="max-width: 80%; font-weight: bold;">
                                {{ $p->judul }}
                            </div>
                            <span class="badge badge-info badge-pill">{{ $p->created_at_formatted }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
  </div>
    <div class="modal fade" id="pengumumanModal" tabindex="-1" aria-labelledby="pengumumanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pengumumanModalLabel">Detail Pengumuman</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h1 id="judulPengumuman"></h1>
                <div id="tanggalPengumuman"></div><br>
                <div id="isiPengumuman"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="modaltf" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAlamatLabel">Transfer Bank</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <div class="row">
              <div class="col-md-6">
                <label for="namabank" class="form-label">Nama Bank Tujuan</label>
                <input type="hidden" id="permohonan_id_tf">
                <input type="text" class="form-control" name="namabank" readonly="" value="{{ $pengaturan['nama_bank']->nilai }}">
              </div>
              <div class="col-md-6">
                <label for="namabank" class="form-label">No Rekening</label>
                <input type="text" class="form-control" name="namabank" readonly="" value="{{ $pengaturan['no_rekening']->nilai }}">
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="buktibayar" class="form-label">Bukti Pembayaran</label>
            <input type="file" class="form-control" id="file">
          </div>
        </div>
        <div class="modal-footer">
          <button id="bayartf" type="button" class="btn btn-success" onclick="bayarTf()">
            Unggah bukti pembayaran
          </button>
        </div>
      </div>
    </div>
  </div>
  @if(empty(Auth::user()->phone) || empty(Auth::user()->alamat_id))
    <div class="modal fade" id="profilModal" role="dialog" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-left-danger">
          <div class="modal-header">
            <h5 class="modal-title text-danger">Lengkapi Profil Anda</h5>
          </div>
          <div class="modal-body">
            Profil Anda belum lengkap. Harap lengkapi data diri anda.
          </div>
          <div class="modal-footer">
            <a href="{{ route('user.akun') }}" class="btn btn-primary" data-dismiss="modal">Lengkapi Sekarang</a>
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection
@section('script')
  <script>
    document.addEventListener("DOMContentLoaded", function(){
      $('.countdown').each(function(){
        let el = $(this);
        let expiredAt = new Date(el.data('expired')).getTime();

        const interval = setInterval(function(){
          let now = new Date().getTime();
          let distance = expiredAt - now;
          if(distance < 0){
            clearInterval(interval);
            el.text("Waktu habis");
            el.removeClass('badge-danger').addClass('badge-secondary');
            return;
          }
          let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          let seconds = Math.floor((distance % (1000 * 60)) / (1000));

          el.text(hours + " jam " + minutes + " menit " + seconds + " detik ");
        }, 1000);
      });
    });

    function selesai(id){
      $("#selesaiBtn").prop("disabled", true);
      Swal.fire({
        title: 'Yakin selesaikan proses legalisir?, pastikan dokumen sudah diterima',
        icon: "info",
        showCancelButton: true,
        cancelButtonText: "Kembali",
        confirmButtonText: "Ok",
      }).then((btn) => {
        if(btn){
          Swal.fire({
            title: "Loading..",
            html: 'Sedang memproses data',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          fetch("{{ route('api.selesai') }}", {
            method: "POST",
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            body: JSON.stringify({permohonan_id: id})
          })
          .then(res => res.json())
          .then(data => {
            if(data.success){
              Swal.close();
              $("#selesaiBtn").prop("disabled", false);
              Swal.fire({
                title: data.success,
                icon: "success",
                showCancelButton: false,
                confirmButtonText: "OK"
              }).then((result) => {
                if(result){
                  window.location.href = "{{ route('user.dashboard') }}";
                }
              });
            } else {
              Swal.fire(data.error, "", "error");
            }
          });
        }
      });
    }

    function trackDokumen(id){
      const tracking = { id: id};
      
      fetch("{{ route('api.track.order') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(tracking)
      })
      .then(res => res.json())
      .then(data => {
        window.open(data.url, "_blank");
      });
    }

    function bayarTf(){
      let formData = new FormData();
      formData.append('id', $("#permohonan_id_tf").val());
      formData.append('file', $("#file")[0].files[0]);
      Swal.fire({
        title: "Loading..",
        html: 'Sedang memproses data',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      fetch("{{ route('api.paymenttf') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        Swal.close();
        if(data.success){
          $("#modaltf").modal("hide");
          Swal.fire({
            title: data.success,
            icon: "success",
            showDenyButton: false,
            showCancelButton: false,
            confirmButtonText: "Ok",
          }).then((result) => {
            if(result){
              window.location.href = "{{ route('user.dashboard') }}";
            }
          });
        } else if(data.failed){
          Swal.fire(data.failed, "", "error");
        } else if(data.validation){
          Swal.fire(data.validation, "", "error");
        }
      });
    }

    function lanjutBayar(token, bayarId, mohonId) {
      snap.pay(token, {
        onSuccess: function(result){
          Swal.fire({
            title: 'Loading...',
            html: 'Sedang memproses data',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          fetch("{{ route('api.update.bayar') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
              pembayaran_id: bayarId,
              permohonan_id: mohonId,
              bukti_tf: "Berhasil",
            })
          })
          .then(res => res.json())
          .then(data => {
            if(data.success){
              Swal.close();
              Swal.fire({
                title: `Pembayaran anda berhasil`,
                showDenyButton: false,
                showCancelButton: false,
                confirmButtonText: "Ok",
              }).then((result) => {
                if(result){
                  window.location.href = "{{ route('user.dashboard') }}";
                }
              });
              $("#detailmodal").modal("hide");
            }
          });
        },
        onPending: function(result){
          Swal.fire({
            title: `Pembayaran anda tertunda, harap segera selesaikan`,
            showDenyButton: false,
            showCancelButton: false,
            confirmButtonText: "Ya",
          }).then((result) => {
            if(result){
              window.location.href = "{{ route('user.dashboard') }}";
            }
          });
        },
        onClose: function(){
          Swal.fire({
            title: `Pembayaran anda tertunda, harap segera selesaikan`,
            showDenyButton: false,
            showCancelButton: false,
            confirmButtonText: "Ya",
          }).then((result) => {
            if(result){
              window.location.href = "{{ route('user.dashboard') }}";
            }
          });
        }
      })
    }
    
    function getUmum(id){
        const url = "{{ route('user.getumum', ['id' => ':id']) }}".replace(':id', id);
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    console.log(data);
                    $("#judulPengumuman").html(data.success.judul);
                    $("#tanggalPengumuman").html(data.success.created_at_formatted);
                    $("#isiPengumuman").html(data.success.isi);
                    $("#pengumumanModal").modal("show");
                } else if(data.error){
                    Swal.fire(data.error, "", "error");
                } else {
                    Swal.fire("Gagal", "", "error");
                }
            });
        
    }
  </script>
@endsection
