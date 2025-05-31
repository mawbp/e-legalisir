@extends('admin.home')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Data Pengumuman</h2>
          <h5 class="text-white op-7 mb-2">Halaman pembuatan pengumuman</h5>
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
              <a role="button" class="btn btn-primary text-white" href="{{ route('admin.buatpengumuman') }}">Buat pengumuman</a>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="dokumentbl" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Judul Pengumuman</th>
                    <th>Tanggal pengumuman</th>
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
        url: "{{ route('pengumuman.index') }}",
        dataSrc: ""
      },
      columns: [
        {
          data: null,
          render: function(data, type, row, meta){
            return meta.row + 1;
          }
        },
        {data: 'judul'},
        {
          data: 'created_at',
          render: function (data, type, row){
            const date = new Date(data);
            return date.toLocaleString('id-ID', {
              day: '2-digit',
              month: 'long',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
            });
          }
        },
        {
          data: 'id',
          render: function(data){
            let url = `{{ route('pengumuman.edit', ':id') }}`.replace(':id', data);
            return `
              <div class="form-button-action">
                <a href="${url}" data-toggle="tooltip" title="Lihat detail" class="btn btn-link btn-primary btn-lg" data-original-title="Approve">
                  <i class="fa fa-edit"></i>
                </a>
                <button type="button" onclick="hapus(${data})" data-toggle="tooltip" title="Hapus" class="btn btn-link btn-danger" data-original-title="Reject">
                  <i class="fa fa-times"></i>
                </button>
              </div>
            `;
          }
        }
      ],
    });
    
   function hapus(id) {
        const url = `{{ route('pengumuman.destroy', ':id') }}`.replace(':id', id);
    
        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire("Berhasil", data.message, "success").then(() => {
                    location.reload();
                });
            } else {
                Swal.fire("Gagal", data.message, "error");
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire("Error", "Terjadi kesalahan", "error");
        });
    }

  </script>
@endsection