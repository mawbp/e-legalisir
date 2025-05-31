@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Data Alumni</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengelolaan alumni</h5>
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
              <button class="btn btn-secondary" data-toggle="modal" data-target="#importmodal" data-backdrop="static" data-keyboard="false">Impor data</button>
              <button class="btn btn-info" data-toggle="modal" data-target="#scanmodal" data-backdrop="static" data-keyboard="false">Upload scan dokumen</button>
            </div>
            <div id="importErrorsAlert" class="alert alert-danger mt-3 d-none">
              <h5 id="errorHeader"></h5>
              <ul id="errorList" class="mb-0"></ul>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="alumnitbl" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Tahun Angkatan</th>
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
          <h5 class="modal-title" style="font-size: 20px;">Tambah Data Alumni</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="form-group col-md-12">
              <label>NIM</label>
              <input type="text" class="form-control fadd" id="nim" placeholder="Masukkan NIM" required="">
            </div>
            <div class="form-group col-md-12">
              <label>Nama Mahasiswa</label>
              <input type="text" class="form-control fadd" id="nama"  placeholder="Masukkan Nama" required="">
            </div>
            <div class="form-group col-md-12">
              <label>Prodi</label>
              <select class="form-control ftambah" id="prodi" required="">
                <option value="">Pilih salah satu</option>
                @foreach($prodi as $p)
                    <option value="{{ $p->nama_prodi }}">{{ $p->nama_prodi }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-12">
              <label>Tahun Angkatan</label>
              <input type="text" class="form-control fadd" id="angkatan"  placeholder="Masukkan Tahun Angkatan" required="">
            </div>
            @foreach($dokumen as $d)
                @if($d->pemilik == "Alumni")
                    <div class="form-group col-md-12">
                      <label>Scan {{ $d->nama_dokumen }}</label>
                      <input type="file" class="form-control fadd" id="{{ "scan_" . Str::slug($d->nama_dokumen, '_') }}" required="">
                    </div>
                @endif
            @endforeach
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
              <a href="{{ route('admin.unduh') }}" style="color: white" class="btn btn-primary">Unduh template</a>
            </div>
            <div class="form-group col-md-12">
              <label>File</label>
              <input type="file" class="form-control" id="file" required="">
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
                @foreach($prodi as $p)
                    <option value="{{ $p->nama_prodi }}">{{ $p->nama_prodi }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-12">
              <label>Tahun Angkatan</label>
              <input type="text" class="form-control fadd" id="angkatane" required="">
            </div>
            @foreach($dokumen as $d)
                @if($d->pemilik == "Alumni")
                    <div class="form-group col-md-12 {{ "scane_" . Str::slug($d->nama_dokumen, '_') }}">
                      <label>Scan {{ $d->nama_dokumen }}</label>
                      <input type="file" class="form-control fadd input-berkas" id="{{ "scane_" . Str::slug($d->nama_dokumen, '_') }}" required="">
                    </div>
                @endif
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="updateBtn" class="btn btn-primary" onclick="updateAlumni()">Simpan</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" role="dialog" id="scanmodal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" style="font-size: 20px;">Upload Scan Dokumen Alumni</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <p><strong>Instruksi: </strong></p>
            <ul>
                <li>Seluruh file harus menggunakan NIM sebagai nama, contoh: <code>4121017.jpg</code></li>
                <li>Kompres seluruh file dalam format <strong>.zip</strong></li>
            </ul>
          <div class="row">
            <div class="form-group col-md-12">
              <label>Dokumen</label>
              <select class="form-control ftambah" id="dokumenscan" required="">
                <option value="">Pilih salah satu</option>
                @foreach($dokumen as $d)
                    @if($d->pemilik == "Alumni")
                        <option value="{{ Str::slug($d->nama_dokumen, '_') }}">{{ $d->nama_dokumen }}</option>
                    @endif
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-12">
              <label>Upload Scan Dokumen</label>
              <input type="file" class="form-control fadd" id="scanzip" required="">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="uploadBtn" class="btn btn-primary" onclick="uploadScan()">Simpan</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
  <script>
    let table;
    table = $("#alumnitbl").DataTable({
      ajax: {
        url: "{{ route('alumni.index') }}",
        dataSrc: ""
      },
      columns: [
        {
        data: null,
        render: function(data, type, row, meta){
          return meta.row + 1;
        }
        },
        {data: 'nim'},
        {data: 'nama'},
        {data: 'prodi'},
        {data: 'tahun_angkatan'},
        {
          data: 'id',
          render: function(data){
            return `
                <div class="form-button-action">
                    <button type="button" data-toggle="tooltip" title="Lihat Detail" class="btn btn-link btn-primary btn-lg" onclick="tampilAlumni(${data})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" data-toggle="tooltip" title="Hapus" class="btn btn-link btn-danger" onclick="hapusAlumni(${data})">
                        <i class="fas fa-trash"></i>
                    </button>
              </div>
            `;
          }
        }
      ],
    });
    
    function slugify(text, separator = '-') {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, separator)        
            .replace(/[^\w\-]+/g, '')          
            .replace(/\-\-+/g, separator);     
    }
    
    function uploadScan(){
        $("#uploadBtn").prop("disabled", true);
        $("#uploadBtn").text("Loading..");
        const dokumen = $("#dokumenscan").val();
        let formData = new FormData();
	    formData.append('dokumen', dokumen);
	    formData.append('file', $("#scanzip")[0].files[0]);
	    
	    fetch("{{ route('admin.uploadscan') }}", {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
          })
          .then(res => res.json())
          .then(data => {
              $("#uploadBtn").prop("disabled", false);
              $("#uploadBtn").text("Simpan");
              if(data.success){
                  Swal.fire(data.success, "", "success");
                  table.ajax.reload();
                  resetForm();
              } else {
                  Swal.fire("Gagal", "Terjadi kesalahan", "error");
              }
          })
    }

    function tambahAlumni(){
        $("#addBtn").prop("disabled", true);
        $("#addBtn").text("Loading..");
        let nim = $("#nim").val();
        let nama = $("#nama").val();
        let prodi = $("#prodi").val();
        let angkatan = $("#angkatan").val();
        
        const dokumen = @json($dokumen);
        const formData = new FormData();
        formData.append('nim', nim);
        formData.append('nama', nama);
        formData.append('prodi', prodi);
        formData.append('angkatan', angkatan);
        dokumen.forEach(d => {
            const slug = slugify(d.nama_dokumen, '_');
            const inputId = "scan_" + slug;
    
            const fileInput = document.getElementById(inputId);
            if (fileInput && fileInput.files.length > 0) {
                formData.append(inputId, fileInput.files[0]);
            }
        });
    
      fetch("{{ route('alumni.store') }}", {
        method: "POST",
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        $("#addBtn").prop("disabled", false);
        $("#addBtn").text("Simpan");
        if(data.success){
          Swal.fire({title: "Berhasil", text: data.success, icon: "success"});
          table.ajax.reload();
          resetForm();
        } else if(data.validation){
          Swal.fire({title: "Gagal", text: data.validation, icon: "warning"});
          resetForm();
        } else if(data.error) {
          Swal.fire({title: "Gagal", text: data.error, icon: "error"});
        } else {
          console.log(data);
        }
      });
    }

    function resetForm(){
      $(".form-control").val("");
    }

    function tampilAlumni(id){
      $(".info-berkas").remove();
      $(".input-berkas").removeClass('d-none');
      fetch("{{ route('alumni.show', ':id') }}".replace(":id", id), {
        method: "GET",
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
      })
      .then(res => res.json())
      .then(data => {
        $("#ide").val(data.data.id);
        $("#nime").val(data.data.nim);
        $("#namae").val(data.data.nama);
        $("#prodie").val(data.data.prodi);
        $("#angkatane").val(data.data.tahun_angkatan);
        const dokumen = data.dokumen;
        for (const [key, value] of Object.entries(dokumen)) {
            if (value === null) {
                console.log('');
            } else {
                $(`.scane_${key}`).append(
                    `
                        <a class="info-berkas"><a style="color: white; text-decoration: none" class="btn btn-primary info-berkas" role="button" href="${value}" target="_blank">Lihat Berkas</a>
                        
                    `
                );
                $(`#scane_${key}`).addClass('d-none');
            }
        }
        $("#updatemodal").modal("show");
      });
    }

    function updateAlumni(){
      $("#updateBtn").prop("disabled", true);
      $("#updateBtn").text("Loading..");
      let id = $("#ide").val();
      let nim = $("#nime").val();
      let nama = $("#namae").val();
      let prodi = $("#prodie").val();
      let angkatan = $("#angkatane").val();
      
      const dokumen = @json($dokumen);
      const formData = new FormData();
      formData.append('nim', nim);
      formData.append('nama', nama);
      formData.append('prodi', prodi);
      formData.append('angkatan', angkatan);
      formData.append('_method', 'PUT');
      dokumen.forEach(d => {
        const slug = slugify(d.nama_dokumen, '_');
        const inputId = "scane_" + slug;

        const fileInput = document.getElementById(inputId);
        if (fileInput && fileInput.files.length > 0) {
            formData.append(inputId, fileInput.files[0]);
        }
      });
	  
      if(id == "" || nim == "" || nama == "" || prodi == "" || angkatan == ""){
        Swal.fire({title: "Gagal", text: "Harap lengkapi seluruh kolom", icon: "error"});
      }
      fetch("{{ route('alumni.update', ':id') }}".replace(':id', id), {
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

    function hapusAlumni(id){
      Swal.fire({
        title: "Yakin ingin menghapus alumni ini?",
        showCancelButton: true,
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak"
      }).then((result) => {
        if(result.isConfirmed){
          fetch("{{ route('alumni.destroy', ':id') }}".replace(':id', id), {
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
          });
        }
      });
    }

    function imporAlumni() {
      let formData = new FormData();
      formData.append('file', $("#file")[0].files[0]);
      Swal.fire({
        title: 'Loading...',
        html: 'Sedang memproses data',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      fetch("{{ route('admin.impor') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if(data.validation){
          Swal.close();
          Swal.fire(data.validation, "", "warning");
          table.ajax.reload();
          resetForm();
          $("#importmodal").modal("hide");
        } else if(data.warning){
          let errors = data.errors;
          if(errors.length > 0){
            $("#errorList").html("");
            $("#importErrorsAlert").removeClass('d-none');
            $("#errorHeader").text(`Gagal mengimport ${errors.length} data:`);

            errors.forEach(err => {
              $("#errorList").append(`
                <li>
                  <strong>Baris ${err.row}:</strong>
                  ${err.message}
                  <br>
                  <small class="text-muted">
                    Data: ${JSON.stringify(err.data)}
                  </small>
                </li>
              `);
            });
          }
          Swal.close();
          Swal.fire(data.warning, "", "warning");
          table.ajax.reload();
          resetForm();
          $("#importmodal").modal("hide");
          console.log(data.dupe);
        } else if(data.success){
          Swal.close();
          Swal.fire(data.success, "", "success");
          table.ajax.reload();
          resetForm();
          $("#importmodal").modal("hide");
        }
      });
    }
  </script>
@endsection