<div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Default Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="bankpsp/<?= $datalama->id_transaksi ?>" method="post" id="simpan">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PATCH">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="tgl_transaksi">No Akun</label>
                                <input type="text" name="tgl_transaksi" id="tgl_transaksi" class="form-control tanggal" placeholder="Tanggal" autocomplete="off" value="<?= tanggal($datalama->tgl_transaksi) ?>">
                                <div class="invalid-feedback tgl_transaksi"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Bkk/Bkm</label>
                                <input type="text" name="no_bukti" id="no_bukti" class="form-control" placeholder="BKK/BKM" value="<?= $datalama->no_bukti ?>">
                                <div class="invalid-feedback no_bukti"></div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="form-group">
                                <label class="form-label">Uraian</label>
                                <input type="text" name="uraian" id="uraian" class="form-control" placeholder="Uraian" value="<?= $datalama->uraian ?>">
                                <div class="invalid-feedback uraian"></div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-label">Akun Debet</label>
                                <select name="akun_debet" id="akun_debet" class="form-control">
                                    <?php foreach ($noakun as $na) : ?>
                                        <?php if ($na->no_akun == $datalama->akun_debet) : ?>
                                            <option value="<?= $na->no_akun ?>" selected><?= $na->no_akun . ' - ' . $na->nama_akun ?></option>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback akun_debet"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Debet</label>
                                <input type="text" name="debet" id="debet" class="form-control rupiah" placeholder="Debet" value="<?= $datalama->debet ?>" autocomplete="off">
                                <div class="invalid-feedback debet"></div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label class="form-label">Akun Kredit</label>
                                <select name="akun_kredit" id="akun_kredit" class="form-control">
                                    <?php foreach ($noakun as $na) : ?>
                                        <?php if ($na->no_akun == $datalama->akun_kredit) : ?>
                                            <option value="<?= $na->no_akun ?>" selected><?= $na->no_akun . ' - ' . $na->nama_akun ?></option>
                                        <?php endif ?>
                                    <?php endforeach ?>
                                </select>
                                <div class="invalid-feedback akun_kredit"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">Kredit</label>
                                <input type="text" name="kredit" id="kredit" class="form-control rupiah" placeholder="Kredit" value="<?= $datalama->kredit ?>" autocomplete="off">
                                <div class="invalid-feedback kredit"></div>
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
        dataakunpsp()
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
                            $('#' + key + '').addClass('is-invalid')
                            $('.' + key + '').text(value)
                            if (value === '') {
                                $('#' + key + '').removeClass('is-invalid')
                            }
                        })
                        // $('input[name=csrfToken]').val(response.csrfToken)
                    }
                },
                error: function(xhr, thrownError) {
                    alert(xhr.status + "\n" + xhr.responseText + "\n" + thrownError);
                }
            })
            return false
        })
    })

    function dataakunpsp() {
        $('#akun_debet,#akun_kredit').select2({
            theme: "bootstrap4",
            minimumInputLength: 3,
            allowClear: true,
            placeholder: "Pilih",
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
    new AutoNumeric('#debet', {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        selectOnFocus: false,
    })
    new AutoNumeric('#kredit', {
        digitGroupSeparator: '.',
        decimalCharacter: ',',
        decimalPlaces: 0,
        selectOnFocus: false,
    })
    $('input[type=text]').keyup(function(e) {
        this.value = this.value.toUpperCase();
    })
</script>