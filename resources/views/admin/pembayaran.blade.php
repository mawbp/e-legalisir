@extends('admin.dashboard')

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Data Pembayaran</h2>
          <h5 class="text-white op-7 mb-2">Halaman pengelolaan pembayaran</h5>
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
              <button class="btn btn-primary" data-toggle="modal" data-target="#modaltambah" data-backdrop="static" data-keyboard="false">Tambah data</button>
              <a href="#" target="_blank" class="btn btn-info" style="color: white">Cetak PDF Statis</a>
              <a href="#" target="_blank" class="btn btn-danger" style="color: white">Cetak PDF Dinamis</a>
              <a href="#" target="_blank" class="btn btn-success" style="color: white">Cetak Excel Statis</a>
              <a href="#" target="_blank" class="btn btn-warning" style="color: white">Cetak Excel Dinamis</a>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table id="pembayarantbl" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th style="width: 25%">Tanggal Permohonan</th>
                    <th style="width: 5%">Metode Pembayaran</th>
                    <th style="width: 5%">Jumlah Biaya</th>
                    <th style="width: 10%">Status Pembayaran</th>
                    <th style="width: 10%">Bukti</th>
                    <th style="width: 20%">Operasi</th>
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
    table = $("#pembayarantbl").DataTable({
      ajax: {
        url: "{{ route('admin.dtpembayaran') }}",
        dataSrc: ""
      },
      columns: [
        {
          data: 'created_at',
          className: 'text-start',
          render: function(data){
            let date = new Date(data);
            return date.getFullYear() + '-' + 
            String(date.getMonth() + 1).padStart(2, '0') + '-' +
            String(date.getDate()).padStart(2, '0') + ' ' +
            String(date.getHours()).padStart(2, '0') + ':' +
            String(date.getMinutes()).padStart(2, '0') + ':' +
            String(date.getSeconds()).padStart(2, '0');
          }
        },
        {data: 'metode_pembayaran'},
        {data: 'jumlah_bayar'},
        {data: 'status_pembayaran'},
        {data: 'bukti_pembayaran'},
        {
          data: 'id',
          render: function(data){
            return `<button onclick='updateStatus(${data})' class='btn btn-primary' style='padding: 10px'><i class='fas fa-edit'></i> Update</button>`
          }
        }
      ],
    });
  </script>
@endsection