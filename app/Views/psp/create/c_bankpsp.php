<div class="modal fade" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Default Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form enctype="multipart/form-data" action="bankpsp" method="post" id="simpan">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="file" class="form-control" name="file_excel" id="file_excel">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm btn-save">Import</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#simpan').submit(function(e) {
            e.preventDefault();
            let form = $('#simpan')[0];
            let data = new FormData(form);
            $.ajax({
                type: "post",
                url: $(this).attr('action'),
                data: data,
                dataType: "json",
                enctype: "mulitaprt/form-data",
                processData: false,
                contentType: false,
                cache: false,
                beforeSend: function(e) {
                    $('.btn-save').prop('disabled', true);
                    $('.btn-save').html('<i class="fa fa-spin fa-spinner"></i>');
                },
                complete: function() {
                    $('.btn-save').prop('disabled', false);
                    $('.btn-save').html('Import');
                },
                success: function(response) {
                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: `<strong>${response.ok}</strong>`,
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true,
                        })
                        $('#modal-create').modal('hide');
                        $('input[name=csrfToken]').val(response.csrfToken)
                        reloadTable();
                    } else {
                        for (let i = 0; i < response.name.length; i++) {
                            $('[name="' + response.name[i] + '"]').addClass('is-invalid');
                            $('[name="' + response.name[i] + '"]').next().text(response.errors[i]);
                        }
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
            return false;
        });
    })
</script>