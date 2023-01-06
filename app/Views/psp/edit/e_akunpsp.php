<div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Default Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="akunpsp/<?= $datalama->id_akun ?>" method="post" id="simpan">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PATCH">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="no_akun">No Akun</label>
                                <input type="text" name="no_akun" id="no_akun" class="form-control" value="<?= $datalama->no_akun ?>" placeholder="No Akun">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="nama_akun">Nama Akun</label>
                                <input type="text" name="nama_akun" id="nama_akun" class="form-control" value="<?= $datalama->nama_akun ?>" placeholder="Nama Akun">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="saldo_awal">Saldo Awal</label>
                                <input type="text" name="saldo_awal" id="saldo_awal" class="form-control rupiah" value="<?= $datalama->saldo_awal ?>" placeholder="Saldo Awal">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Debet/Kredit</label>
                                <select name="dk_akun" id="dk_akun" class="form-control">
                                    <option value="">Pilih</option>
                                    <?php foreach ($debetkredit as $dk) : ?>
                                        <option value="<?= $dk['value'] ?>" <?= $datalama->dk_akun === $dk['value'] ? 'selected' : '' ?>><?= $dk['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Keterangan</label>
                                <select name="ap_akun" id="ap_akun" class="form-control">
                                    <option value="">Pilih</option>
                                    <?php foreach ($ketakun as $ket) : ?>
                                        <option value="<?= $ket['value'] ?>" <?= $datalama->ap_akun === $ket['value'] ? 'selected' : '' ?>><?= $ket['name'] ?></option>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback"></div>
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
        new AutoNumeric('#saldo_awal', {
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            decimalPlaces: 0,
        })
        $('#simpan').submit(function(e) {
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
    $('input[type=text]').keyup(function(e) {
        this.value = this.value.toUpperCase();
    })
</script>