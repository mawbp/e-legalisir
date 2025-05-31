@extends('user.dashboard')

@section('title', 'Cek Status Pengajuan')
@section('subtitle', 'Menampilkan perkembangan status permohonan yang udah kamu ajukan')

@section('content')
  <table id="example" class="table table-striped table-hovered nowrap mt-3" style="width: 100%">
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal Permohonan</th>
        <th>Nama Pemohon</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
    </thead>
  </table>
  <div class="row">
    <div class="col-md-12">
    </div>
  </div>
  <div class="modal fade" id="modaltf" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAlamatLabel">Transfer Bank</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <div class="row">
              <div class="col-md-6">
                <label for="namabank" class="form-label">Nama Bank Tujuan</label>
                <input type="hidden" id="permohonan_id_tf">
                <input type="text" class="form-control" name="namabank" readonly="">
              </div>
              <div class="col-md-6">
                <label for="namabank" class="form-label">No Rekening</label>
                <input type="text" class="form-control" name="namabank" readonly="">
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
  <div class="modal fade" role="dialog" id="detailmodal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="font-size: 20px;">Detail Pengajuan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row list-dokumen">
            <div class="form-group col-md-6 mb-3">
              <label>NIM</label>
              <input type="hidden" id="permohonan_id">
              <input type="text" class="form-control fadd" id="nim" readonly>
            </div>
            <div class="form-group col-md-12 mb-3">
              <label>Nama Mahasiswa</label>
              <input type="text" class="form-control fadd" id="nama" readonly>
            </div>
            <div class="form-group col-md-12 mb-3">
              <strong>Dokumen yang diajukan: </strong>
            </div>
          </div>
        </div>
        <div class="modal-footer tombol-modal-detail">
          <a id="invoice" class="btn btn-warning" download>Unduh Invoice</a>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
  <script>
    let table;
    table = $("#example").DataTable({
      responsive: true,
      ajax: {
        url: "{{ route('user.dtcekstatus') }}",
        dataSrc: ""
      },
      columns: [
        {
          data: null,
          render: function(data, type, row, meta){
            return meta.row + 1;
          }
        },
        {
          data: 'tanggal',
          className: 'text-start',
        },
        {data: 'nama'},
        {data: 'nama_status'},
        {
          data: 'permohonan_id',
          render: function(data, type, row){
            return `<button onclick='detail("${data}")' class='btn btn-primary' style='padding: 10px'><i class='fas fa-edit'></i> Detail</button>`;
          }
        }
      ],
    });

    function detail(id){
      fetch("{{ route('user.detail') }}", {
        method: "POST",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        body: JSON.stringify({id: id})
      })
      .then(res => res.json())
      .then(data => {
        console.log(data);
        if($(".list-dokumen .detail-data").length){
          $(".detail-data").remove();
        }
        const status = data[0].status_permohonan.nama_status; 
        const container = $(".list-dokumen");
        let html = '';
        let buktibayar = '';
        let btn = '';
        const keterangan = `
          <div class="form-group col-md-12 detail-data mt-3">
            <label>Status Permohonan</label>
            <input type="text" class="form-control fadd" value="${status}" readonly>
          </div>
        `;
        if(data[0].status_permohonan.nama_status == "Menunggu Pembayaran" && !data[0].pembayaran.bukti_pembayaran){
          btn += `<button type="button" id="bayardok" class="btn btn-success detail-data" onclick="bayarDokumen()">Selesaikan Pembayaran</button>`;
        } else if(data[0].status_permohonan.nama_status == "Pengiriman / Pengambilan Dokumen" && data[0].pembayaran.metode_pengiriman == "Antar ke Rumah"){
          btn += `<button type="button" class="btn btn-success detail-data" onclick="trackDokumen()">Lacak pengiriman</button>`;
        }
        data.forEach(i => {
          html += `
            <div class="form-group col-md-4 detail-data">
              <label>Nama Dokumen</label>
              <input type="text" class="form-control fadd" value="${i.dokumen.nama_dokumen}" readonly>
            </div>
            <div class="form-group col-md-4 detail-data">
              <label>Nomor Dokumen</label>
              <input type="text" class="form-control fadd" value="${i.nomor_dokumen}" readonly>
            </div>
            <div class="form-group col-md-4 detail-data">
              <label>Jumlah Cetak</label>
              <input type="text" class="form-control fadd" value="${i.jumlah_cetak}" readonly>
            </div>
          `;
        });
        container.append(html);
        container.append(keterangan);
        container.append(buktibayar);
        $("#detailmodal").modal("show");
        $("#permohonan_id").val(id);
        $("#nim").val(data[0].user.alumni.nim);
        $("#nama").val(data[0].user.alumni.nama);
        $(".tombol-modal-detail").append(btn);
        $("#invoice").attr("href", `{{ asset('storage/invoices/${id}.pdf') }}`)
      });
    }

    function selesai(id){
      const data = {id: id};
      swal({
        title: 'Konfirmasi',
        text: "Yakin selesaikan pesanan?, pastikan dokumen sudah diterima",
        icon: "warning",
        buttons: {
          confirm: {text: 'Ya', className: 'btn btn-success'},
          cancel: {visible: true, text: "Tidak", className: 'btn btn-danger'}
        }
      }).then((btn) => {
        fetch("{{ route('api.selesai') }}", {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
          table.ajax.reload();
          swal({title: 'Berhasil', text: data.message, icon: "success"});
        });
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

    function bayarDokumen(){
      const id = $("#permohonan_id").val();
      let btn = $("#bayardok");
      let loader = `
        <span class="spinner-border spinner-border-sm"></span>
        <span role="status">Loading...</span>
      `;
      btn.html(loader);
      btn.prop("disabled", true);
      fetch("{{ route('user.cekbayar') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({id: id})
      })
      .then(res => res.json())
      .then(data => {
        if(data.metode == 'pg'){
          fetch("{{ route('api.paymentpg') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({id: id})
          })
          .then(res => res.json())
          .then(data => {
            snap.pay(data.snapToken, {
              onSuccess: function(result){
                fetch("{{ route('api.update.bayar') }}", {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                  },
                  body: JSON.stringify({
                    pembayaran_id: data.id,
                    permohonan_id: data.permohonan_id,
                    bukti_tf: "Berhasil",
                  })
                })
                .then(res => res.json())
                .then(data => {
                  if(data.success){
                    btn.html("Selesaikan pembayaran");
                    btn.prop("disabled", false);
                    table.ajax.reload();
                    Swal.fire(data.success, "", "success");
                    $("#detailmodal").modal("hide");
                  }
                });
              },
              onPending: function(result){
                alert("waiting your payment"); console.log(result);
              },
              onClose: function(){
                alert("you closed the popup without finishing the payment");
              }
            })
          });
        } else if(data.metode == 'tf'){
          $("#permohonan_id_tf").val(id);
          btn.html("Selesaikan pembayaran");
          btn.prop("disabled", false);
          $("#detailmodal").modal("hide");
          $("#modaltf").modal("show");
        }
      });
    }

    function bayarTf() {
      let btn = $("#bayartf");
      let loader = `
        <span class="spinner-border spinner-border-sm"></span>
        <span role="status">Loading...</span>
      `;
      btn.html(loader);
      btn.prop("disabled", true);
      let formData = new FormData();
      console.log($("#permohonan_id_tf").val());
      formData.append('id', $("#permohonan_id_tf").val());
      formData.append('file', $("#file")[0].files[0]);
      fetch("{{ route('api.paymenttf') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if(data.success){
          fetch("{{ route('api.update.bayar') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
              pembayaran_id: data.pembayaran_id,
              permohonan_id: data.permohonan_id,
              bukti_tf: data.bukti_tf,
            })
          })
          .then(res => res.json())
          .then(data => {
            if(data.success){
              btn.prop("disabled", false);
              btn.html("Unggah bukti pembayaran");
              table.ajax.reload();
              Swal.fire(data.success, "", "success");
              $("#modaltf").modal("hide");
            } else {
              console.log(data);
            }
          });
        } else if(data.failed) {
          Swal.fire(data.failed, "", "error");
        } else if(data.validation){
          Swal.fire(data.validation, "", "error");
        }
      });
    }
  </script>
@endsection