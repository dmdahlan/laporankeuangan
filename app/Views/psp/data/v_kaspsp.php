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
                    <h5 class="m-0">Kas Perdana</h5>
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
                                    <!-- <button class="btn btn-success btn-sm rounded-circle" onclick="create()"><i class="fas fa-plus"></i></button> -->
                                    <a type="button" class="btn btn-success btn-sm " id="btn-import"><i class="fas fa-file-excel"></i> Import</a>
                                    <button class="btn btn-danger btn-sm" id="btn-delete"><i class="fas fa-trash-alt"></i> Delete</button>
                                    <button class="btn btn-primary btn-sm" id="btn-refresh"><i class="fas fa-sync-alt"></i> Refresh</button>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive">
                            <table id="table" class="table md-table table-sm table-bordered table-hover table-striped nowrap cell-border">
                                <thead>
                                    <tr>
                                        <th>NO</th>
                                        <th>TANGGAL</th>
                                        <th>BKK/BKM</th>
                                        <th>URAIAN</th>
                                        <th>NO AKUN</th>
                                        <th>NAMA AKUN</th>
                                        <th>DEBET</th>
                                        <th>NO AKUN</th>
                                        <th>NAMA AKUN</th>
                                        <th>KREDIT</th>
                                        <!-- <th>
                                            <input type="checkbox" name="id_transaksi[]" id="checkall" style="margin-left: -3px;">
                                        </th> -->
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
<div class="viewmodal" style="display: none"></div>
<!-- End Modal -->
<?= $this->endsection() ?>
<!-- Javascript -->
<?= $this->section('pageScripts') ?>
<script>
    $(document).ready(function() {
        data()
    });

    function data() {
        table = $('#table').DataTable({
            "processing": true,
            "serverSide": true,
            "lengthMenu": [10, 50, 100],
            "order": [1, 'asc'],
            "ajax": {
                "url": 'kaspsp/data',
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
                    data: 'akun_debet',
                },
                {
                    data: 'nama_debet',
                },
                {
                    data: 'debet',
                },
                {
                    data: 'akun_kredit',
                },
                {
                    data: 'nama_kredit',
                },
                {
                    data: 'kredit',
                },
                // {
                //     data: 'action',
                //     orderable: false
                // },
            ],
        })
    }

    function reloadTable() {
        table.ajax.reload(null, false)
    }
    $('#btn-refresh').on('click', function(e) {
        table.ajax.reload(null, false)
        $('#checkall').prop('checked', false)
    })
    $('#btn-import').on('click', function(e) {
        e.preventDefault()
        $.ajax({
            method: "get",
            url: "kaspsp/new",
            dataType: "json",
            success: function(response) {
                if (response.ok) {
                    $('.viewmodal').html(response.ok).show()
                    $('#modal-create').modal('show')
                    $('.modal-title').text('Import Kas PSP')
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError)
            }
        })
    })

    function edit(id) {
        $.ajax({
            type: "get",
            url: "role/" + id + "/edit",
            dataType: "json",
            success: function(response) {
                if (response.ok) {
                    $('#viewmodal').html(response.ok).show()
                    $('#modal-edit').modal('show')
                    $('.modal-title').text('Edit Role')
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        });
    }
    $('#btn-delete').on('click', function(e) {
        e.preventDefault()
        Swal.fire({
            title: 'Are you sure?',
            text: 'Semua data kas akan dihapus',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "post",
                    url: "kaspsp/deleteall",
                    data: {
                        csrfToken: $('input[name=csrfToken]').val(),
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.ok) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                html: `${response.ok}`,
                            })
                            $('input[name=csrfToken]').val(response.csrfToken)
                            reloadTable();
                        }
                    },
                    error: function(xhr, thrownError) {
                        alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                    }
                });
            }
        })

        return false
    })
    // $('#checkall').on('click', function(e) {
    //     // e.preventDefault()
    //     if ($(this).is(':checked')) {
    //         $('.checklist').prop('checked', true)
    //     } else {
    //         $('.checklist').prop('checked', false)
    //     }
    // })
    // $('#btn-delete').on('click', function(e) {
    //     e.preventDefault()
    //     const totaldelete = $('.checklist:checked')
    //     if (totaldelete.length === 0) {
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Perhatian',
    //             text: 'Belum ada data yang dipilih'
    //         })
    //     } else {
    //         Swal.fire({
    //             title: 'Are you sure?',
    //             text: `${totaldelete.length} Data kas akan dihapus`,
    //             icon: 'warning',
    //             showCancelButton: true,
    //             confirmButtonColor: '#3085d6',
    //             cancelButtonColor: '#d33',
    //             confirmButtonText: 'Yes, delete it!'
    //         }).then((result) => {
    //             if (result.isConfirmed) {
    //                 $.ajax({
    //                     type: "post",
    //                     url: "kaspsp/" + totaldelete,
    //                     data: {
    //                         csrfToken: $('input[name=csrfToken]').val(),
    //                         _method: "delete",
    //                     },
    //                     dataType: "json",
    //                     success: function(response) {
    //                         if (response.ok) {
    //                             Swal.fire({
    //                                 icon: 'success',
    //                                 title: 'Success',
    //                                 html: `<strong>${name}</strong> ${response.ok}`,
    //                             })
    //                             $('input[name=csrfToken]').val(response.csrfToken)
    //                             reloadTable();
    //                         }
    //                     },
    //                     error: function(xhr, thrownError) {
    //                         alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
    //                     }
    //                 });
    //             }
    //         })
    //     }
    //     return false
    // })
</script>
<?= $this->endsection() ?>
<!-- CSS -->
<?= $this->section('css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="/assets/adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="/assets/adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
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
<!-- SweetAlert -->
<script src="/assets/adminlte/plugins/sweetalert2/dist/sweetalert2.min.js"></script>
<?= $this->endsection() ?>