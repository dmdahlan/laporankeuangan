<?= $this->extend('template/main') ?>
<?= $this->section('content') ?>
<style>
    table.dataTable tr {
        font-size: 0.9rem;
    }

    table.dataTable td {
        font-size: 0.9rem;
    }
</style>
<div class="content-wrapper mt-5">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h5 class="m-0">Bukubesar Perdana</h5>
                </div>
            </div>
        </div>
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-1">
                                    <input type="text" id="tglawal" placeholder="Tgl awal" class="form-control tanggal form-control-sm" type="text" autocomplete="off">
                                </div>
                                <div class="col-1">
                                    <input type="text" id="tglakhir" placeholder="Tgl akhir" class="form-control tanggal form-control-sm" type="text" autocomplete="off">
                                </div>
                                <div class="col-4">
                                    <select name="noakun" id="noakun" class="form-control form-control-sm">
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="text" name="uraian" id="uraian" class="form-control form-control-sm" placeholder="Uraian">
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-primary rounded-circle btn-sm" onclick="report()"><i class="fa fa-search"></i></button>
                                    <button class="btn btn-primary btn-sm rounded-circle" id="btn-refresh"><i class="fas fa-sync-alt"></i></button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div id="tabel-div" hidden class="card-body table-responsive">
                            <table id="table" class="table md-table table-sm table-bordered table-hover table-striped nowrap cell-border">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>TANGGAL</th>
                                        <th>KODE PEMBANTU</th>
                                        <th>URAIAN</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal -->
<!-- End Modal -->
<?= $this->endsection() ?>
<!-- Javascript -->
<?= $this->section('pageScripts') ?>
<script>
    // $('#btn-filtere').on('click', function(e) {
    //     e.preventDefault()
    //     $('.card-body').prop('hidden', true);
    //     $('#tabel').DataTable().clear().destroy()
    //     table = $('#table').DataTable({
    //         "processing": true,
    //         "serverSide": true,
    //         "lengthMenu": [10, 50, 100],
    //         "order": [1, 'desc'],
    //         "ajax": {
    //             "url": 'bbpsp/data',
    //             "method": 'post',
    //             "data": function(data) {
    //                 data.csrfToken = $('input[name=csrfToken]').val()
    //             },
    //             "dataSrc": function(response) {
    //                 $('input[name=csrfToken]').val(response.csrfToken)
    //                 return response.data;
    //             },
    //         },
    //         "columns": [{
    //                 data: 'no',
    //                 orderable: false
    //             },
    //             {
    //                 data: 'tgl_transaksi',
    //             },
    //             {
    //                 data: 'no_bukti',
    //             },
    //             {
    //                 data: 'uraian',
    //             },
    //             {
    //                 data: 'debet',
    //             },
    //             {
    //                 data: 'kredit',
    //             },
    //             {
    //                 data: 'debet',
    //             },
    //         ],
    //     })
    //     $('.card-body').prop('hidden', false)
    // })

    function report() {
        $('#tabel-div').prop('hidden', true);
        $('#tabel').DataTable().clear().destroy()
        var table
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "lengthMenu": [10, 50, 100],
            "order": [1, 'desc'],
            "ajax": {
                "url": 'bbpsp/data',
                "method": 'post',
                "data": function(data) {
                    data.csrfToken = $('input[name=csrfToken]').val()
                },
                "dataSrc": function(response) {
                    $('input[name=csrfToken]').val(response.csrfToken)
                    return response.data;
                },
            },
            "columns": [{
                    data: 'no',
                    orderable: false
                },
                {
                    data: 'tgl_transaksi',
                },
                {
                    data: 'no_bukti',
                },
                {
                    data: 'uraian',
                },
                {
                    data: 'debet',
                },
                {
                    data: 'kredit',
                },
                {
                    data: 'debet',
                },
            ],
        })
        $('#tabel-div').prop('hidden', false);
    }

    function reloadTable() {
        table.ajax.reload(null, false)
    }
    $('#btn-refresh').on('click', function(e) {
        table.ajax.reload(null, false)
    })
    $('.tanggal').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-M-yyyy"
    });

    var tanggal = new Date();
    var tahunawal = tanggal.getFullYear();
    tahunawal = "01-01-" + tahunawal;
    var tahunakhir = tanggal.getFullYear();
    tahunakhir = "31-12-" + tahunakhir;
    $("#tglawal").datepicker("setDate", tahunawal);
    $("#tglakhir").datepicker("setDate", tahunakhir);
</script>
<?= $this->endsection() ?>
<!-- CSS -->
<?= $this->section('css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<!-- Datepicker -->
<link rel="stylesheet" href="/assets/adminlte/plugins/bootstrap-datepicker-1.9.0/css/bootstrap-datepicker3.css">
<?= $this->endsection() ?>
<!-- JS -->
<?= $this->section('js') ?>
<!-- DataTables  & Plugins -->
<script src="/assets/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- Datepicker -->
<script src="/assets/adminlte/plugins/bootstrap-datepicker-1.9.0/js/bootstrap-datepicker.js"></script>
<?= $this->endsection() ?>