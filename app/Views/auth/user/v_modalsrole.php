<div class="modal fade" id="modalrole">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Default Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="user/updaterole" method="post" id="simpan">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" name="id" value="<?= $datalama->user_id ?>">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" name="username" id="username" value="<?= $datalama->username ?>" disabled>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="email">Role</label>
                                <input type="text" class="form-control" name="email" id="email" value="<?= $datalama->email ?>" disabled>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Role</label>
                                <select id="group_id" name="group_id" class="form-control select2">
                                    <?php foreach ($role as $r) : ?>
                                        <?php if ($r->id === $datalama->group_id) : ?>
                                            <option value="<?= $r->id ?>" selected><?= $r->name ?></option>
                                        <?php else : ?>
                                            <option value="<?= $r->id ?>"><?= $r->name ?></option>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm btn-save">Update</button>
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
            $.ajax({
                type: "post",
                url: $(this).attr('action'),
                data: $(this).serialize(),
                dataType: "json",
                beforeSend: function(e) {
                    $('.btn-save').prop('disabled', true);
                    $('.btn-save').html('<i class="fa fa-spin fa-spinner"></i>');
                },
                complete: function() {
                    $('.btn-save').prop('disabled', false);
                    $('.btn-save').html('Update');
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
                        $('#modalrole').modal('hide')
                        $('input[name=csrfToken]').val(response.csrfToken)
                        reloadTable();
                    } else {
                        for (let i = 0; i < response.name.length; i++) {
                            $('[name="' + response.name[i] + '"]').addClass('is-invalid');
                            $('[name="' + response.name[i] + '"]').next().text(response.errors[i]);
                        }
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
</script>