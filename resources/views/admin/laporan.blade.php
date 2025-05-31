@extends('admin.home')

@section('style')
    <style>
        table.dataTable td {
          white-space: nowrap;
        }
    </style>
@endsection

@section('content')
  <div class="panel-header bg-primary-gradient">
    <div class="page-inner py-5">
      <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row">
        <div>
          <h2 class="text-white pb-2 fw-bold">Riwayat</h2>
          <h5 class="text-white op-7 mb-2">Halaman Riwayat Permohonan</h5>
        </div>
      </div>
    </div>
  </div>
  <div class="page-inner mt--5">
    <div class="row mt--2">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Filter Permohonan</h4>
            <form action="{{ route('admin.dtlaporan') }}" method="GET" class="form-inline ml-auto">
              <div class="form-group mx-sm-2">
                <label for="start_date" class="mr-1">Mulai :</label>
                <input type="date" class="form-control form-control-sm" name="start_date">
              </div>
              <div class="form-group mx-sm-2">
                <label for="end_date" class="mr-1">Akhir :</label>
                <input type="date" class="form-control form-control-sm" name="end_date">
              </div>
              <div class="form-group mx-sm-2">
                <label for="" class="sr-only">Status</label>
                <select name="status" id="" class="form-control form-control-sm">
                  <option value="">Semua status</option>
                  <option value="success">Success</option>
                  <option value="pending">Pending</option>
                  <option value="expired">Expired</option>
                </select>
              </div>
              <div class="form-group mx-sm-2">
                <label for="" class="sr-only">Metode</label>
                <select name="metode" id="" class="form-control form-control-sm">
                  <option value="">Semua metode</option>
                  <option value="tf_ambil">Transfer Bank & Diambil</option>
                  <option value="tf_kirim">Transfer Bank & Dikirim</option>
                  <option value="pg_ambil">Payment Gateway & Diambil</option>
                  <option value="pg_kirim">Payment Gateway & Dikirim</option>
                </select>
              </div>
              <button type="button" id="filterBtn" class="btn btn-sm btn-primary ml-sm-2">Filter</button>
              <button type="button" class="btn btn-sm btn-secondary ml-sm-2" onclick="resetData()">Reset</button>
            </form><br><br>
            <div class="table-responsive">
              <table id="laporantbl" class="display table table-striped table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Tanggal Permohonan</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Jumlah dokumen</th>
                    <th>Total cetak</th>
                    <th>Jumlah bayar</th>
                    <th>Metode pembayaran</th>
                    <th>Metode pengiriman</th>
                    <th>Biaya kurir</th>
                    <th>Status pembayaran</th>
                    <th>Invoice</th>
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
    document.addEventListener("DOMContentLoaded", function(){
      $("#filterBtn").on('click', function() {
        table.ajax.reload();
      })
    });

    table = $("#laporantbl").DataTable({
      processing: true,
      ajax: {
        url: "{{ route('admin.dtlaporan') }}",
        data: function(d) {
          d.start_date = $("input[name=start_date]").val();
          d.end_date = $("input[name=end_date]").val();
          d.status = $("select[name=status]").val();
          d.metode = $("select[name=metode]").val();
        },
        dataSrc: ""
      },
      dom: '<"row mb-2"<"col-md-6"B><"col-md-6"f>>' +
       '<"row"<"col-sm-12"tr>>' +                 
       '<"row mt-2"<"col-md-6"l><"col-md-6"p>>',
      buttons: [
        {
            extend: 'excelHtml5',
            title: 'Data Permohonan',
            text: '<i class="fas fa-file-excel"></i> Export Excel',
            className: 'btn btn-success text-white',
            exportOptions: {
                columns: ':visible',
                format: {
                    body: function (data, row, column, node) {
                      if (column === 0) {
                        return row + 1;
                      }
                      
                        if (column === 12) {
                            const tempDiv = document.createElement("div");
                            tempDiv.innerHTML = data;
                            const link = tempDiv.querySelector("a");
                            return link ? link.href : '';
                        }
                      // Kalau data object, hilangkan (misalnya ada object dari kolom lain)
                      return typeof data === 'object' ? '' : data;
                    }
                }
            }
        }
      ],
      order: [1, 'desc'],
      columns: [
        {data: null},
        {data: 'tanggal_permohonan'},
        {data: 'nim'},
        {data: 'nama'},
        {data: 'prodi'},
        {data: 'jumlah_dokumen'},
        {data: 'total_cetak'},
        {data: 'jumlah_bayar'},
        {data: 'metode_pembayaran'},
        {data: 'metode_pengiriman'},
        {data: 'biaya_kurir'},
        {data: 'status_pembayaran'},
        {
          data: null,
          render: function(data, type, row){
            const url = "{{ route('admin.unduhinvoice', ['id' => ':id', 'status' => ':status']) }}".replace(':id', data.permohonan_id).replace(':status', data.status_pembayaran);              
            return `
                <div class="form-button-action">
                    <a href="${url}" data-toggle="tooltip" title="Download" class="btn btn-link btn-primary btn-lg">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            `;
          }
        }
      ],
      rowCallback: function(row, data, index){
        var pageInfo = this.api().page.info();
        $('td:eq(0)', row).html(index + 1 + pageInfo.start);
      }
    });
  </script>
@endsection