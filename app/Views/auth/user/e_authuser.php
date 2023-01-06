<div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Default Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="user/<?= $datalama->id ?>" method="post" id="simpan">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PATCH">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?= $datalama->email ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?= $datalama->username ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pass_confirm">Repeat Password</label>
                                <input type="password" class="form-control" name="pass_confirm" id="pass_confirm" placeholder="Repeat Password">
                                <div class="invalid-feedback"></div>
                                <input type="hidden" name="pass_confirmold" id="pass_confirmold" value="<?= $datalama->password_hash ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="active" name="active" <?= $datalama->active == 1 ? 'checked' : null ?> value="<?= $datalama->active ?>">
                                    <label class="custom-control-label" for="active" id="labelcheck"><?= $datalama->active == 1 ? 'Active' : 'Non Active' ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm btn-save"><i class="fas fa-save"></i> Update</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa-solid fa-circle-xmark"></i> Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#simpan').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: "post",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function(e) {
                    $('.btn-save').prop('disabled', true);
                    $('.btn-save').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                complete: function() {
                    $('.btn-save').prop('disabled', false);
                    $('.btn-save').html('<i class="fas fa-save"></i> Update')
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
                        $('#modal-edit').modal('hide')
                        $('input[name=csrfToken]').val(response.csrfToken)
                        reloadTable();
                    } else {
                        $.each(response.errors, function(key, value) {
                            $('[name="' + key + '"]').addClass('is-invalid')
                            $('[name="' + key + '"]').next().text(value)
                            if (value === '') {
                                $('[name="' + key + '"]').removeClass('is-invalid')
                            }
                        })
                        // $('input[name=csrfToken]').val(response.csrfToken)
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            });
            return false;
        });
    });
    $('#active').click(function() {
        $('#active').prop('checked') ? $('#labelcheck').text('Active') : $('#labelcheck').text('Non Active');
        $("#active").prop('checked') ? $('#active').val('1') : $('#active').val('0')
    })
</script>