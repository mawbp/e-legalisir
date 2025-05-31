@extends('user.home')


@section('style')
  <style>
    /* .dataTables_filter, .dataTables_paginate {
      justify-content: flex-end !important;
      display: flex !important;
    }
    .dataTables_length {
      display: flex !important;
      align-items: center !important;
      gap: 10px;
    }
    .dataTables_length label {
      display: flex; 
      align-items: center;
      gap: 10px;
    }
    table.dataTable {
      margin-top: 15px;
    } */
  </style>
@endsection

@section('content')
<!-- Page Heading -->
  <h1 class="h3 mb-2 text-gray-800">Riwayat Permohonan</h1>
  <p class="mb-4">Menampilkan seluruh riwayat permohonan yang telah anda ajukan.</p>
  <!-- DataTales Example -->
  <div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered" id="example" width="100%">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal Permohonan</th>
                  <th>Jenis Dokumen</th>
                  <th>Metode Pengiriman</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>No</th>
                  <th>Tanggal Permohonan</th>
                  <th>Jenis Dokumen</th>
                  <th>Metode Pengiriman</th>
                  <th>Status</th>
                </tr>
              </tfoot>
          </table>
        </div>
    </div>
  </div>
<!-- <table id="example" class="table table-striped table-hovered nowrap mt-3" style="width: 100%">
  <thead>
    <tr>
      <th>No</th>
      <th>Tanggal Permohonan</th>
      <th>Jenis Dokumen</th>
      <th>Metode Pengambilan</th>
      <th>Status</th>
    </tr>
  </thead>
</table> -->
@endsection

@section('script')
  <script>
    let table;
    table = $("#example").DataTable({
      responsive: true,
      ajax: {
        url: "{{ route('user.dtriwayat') }}",
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
        {data: 'dokumen.nama_dokumen'},
        {data: 'pembayaran.metode_pengiriman'},
        {
          data: 'status_permohonan',
          render: function(data){
            if(data == "Selesai"){
              return `<span class="badge badge-success" style="color: black">${data}</span>`;
            } else {
              return `<span class="badge badge-warning" style="color: black">${data}</span>`;
            }
          }
        },
        // {
        //   data: 'nim',
        //   render: function(data){
        //     return `<button onclick='getPegawai(${data})' class='btn btn-primary' style='padding: 10px'><i class='fas fa-edit'></i> Edit</button>
        //             <button onclick='deletePegawai(${data})' class='btn btn-danger' style='padding: 10px'><i class='fas fa-trash-alt'></i> Hapus</button>`
        //   }
        // }
      ],
    });
  </script>
@endsection
