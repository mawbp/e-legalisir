@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Data Permohonan</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengelolaan permohonan</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header bg-light border-bottom">
            <div class="p-3 rounded" style="background-color: #f4f6f9;">
              <h6 class="mb-2 fw-bold text-primary">Informasi: </h6>
              <ul class="mb-0 small text-muted">
                <li>Klik ID Permohonan untuk melihat detail data.</li>
                <li>Harap lakukan verifikasi pada pembayaran melalui transfer bank.</li>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="permohonantbl" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Tanggal Permohonan</th>
                    <th>ID Permohonan</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="alert alert-success" role="alert" id="notifikasi" style="display: none; position:fixed; top:20px; right:20px; z-index:1050"></div>
  <div class="modal fade" role="dialog" id="detailmodal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="font-size: 20px;">Detail Pengajuan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row validasi-modal d-none">
            <div class="form-group col-md-6">
              <label>NIM</label>
              <input type="hidden" id="permohonan_id">
              <input type="text" class="form-control fadd" id="nim" readonly>
            </div>
            <div class="form-group col-md-12">
              <label>Nama Mahasiswa</label>
              <input type="text" class="form-control fadd" id="nama" readonly>
            </div>
          </div>
        </div>
        <div class="modal-footer footer-validasi">
        </div>
      </div>
    </div>
  </div>
  <select id="filterStatus" class="form-control-sm mt-2 d-none" style="width: 200px; margin-left: 10px; border: 1px solid #ced4da;">
    <option value="">-- Semua Permohonan --</option>
    <option value="Validasi Dokumen"> Validasi Dokumen </option>
    <option value="Validasi Pembayaran"> Validasi Pembayaran </option>
    <option value="Proses Legalisir Dokumen"> Proses Legalisir Dokumen </option>
    <option value="Pengiriman / Pengambilan Dokumen"> Pengiriman / Pengambilan Dokumen </option>
    <option value="Selesai"> Selesai </option>
  </select>
