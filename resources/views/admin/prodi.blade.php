@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Data Prodi</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengelolaan prodi</h5>
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
                    <th>Nama Prodi</th>
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
          <h5 class="modal-title" style="font-size: 20px;">Tambah Data Prodi</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-12">
              <label>Nama Prodi</label>
              <input type="text" class="form-control" id="nama" placeholder="Masukkan nama prodi">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="tambahProdi()">Simpan</button>
          <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" role="dialog" id="updatemodal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="font-size: 20px;">Update Data Prodi</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-12">
              <label>Nama Prodi</label>
              <input type="hidden" id="ide">
              <input type="text" class="form-control" id="namae" placeholder="Masukkan nama prodi">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="updateBtn" class="btn btn-primary" onclick="updateProdi()">Simpan</button>
          <button type="button" class="btn btn-danger" onclick="resetForm()">Reset</button>
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
        url: "{{ route('prodi.index') }}",
        dataSrc: ""
      },
      columns: [
        {
        data: null,
        render: function(data, type, row, meta){
          return meta.row + 1;
        }
        },
        {data: 'nama_prodi'},
        {
          data: 'id',
          render: function(data){
            return `
              <div class="form-button-action">
                <button type="button" data-toggle="tooltip" title="Lihat Detail" class="btn btn-link btn-primary btn-lg" onclick="tampilProdi(${data})">
                  <i class="fas fa-edit"></i>
                </button>
                <button type="button" data-toggle="tooltip" title="Hapus" class="btn btn-link btn-danger" onclick="hapusProdi(${data})">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            `;
          }
        }
      ],
    });

    function tambahProdi() {
      let nama = $("#nama").val();
      if(nama == ""){
        Swal.fire({title: "Gagal", text: "Harap lengkapi seluruh kolom", icon: "error"});
      } else {
        fetch("{{ route('prodi.store') }}", {
          method: "POST",
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({nama: nama})
        })
        .then(res => res.json())
        .then(data => {
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

    function tampilProdi(id){
      fetch("{{ route('prodi.show', ':id') }}".replace(":id", id), {
        method: "GET",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
      })
      .then(res => res.json())
      .then(data => {
        $("#ide").val(data.id);
        $("#namae").val(data.nama_dokumen);
        $("#updatemodal").modal("show");
      });
    }

    function updateProdi(){
      let id = $("#ide").val();
      let nama = $("#namae").val();
      if(id == "" || nama == ""){
        Swal.fire({title: "Gagal", text: "Harap lengkapi seluruh kolom", icon: "error"});
      } else {
        $("#updateBtn").prop("disabled", true);
        $("#updateBtn").text("Loading...");
        fetch("{{ route('prodi.update', ':id') }}".replace(':id', id), {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({nama: nama})
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

    function hapusProdi(id){
      Swal.fire({
        title: "Yakin ingin menghapus prodi ini?",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak"
      }).then((result) => {
        if(result.isConfirmed){  
          fetch("{{ route('prodi.destroy', ':id') }}".replace(':id', id), {
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