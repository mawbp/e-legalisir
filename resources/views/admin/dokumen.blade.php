@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Data Dokumen</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengelolaan dokumen</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <div class="card-title">
              <button class="btn btn-primary" data-toggle="modal" data-target="#addmodal" data-backdrop="static" data-keyboard="false">Tambah data</button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="dokumentbl" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Dokumen</th>
                    <th>Deskripsi</th>
                    <th>Pemilik</th>
                    <th>Operasi</th>
                  </tr>
                </thead>
              </table>
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
          <h5 class="modal-title" style="font-size: 20px;">Tambah Data Dokumen</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row add-form">
            <div class="form-group col-md-12">
              <label>Nama Dokumen</label>
              <input type="text" class="form-control" id="nama" placeholder="Masukkan nama dokumen">
            </div>
            <div class="form-group col-md-12">
              <label>Deskripsi (Opsional)</label>
              <input type="text" class="form-control" id="des" placeholder="Masukkan deskripsi dokumen">
            </div>
            <div class="form-group col-md-12">
              <label>Biaya per cetak</label>
              <input type="number" class="form-control" id="harga" placeholder="Masukkan biaya per cetak">
            </div>
            <div class="form-group col-md-12">
              <label>Kepemilikan Dokumen</label>
              <select class="form-control" id="pemilik">
                  <option value="">Pilih salah satu</option>
                  <option value="Admin">Admin</option>
                  <option value="Alumni">Alumni</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="addBtn" class="btn btn-primary" onclick="tambahDokumen()">Simpan</button>
          <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" role="dialog" id="updatemodal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="font-size: 20px;">Update Data Dokumen</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row edit-form">
            <div class="form-group col-md-12">
              <label>Nama Dokumen</label>
              <input type="hidden" id="ide">
              <input type="text" class="form-control" id="namae" placeholder="Masukkan nama dokumen">
            </div>
            <div class="form-group col-md-12">
              <label>Deskripsi (Opsional)</label>
              <input type="text" class="form-control" id="dese" placeholder="Masukkan deskripsi dokumen">
            </div>
            <div class="form-group col-md-12">
              <label>Kepemilikan Dokumen</label>
              <select class="form-control" id="pemilike">
                  <option value="">Pilih salah satu</option>
                  <option value="Admin">Admin</option>
                  <option value="Alumni">Alumni</option>
              </select>
            </div>
            <div class="form-group col-md-4 lihat-berkas d-none">
                <a id="lihatBerkas" class="btn btn-primary" target="_blank" role="button" style="color: white;">Lihat berkas</a>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="updateBtn" class="btn btn-primary" onclick="updateDokumen()">Simpan</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
  <script>
    let table;
    table = $("#dokumentbl").DataTable({
      ajax: {
        url: "{{ route('dokumen.index') }}",
        dataSrc: ""
      },
      columns: [
        {
        data: null,
        render: function(data, type, row, meta){
          return meta.row + 1;
        }
        },
        {data: 'nama_dokumen'},
        {data: 'deskripsi'},
        {data: 'pemilik'},
        {
          data: 'id',
          render: function(data){
            return `
              <div class="form-button-action">
                <button type="button" data-toggle="tooltip" title="Lihat Detail" class="btn btn-link btn-primary btn-lg" onclick="tampilDokumen(${data})">
                  <i class="fas fa-edit"></i>
                </button>
                <button type="button" data-toggle="tooltip" title="Hapus" class="btn btn-link btn-danger" onclick="hapusDokumen(${data})">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            `;
          }
        }
      ],
    });
    
    document.addEventListener("DOMContentLoaded", function(){
        $("#pemilik").on("change", function(){
            if($(this).val() == "Admin"){
                $(".add-form").append(`
                    <div class="form-group col-md-12 add-scan">
                        <label>Upload Scan Dokumen</label>
                        <input type="file" class="form-control" id="scan">
                    </div>
                `);
            } else {
                $(".add-scan").remove();
            }
       });
       
       $("#pemilike").on("change", function(){
            if($(this).val() == "Admin"){
                $(".edit-form").append(`
                    <div class="form-group col-md-12 edit-scan">
                        <label>Upload Scan Dokumen</label>
                        <input type="file" class="form-control" id="scane">
                    </div>
                `);
                if($("#lihatBerkas").attr("href")){
                    $(".lihat-berkas").removeClass('d-none');   
                }
            } else {
                $(".edit-scan").remove();
                $(".lihat-berkas").addClass('d-none');
            }
       });
    });

    function tambahDokumen() {
      let nama = $("#nama").val();
      let des = $("#des").val();
      let pem = $("#pemilik").val();
      let harga = $("#harga").val();
      const formData = new FormData();
      formData.append('nama', nama);
      formData.append('des', des);
      formData.append('pem', pem);
      formData.append('harga', harga);
      const fileInput = document.getElementById('scan');
        if (fileInput && fileInput.files.length > 0) {
            formData.append('file', fileInput.files[0]);
        }
      if(nama == "" || pem == ""){
        Swal.fire({title: "Gagal", text: "Harap lengkapi seluruh kolom", icon: "error"});
      } else {
        $("#addBtn").prop("disabled", true).text("Loading...");
        fetch("{{ route('dokumen.store') }}", {
          method: "POST",
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          $("#addBtn").prop("disabled", false).text("Simpan");
          if(data.success){
            Swal.fire({title: "Berhasil", text: data.success, icon: "success"});
            table.ajax.reload();
            resetForm();
          } else if(data.validation){
            Swal.fire({title: "Gagal", text: data.validation, icon: "warning"});
          } else if(data.error) {
            Swal.fire({title: "Gagal", text: data.error, icon: "error"});
          } else {
            console.log(data);
          }
        });
      }
    };

    function resetForm(){
      $(".form-control").val("");
    }

    function tampilDokumen(id){
      fetch("{{ route('dokumen.show', ':id') }}".replace(":id", id), {
        method: "GET",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
      })
      .then(res => res.json())
      .then(data => {
        $("#ide").val(data.data.id);
        $("#namae").val(data.data.nama_dokumen);
        $("#dese").val(data.data.deskripsi);
        $("#pemilike").val(data.data.pemilik);
        $(".edit-scan").remove();
        if(data.data.pemilik == "Admin"){
            $(".edit-form").append(`
                <div class="form-group col-md-12 edit-scan">
                    <label>Upload Scan Dokumen</label>
                    <input type="file" class="form-control" id="scane">
                </div>
            `);
            $("#lihatBerkas").attr("href", `/uploads/${data.slug}.${data.ext}`);
            $(".lihat-berkas").removeClass('d-none');
        }
        $("#updatemodal").modal("show");
      });
    }

    function updateDokumen(){
      let id = $("#ide").val();
      let nama = $("#namae").val();
      let des = $("#dese").val();
      let pem = $("#pemilike").val();
      const formData = new FormData();
      formData.append('nama', nama);
      formData.append('des', des);
      formData.append('pem', pem);
      formData.append('_method', 'PUT');
      const fileInput = document.getElementById('scane');
      if (fileInput && fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
      }
      if(id == "" || nama == "" || pem == ""){
        Swal.fire({title: "Gagal", text: "Harap lengkapi seluruh kolom", icon: "error"});
      } else {
        $("#updateBtn").prop("disabled", true);
        $("#updateBtn").text("Loading...");
        fetch("{{ route('dokumen.update', ':id') }}".replace(':id', id), {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          $("#updateBtn").prop("disabled", false);
          $("#updateBtn").text("Simpan");
          if(data.success){
            Swal.fire({title: "Berhasil", text: data.success, icon: "success"});
            table.ajax.reload();
            $("#updatemodal").modal("hide");
          } else if(data.validation){
            Swal.fire({title: "Gagal", text: data.validation, icon: "warning"});
          } else if(data.error) {
            Swal.fire({title: "Gagal", text: data.error, icon: "error"});
          } else {
            console.log(data);
          }
        });
      }
    }

    function hapusDokumen(id){
      Swal.fire({
        title: "Yakin ingin menghapus dokumen ini?",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak"
      }).then((result) => {
        if(result.isConfirmed){  
          fetch("{{ route('dokumen.destroy', ':id') }}".replace(':id', id), {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
          })
          .then(res => res.json())
          .then(data => {
            if(data.success){
              Swal.fire({title: "Berhasil", text: data.success, icon: "success"});
              table.ajax.reload();
            } else if(data.validation){
              Swal.fire({title: "Gagal", text: data.validation, icon: "warning"});
            } else if(data.error) {
              Swal.fire({title: "Gagal", text: data.error, icon: "error"});
            } else {
              console.log(data);
            }
          })
        }
      });
    }
  </script>
@endsection