@endsection
@section('script')
  <script>
    let table;
    table = $("#permohonantbl").DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('admin.dtpermohonan') }}",
        dataSrc: "",
        data: function(d){
          d.status = $("#filterStatus").val();
        },
        dataSrc: function(json){
          return json.data;
        }
      },
      columns: [
        {data: 'nim', name: 'alumnis.nim'},
        {data: 'nama', name: 'alumnis.nama'},
        {
          data: 'status', 
          name: 'status_permohonans',
          render: function(data, type, row){
            if(data == "Validasi Dokumen"){
              return `<span class="badge badge-warning">Baru</span>`;
            } else if(data == "Selesai"){
              return `<span class="badge badge-success">Selesai</span>`;
            } else {
              return `<span class="badge badge-info">Diproses</span>`;
            }
          }
        },
        {
          data: 'tanggal',
          render: function (data, type, row){
            const date = new Date(data);
            return date.toLocaleString('id-ID', {
              day: '2-digit',
              month: 'long',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
            });
          },
          name: 'permohonans.created_at',
          className: 'text-start',
        },
        {
          data: 'permohonan_id',
          name: 'permohonans.permohonan_id',
          render: function(data, type, row){
            const base = "{{ route('admin.detail', ['id' => 'REPLACE_ID']) }}"
            const url = base.replace('REPLACE_ID', data);
            return `<a href="${url}" style="cursor: pointer; color: blue;">${data}</a>`;
          }
        },
      ],
    });
    $("#permohonantbl_filter").append($("#filterStatus"));
    $("#filterStatus").removeClass('d-none');
    document.addEventListener("DOMContentLoaded", function(){
      $("#filterStatus").change(function(){
        table.ajax.reload();
      });
      $("#searchInput").on('keyup', function(){
        table.search(this.value).draw();
      })
    });


    function updateStatus(id, validasi){
      let nim = $("#nim").val();
      Swal.fire({
        title: `Apakah ${validasi} ini valid?`,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Kembali",
        denyButtonText: "Tidak"
      }).then((result) => {

        if(result.isConfirmed){
          Swal.fire({
            title: 'Loading...',
            html: 'Sedang memproses data',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          fetch("{{ route('admin.update') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({nim: nim, permohonanId: id})
          })
          .then(res => res.json())
          .then(data => {
            Swal.close();
            if(data.success){
              console.log(data);
              Swal.fire({title: "Berhasil", text: data.success, icon: "success"});
              table.ajax.reload();
              $("#detailmodal").modal("hide");
            } else if(data.failed){
              Swal.fire({title: "Gagal", text: data.failed, icon: "warning"});
            } else if(data.error) {
              Swal.fire({title: "Gagal", text: data.error, icon: "error"});
            } else {
              console.log(data);
            }
          })
        } else if(result.isDenied){
          Swal.fire({
            title: 'Loading...',
            html: 'Sedang memproses data',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          fetch("{{ route('admin.tolak') }}", {
            headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}",
              'Content-Type': "application/json",
            },
            body: JSON.stringify({permohonanId: id}),
          })
          .then(res => res.json())
          .then(data => {
            Swal.close();
            if(data.success){
              Swal.fire(data.success, "", "success");
              table.ajax.reload();
              $("#detailmodal").modal("hide");
            } else {
              Swal.fire(data.error, "", "error");
              table.ajax.reload();
              $("#detailmodal").modal("hide");
            }
          });
        }
      });
    }

    function kirimDokumen(id){
      let metodeAmbil = $("#metode_ambil").val();
      if(metodeAmbil == ""){
        Swal.fire("Harap pilih metode pengambilan paket", "", "warning");
        return;
      }

      Swal.fire({
        title: 'Yakin memulai proses pengiriman?',
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak",
      }).then((btn) => {
        if(btn.isConfirmed){
          Swal.fire({
            title: 'Loading...',
            html: 'Sedang memproses data',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          const data = {permohonanId: id, metodeAmbil: metodeAmbil};
          fetch("{{ route('api.order') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
          })
          .then(res => res.json())
          .then(data => {
            const trackingId = data.response.courier.tracking_id;
            const orderId = data.response.id;
            const tracking = {
              trackingId: trackingId,
              orderId: orderId,
              permohonanId: id,
            };

            fetch("{{ route('api.store.tracking') }}", {
              method: 'POST',
              headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify(tracking)
            })
            .then(res => res.json())
            .then(data => {
              table.ajax.reload();
              if(data.success){
                Swal.close();
                Swal.fire("Berhasil", data.success, "success");
                table.ajax.reload();
                $("#detailmodal").modal("hide");
              } else {
                Swal.close();
                Swal.fire("Gagal", "Data pengiriman gagal dikirimkan ke kurir.", "error");
                table.ajax.reload();
                $("#detailmodal").modal("hide");
              }
            });
          });
        }
      });
    }

    function trackDokumen(id){
      const tracking = {
        id: id,
      }
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

    function detail(id, nim){
      $(".validasi-modal").addClass('d-none');
      fetch("{{ route('admin.tolak') }}", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": "{{ csrf_token() }}",
          "Content-Type": "application/json"
        },
        body: JSON.stringify({id: id, nim: nim})
      })
      .then(res => res.json())
      .then(data => {
        $(".data-validasi").remove();
        $(".validasi-btn").remove();
        const status = data[0].status_permohonan;
        const modal = $(".validasi-modal");
        const footer = $(".footer-validasi");
        let html = '';
        let btn = '';
        const keterangan = `
          <div class="form-group col-md-12 data-validasi">
            <label>Status Permohonan</label>
            <input type="text" class="form-control fadd" value="${status}" readonly>
          </div>
        `;

        if(status == "Validasi Dokumen"){
          btn = `
            <button type="button" class="btn btn-success validasi-btn" onclick="updateStatus('${id}', 'Dokumen')">Validasi Dokumen</button>
          `;
          html += `
            <div class="form-group col-md-12 data-validasi">
              <strong>Dokumen yang diajukan: </strong>
            </div>
          `;
          data.forEach(i => {
            html += `
              <div class="form-group col-md-4 data-validasi">
                <label>Nama Dokumen</label>
                <input type="text" class="form-control fadd" value="${i.dokumen.nama_dokumen}" readonly>
              </div>
              <div class="form-group col-md-4 data-validasi">
                <label>Nomor Dokumen</label>
                <input type="text" class="form-control fadd" value="${i.nomor_dokumen}" readonly>
              </div>
              <div class="form-group col-md-4 data-validasi">
                <label>Jumlah Cetak</label>
                <input type="text" class="form-control fadd" value="${i.jumlah_cetak}" readonly>
              </div>
            `;
          });
        } else if(status == "Validasi Pembayaran"){
          btn = `
            <button type="button" class="btn btn-success validasi-btn" onclick="updateStatus('${id}', 'Pembayaran')">Validasi Pembayaran</button>
          `;
          html += `
            <div class="form-group col-md-6 data-validasi">
              <label>Metode Pembayaran</label>
              <input type="text" class="form-control fadd" value="${data[0].pembayaran.metode_pembayaran}" readonly>
            </div>
            <div class="form-group col-md-6 data-validasi">
              <label>Jumlah Pembayaran</label>
              <input type="text" class="form-control fadd" value="${data[0].pembayaran.jumlah_bayar}" readonly>
            </div>
            <div class="form-group col-md-12 data-validasi">
              <label>Bukti Pembayaran</label>
              <div class="">
              <a href="{{ asset('uploads/${data[0].pembayaran.bukti_pembayaran}') }}" data-lightbox="image-1" data-title="Bukti Pembayaran">
                <img src="{{ asset('uploads/${data[0].pembayaran.bukti_pembayaran}') }}" alt="image" style="width: 150px;" />
              </a>
                <p class="text-muted mt-2">Klik untuk memperbesar</p>
              </div>
            </div>
          `;
        } else if (status == "Proses Legalisir Dokumen"){
          if(data[0].pembayaran.metode_pengiriman == "Antar ke Rumah"){
            const metodeArray = JSON.parse(data[0].metode_pengambilan);

            btn = `
              <button type="button" class="btn btn-success validasi-btn" onclick="kirimDokumen('${id}')">Mulai proses pengiriman</button>
            `;
            html += `
              <div class="form-group col-md-6 data-validasi">
                <label>Kurir</label>
                <input type="text" class="form-control fadd" id="kurir" value="${data[0].kurir}" readonly>
              </div>
              <div class="form-group col-md-6 data-validasi">
                <label>Tipe Kurir</label>
                <input type="text" class="form-control fadd" id="tipe_kurir" value="${data[0].tipe_kurir}" readonly>
              </div>
              <div class="form-group col-md-12 data-validasi">
                <label>Metode Pengambilan</label>
                <select class="form-control fadd" id="metode_ambil">
                  <option value="">Pilih Salah Satu</option>
            `;

            metodeArray.forEach(metode => {
              html += `<option value="${metode}">${metode}</option>`; 
            });

            html += "</select></div>";
            html += `
              <div class="form-group col-md-12 data-validasi">
                <label>Metode Pengiriman</label>
                <input type="text" class="form-control fadd" value="${data[0].pembayaran.metode_pengiriman}" readonly>
              </div>
            `;
          } else if(data[0].pembayaran.metode_pengiriman == "Ambil di Kampus"){
            btn = `
              <button type="button" class="btn btn-success validasi-btn" onclick="updateStatus('${id}', 'Permohonan')">Update permohonan</button>
            `;
            html += `
              <div class="form-group col-md-12 data-validasi">
                <strong>Dokumen yang diajukan: </strong>
              </div>
            `;
            data.forEach(i => {
              html += `
                <div class="form-group col-md-4 data-validasi">
                  <label>Nama Dokumen</label>
                  <input type="text" class="form-control fadd" value="${i.dokumen.nama_dokumen}" readonly>
                </div>
                <div class="form-group col-md-4 data-validasi">
                  <label>Nomor Dokumen</label>
                  <input type="text" class="form-control fadd" value="${i.nomor_dokumen}" readonly>
                </div>
                <div class="form-group col-md-4 data-validasi">
                  <label>Jumlah Cetak</label>
                  <input type="text" class="form-control fadd" value="${i.jumlah_cetak}" readonly>
                </div>
              `;
            });

            html += `
              <div class="form-group col-md-12 data-validasi">
                <label>Metode Pengiriman</label>
                <input type="text" class="form-control fadd" value="${data[0].pembayaran.metode_pengiriman}" readonly>
              </div>
            `;
          } else {
            Swal.fire("Metode pengiriman tidak sesuai", "", "error");
          }
        } else if(status == "Pengiriman / Pengambilan Dokumen"){
          if(data[0].pembayaran.metode_pengiriman == "Antar ke Rumah"){
            btn += `
              <button type="button" class="btn btn-primary validasi-btn" onclick="unduhResi('${id}')">Unduh Resi</button>
              <button type="button" class="btn btn-info validasi-btn" onclick="printLabel()">Cetak label pengiriman</button>
              <button type="button" class="btn btn-success validasi-btn" onclick="trackDokumen()">Lacak pengiriman</button>
            `;
          } else if(data[0].pembayaran.metode_pengiriman == "Ambil di Kampus"){
             html += `
              <div class="form-group col-md-12 data-validasi">
                <strong>Dokumen yang diajukan: </strong>
              </div>
            `;
            data.forEach(i => {
              html += `
                <div class="form-group col-md-4 data-validasi">
                  <label>Nama Dokumen</label>
                  <input type="text" class="form-control fadd" value="${i.dokumen.nama_dokumen}" readonly>
                </div>
                <div class="form-group col-md-4 data-validasi">
                  <label>Nomor Dokumen</label>
                  <input type="text" class="form-control fadd" value="${i.nomor_dokumen}" readonly>
                </div>
                <div class="form-group col-md-4 data-validasi">
                  <label>Jumlah Cetak</label>
                  <input type="text" class="form-control fadd" value="${i.jumlah_cetak}" readonly>
                </div>
              `;
            });

            html += `
              <div class="form-group col-md-12 data-validasi">
                <label>Metode Pengiriman</label>
                <input type="text" class="form-control fadd" value="${data[0].pembayaran.metode_pengiriman}" readonly>
              </div>
            `;
          } else {
            Swal.fire("Metode pengiriman tidak sesuai", "", "error");
          }
        }
        modal.append(html);
        modal.append(keterangan);
        footer.html(btn);
        modal.removeClass('d-none');
        $("#permohonan_id").val(id);  
        $("#nim").val(data[0].user.alumni.nim);
        $("#nama").val(data[0].user.alumni.nama);
        $("#detailmodal").modal("show");
      });
    }
  </script>
  <script src="{{ asset('js/lightbox.js')}}"></script>
  <script>
    lightbox.option({
      'positionFromTop': 200,
      'maxWidth': 500,
      'maxHeight': 500,
    });
  </script>
@endsection