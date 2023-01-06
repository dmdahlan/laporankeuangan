<div class="modal fade" id="modalMenu">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Role : <?= $namarole ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="roleid" value="<?= $role->id ?>">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline">
                            <div class="card-body">
                                <table id="tableMenu" class="table md-table table-sm table-bordered table-hover table-striped nowrap cell-border">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>MENU</th>
                                            <th>PARENT</th>
                                            <th>URL</th>
                                            <th>JENIS</th>
                                            <th>STATUS</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        data();
    });

    function data() {
        table = $('#tableMenu').DataTable({
            "processing": true,
            "serverSide": true,
            "lengthMenu": [10, 25, 50, 100],
            "order": [1, 'asc'],
            "ajax": {
                "url": 'role/listmenu',
                "method": 'post',
                "data": function(data) {
                    data.roleId = $('#roleid').val();
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
                    data: 'action',
                    orderable: false
                },
            ],
        })
    }

    $('body').on('click', '#btn-akses', function(e) {
        e.preventDefault()
        const id = $(this).data('id')
        const roleId = $(this).data('roleid')
        $.ajax({
            type: "post",
            url: "role/changeAccess",
            data: {
                csrfToken: $('input[name=csrfToken]').val(),
                menuId: id,
                roleId: roleId,
            },
            dataType: "json",
            success: function(response) {
                if (response.ok) {
                    Swal.fire({
                        icon: 'success',
                        title: `<strong>Akses berhasil diubah</strong>`,
                        showConfirmButton: false,
                        timer: 1000,
                        timerProgressBar: true,
                    })
                    $('input[name=csrfToken]').val(response.csrfToken)
                    reloadTable();
                }
            },
            error: function(xhr, thrownError) {
                alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
            }
        })
    })
</script>