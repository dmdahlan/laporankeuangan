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
                    <h5 class="m-0">Manajemen Menu</h5>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-12">
                                    <button class="btn btn-success btn-sm rounded-circle" id="btn-new"><i class="fas fa-plus"></i></button>
                                    <button class="btn btn-primary btn-sm rounded-circle" id="btn-refresh"><i class="fas fa-sync-alt"></i></button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="table" class="table md-table table-sm table-bordered table-hover table-striped nowrap cell-border">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>NAME MENU</th>
                                        <th>MENU</th>
                                        <th>PARENT</th>
                                        <th>URL</th>
                                        <th>JENIS</th>
                                        <th>STATUS</th>
                                        <th>SORT</th>
                                        <th>OPSI</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- Modal -->
<div id="viewmodal" style="display: none"></div>
<!-- End Modal -->
<?= $this->endsection() ?>
<!-- Javascript -->
<?= $this->section('pageScripts') ?>
<script>
    // $.ajaxSetup({
    //     headers: {
    //         'X-CSRF-TOKEN': $('meta[name="csrfToken"]').attr('content')
    //     }
    // })
    $(document).ready(function() {
        data();
    });

    function data() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "lengthMenu": [10, 25, 50, 100],
            "order": [1, 'asc'],
            "ajax": {
                "url": 'menu/data',
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
                    data: 'description',
                },
                {
                    data: 'name',
                },
                {
                    data: 'menuid',
                },
                {
                    data: 'url',
                },
                {
                    data: 'jns_menu',
                },
                {
                    data: 'status',
                    orderable: false,
                },
                {
                    data: 'sort_menu',
                },
                {
                    data: 'action',
                    orderable: false
                },
            ],
        })
    }

    function reloadTable() {
        table.ajax.reload(null, false)
    }
    $('#btn-refresh').on('click', function(e) {
        e.preventDefault()
        reloadTable()
    })

    $('#btn-new').on('click', function(e) {
        e.preventDefault()
        $.ajax({
            method: "get",
            url: "menu/new",
            dataType: "json",
            success: function(response) {
                if (response.ok) {
                    $('#viewmodal').html(response.ok).show()
                    $('#modal-create').modal('show')
                    $('.modal-title').text('Tambah Menu')
                    $('.select2').select2({
                        theme: "bootstrap4"
                    })
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError)
            }
        })
    })

    $('body').on('click', '#btn-edit', function(e) {
        e.preventDefault()
        const id = $(this).data('id')
        $.ajax({
            type: "get",
            url: "menu/" + id + "/edit",
            dataType: "json",
            success: function(response) {
                if (response.ok) {
                    $('#viewmodal').html(response.ok).show()
                    $('#modal-edit').modal('show')
                    $('.modal-title').text('Edit Menu')
                    $('.select2').select2({
                        theme: "bootstrap4"
                    })
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        })
    })

    $('body').on('click', '#btn-delete', function(e) {
        e.preventDefault()
        const id = $(this).data('id')
        const ket = $(this).data('ket')
        Swal.fire({
            title: `${ket} <small>akan dihapus ?</small>`,
            html: 'Data yang terhapus tidak dapat dikembalikan lagi',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "menu/" + id,
                    data: {
                        csrfToken: $('input[name=csrfToken]').val(),
                        _method: "delete",
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.ok) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                html: `<strong>${ket}</strong> ${response.ok}`,
                            })
                            $('input[name=csrfToken]').val(response.csrfToken)
                            reloadTable();
                        }
                    },
                    error: function(xhr, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            } else {
                swal.fire("Batal", "Data batal dihapus", "warning");
            }
        })
    })
</script>
<?= $this->endsection() ?>
<!-- CSS -->
<?= $this->section('css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<!-- Select2 -->
<link rel="stylesheet" href="/assets/adminlte/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="/assets/adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
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
<!-- Select2 -->
<script src="/assets/adminlte/plugins/select2/js/select2.full.min.js"></script>
<!-- SweetAlert -->
<script src="/assets/adminlte/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
<?= $this->endsection() ?>