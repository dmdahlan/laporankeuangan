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
                    <h5 class="m-0">Buku Besar Perdana</h5>
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
                                    <select name="noakun" id="noakun" class="form-control form-control-sm"></select>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control form-control-sm" name="saldoawal" id="saldoawal" placeholder="Saldo Awal" readonly>
                                </div>
                                <div class="col-2">
                                    <input type="text" class="form-control form-control-sm" name="saldoakhir" id="saldoakhir" placeholder="Saldo Akhir" readonly>
                                </div>
                                <div class="col-1">
                                    <button class="btn btn-primary rounded-circle btn-sm" id="btn-filter"><i class="fa fa-search"></i></button>
                                    <!-- <button class="btn btn-primary btn-sm rounded-circle" id="btn-refresh"><i class="fas fa-sync-alt"></i></button> -->
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div hidden class="card-body table-responsive">
                            <table id="table" class="table md-table table-sm table-bordered table-hover table-striped nowrap cell-border" style="width: 100%;">
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
    $(document).ready(function() {
        akun()
    });
    $('#btn-filter').on('click', function(e) {
        e.preventDefault()
        if ($('#noakun').val() == null) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Nomor akun tidak boleh kosong',
            })
        } else {
            saldoAwal()
            saldoAkhir()
            $('.card-body').prop('hidden', true);
            $('#table').DataTable().clear().destroy()
            table = $('#table').DataTable({
                "processing": true,
                "serverSide": true,
                "scrollY": "300px",
                "scrollX": true,
                "scrollCollapse": true,
                "lengthMenu": [10, 50, 1000],
                "order": [1, 'asc'],
                "ajax": {
                    "url": 'bbpsp/data',
                    "method": 'post',
                    "data": function(data) {
                        data.csrfToken = $('input[name=csrfToken]').val()
                        data.noakun = $('#noakun').val()
                        data.tglawal = $('#tglawal').val()
                        data.tglakhir = $('#tglakhir').val()
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
                        data: 'saldo',
                    },
                ],
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
    $('.tanggal').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-M-yyyy"
    })

    function akun() {
        $('#noakun').select3({
            theme: "bootstrap5",
            minimumInputLength: 3,
            allowClear: true,
            placeholder: "Pilih Nomor Akun",
            ajax: {
                dataType: "json",
                url: "/noakunpsp",
                delay: 500,
                type: "post",
                data: function(params) {
                    return {
                        search: params.term
                    }
                },
                processResults: function(data, page) {
                    return {
                        results: data
                    };
                },
            }
        });
    }

    function saldoAwal() {
        const noakun = $('#noakun').val()
        $.ajax({
            type: "post",
            url: "bbpsp/saldoawal",
            data: {
                csrfToken: $('input[name=csrfToken]').val(),
                noakun: $('#noakun').val()
            },
            dataType: "json",
            success: function(response) {
                if (response.saldo) {
                    $('#saldoawal').val(rupiah(response.saldo.saldo_awal))
                    $('input[name=csrfToken]').val(response.csrfToken)
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        })
    }

    function saldoAkhir() {
        const noakun = $('#noakun').val()
        $.ajax({
            type: "post",
            url: "bbpsp/saldoakhir",
            data: {
                csrfToken: $('input[name=csrfToken]').val(),
                noakun: $('#noakun').val()
            },
            dataType: "json",
            success: function(response) {
                if (response.saldo) {
                    $('#saldoakhir').val(rupiah(response.saldo))
                    $('input[name=csrfToken]').val(response.csrfToken)
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        })
    }
    var tanggal = new Date();
    var tahunawal = tanggal.getFullYear();
    tahunawal = "01-01-" + tahunawal;
    var tahunakhir = tanggal.getFullYear();
    tahunakhir = "31-12-" + tahunakhir;
    $("#tglawal").datepicker("setDate", tahunawal);
    $("#tglakhir").datepicker("setDate", tahunakhir);

    function rupiah(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
    }
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