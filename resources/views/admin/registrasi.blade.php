@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Data Registrasi</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengelolaan registrasi akun</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table id="dokumentbl" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>NIM</th>
                    <th>Nomor Ijazah</th>
                    <th>Status</th>
                    <th>tanggal</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
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
        url: "{{ route('admin.dtregis') }}",
        dataSrc: ""
      },
      order: [[3, 'desc']],
      columns: [
        {data: 'nim'},
        {data: 'nomor_ijazah'},
        {data: 'status'},
        {data: 'created_at', visible: false},
        {
          data: 'id',
          render: function(data, row){
            return `
              <div class="form-button-action">
                <button type="button" onclick="berkas('${data}')" data-toggle="tooltip" title="Lihat Berkas" class="btn btn-link btn-primary btn-lg">
                  <i class="fas fa-search"></i>
                </button>
                <button type="button" onclick="setuju(${data})" data-toggle="tooltip" title="Setujui" class="btn btn-link btn-success btn-lg">
                  <i class="fas fa-check"></i>
                </button>
                <button type="button" onclick="tolak(${data})" data-toggle="tooltip" title="Tolak" class="btn btn-link btn-danger">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            `;
          }
        }
      ],
    });
    
    function berkas(id){
        const url = "{{ route('admin.lihatberkas', ['id' => ':id']) }}".replace(':id', id);
        fetch(url)
            .then(res => res.json())
            .then(data => {
                if(data.success){
                    window.open(data.url, '_blank');
                } else {
                    Swal.fire("Berkas tidak ditemukan", "", "error");
                    table.ajax.reload();
                }
            });
    }

    function setuju(id){
      Swal.fire({
        title: 'Loading...',
        html: 'Sedang memproses data',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      const url = "{{ route('admin.setujuregis', ['id' => ':id']) }}".replace(':id', id);
      fetch(url)
        .then(res => res.json())
        .then(data => {
          if(data.success){
            Swal.close();
            Swal.fire("Registrasi berhasil disetujui", "", "success");
            table.ajax.reload();
          } else {
            Swal.close();
            Swal.fire("Terjadi kesalahan", "", "error");
            table.ajax.reload();
          }
        });
    }

    function tolak(id){
      Swal.fire({
        title: 'Loading...',
        html: 'Sedang memproses data',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      const url = "{{ route('admin.tolakregis', ['id' => ':id']) }}".replace(':id', id);
      fetch(url)
        .then(res => res.json())
        .then(data => {
          if(data.success){
            Swal.close();
            Swal.fire("Registrasi berhasil ditolak", "", "success");
            table.ajax.reload();
          } else {
            Swal.close();
            Swal.fire("Terjadi kesalahan", "", "error");
            table.ajax.reload();
          }
        });
    }
  </script>
@endsection