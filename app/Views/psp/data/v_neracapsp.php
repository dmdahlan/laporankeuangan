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
                    <h5 class="m-0">Neraca Perdana</h5>
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
                                <div class="col-md-2">
                                    <input type="text" id="tahun" placeholder="Tahun" class="form-control tahun form-control-sm" type="text" autocomplete="off">
                                </div>
                                <div class="col-md">
                                    <button class="btn btn-primary rounded-circle btn-sm" id="btn-filter"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div hidden class="card-body table-responsive">
                            <table id="table" class="table md-table table-sm table-bordered table-hover table-striped nowrap cell-border" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>NO AKUN</th>
                                        <th>NAMA AKUN</th>
                                        <th>SALDO AWAL</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO JAN</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO FEB</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO MAR</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO APR</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO MEI</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO JUN</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO JUL</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO AGT</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO SEP</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO OKT</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO NOP</th>
                                        <th>DEBET</th>
                                        <th>KREDIT</th>
                                        <th>SALDO DES</th>
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
    $(document).ready(function() {});
    $('#btn-filter').on('click', function(e) {
        e.preventDefault()
        if ($('#tahun').val() == null) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tahun tidak boleh kosong',
            })
        } else {
            $('.card-body').prop('hidden', true);
            $('#table').DataTable().clear().destroy()
            table = $('#table').DataTable({
                "processing": true,
                "serverSide": true,
                "paging": false,
                "searching": false,
                "ordering": false,
                "info": false,
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "fixedColumns": {
                    "leftColumns": 2,
                },
                "ajax": {
                    "url": 'neracapsp/data',
                    "method": 'post',
                    "data": function(data) {
                        data.csrfToken = $('input[name=csrfToken]').val()
                        data.tahun = $('#tahun').val()
                    },
                    "dataSrc": function(response) {
                        $('input[name=csrfToken]').val(response.csrfToken)
                        return response.data;
                    },
                },
            })
            $('.card-body').prop('hidden', false)
        }
    })

    function reloadTable() {
        table.ajax.reload(null, false)
    }
    $('#btn-refresh').on('click', function(e) {
        table.ajax.reload(null, false)
    })
    $('#tahun').datepicker({
        startView: "years",
        minViewMode: "years",
        format: 'yyyy'
    }).on('change', function() {
        $('.datepicker').hide();
    });
    var date = new Date();
    var tahun_awal = date.getFullYear();
    tahun_awal = tahun_awal + "-01-01";

    function addDays(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
    }
    $("#tahun").datepicker("setDate", tahun_awal);
</script>
<?= $this->endsection() ?>
<!-- CSS -->
<?= $this->section('css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/adminlte/plugins/datatablefixed/css/fixedColumns.bootstrap4.min.css">
<!-- Datepicker -->
<link rel="stylesheet" href="/assets/adminlte/plugins/bootstrap-datepicker-1.9.0/css/bootstrap-datepicker3.css">
<!-- Select2 -->
<link rel="stylesheet" href="/assets/adminlte/plugins/select3/css/select3.min.css">
<link rel="stylesheet" href="/assets/adminlte/plugins/select3-bootstrap4-theme/select2-bootstrap5.min.css">
<!-- SweetAlert -->
<link rel="stylesheet" href="/assets/adminlte/plugins/sweetalert2/dist/sweetalert2.min.css">
<?= $this->endsection() ?>
<!-- JS -->
<?= $this->section('js') ?>
<!-- DataTables  & Plugins -->
<script src="/assets/adminlte/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="/assets/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="/assets/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="/assets/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="/assets/adminlte/plugins/datatablefixed/js/dataTables.fixedColumns.min.js"></script>
<!-- Datepicker -->
<script src="/assets/adminlte/plugins/bootstrap-datepicker-1.9.0/js/bootstrap-datepicker.js"></script>
<!-- Select2 -->
<script src="/assets/adminlte/plugins/select3/js/select3.full.min.js"></script>
<!-- SweetAlert -->
<script src="/assets/adminlte/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
<?= $this->endsection() ?>