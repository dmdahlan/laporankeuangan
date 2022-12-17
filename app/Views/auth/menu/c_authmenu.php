<div class="modal fade" id="modal-create">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Default Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="menu" method="post" id="simpan">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="description">Nama Menu</label>
                                <input type="text" class="form-control" name="description" id="description" placeholder="Nama Menu">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="name">Menu</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Menu">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sort_menu">Order</label>
                                <input type="number" class="form-control" name="sort_menu" id="sort_menu" placeholder="Order">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Parent Menu</label>
                                <select id="menu_id" name="menu_id" class="form-control select2">
                                    <option value="0">Menu Utama</option>
                                    <?php foreach ($datamenu as $menu) : ?>
                                        <option value="<?= $menu->id ?>"><?= $menu->description ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="url">Url</label>
                                <input type="text" class="form-control" name="url" id="url" placeholder="Url">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Type Menu</label>
                            <select name="jns_menu" id="jns_menu" class="form-control">
                                <option value="">Pilih</option>
                                <?php foreach ($jenismenu as $jenis) : ?>
                                    <option value="<?= $jenis ?>"><?= $jenis ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="icon">Icon</label>
                                <input type="text" class="form-control" name="icon" id="icon" placeholder="Icon">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="background">Background</label>
                                <input type="text" class="form-control" name="background" id="background" placeholder="Background">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="parameter">Parameter</label>
                                <input type="text" class="form-control" name="parameter" id="parameter" placeholder="Parameter">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="checkbox">
                                    <label class="form-check-label">
                                        <input type="checkbox" value="1" id="status" name="status">
                                        Status Aktif
                                    </label>
                                    <input type="hidden" name="is_active" id="is_active">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-end">
                    <button type="submit" class="btn btn-primary btn-sm btn-save"><i class="fas fa-save"></i> Save</button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal"><i class="fa-solid fa-circle-xmark"></i> Close</button>
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
                    $('.btn-save').prop('disabled', true)
                    $('.btn-save').html('<i class="fa fa-spin fa-spinner"></i>')
                },
                complete: function() {
                    $('.btn-save').prop('disabled', false)
                    $('.btn-save').html('<i class="fas fa-save"></i> Save')
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
    $("#status").click(function() {
        const nilai = $("#status:checked").val();
        $('#is_active').val(nilai)
    });
</script>