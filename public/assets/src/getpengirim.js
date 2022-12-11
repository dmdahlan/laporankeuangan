function pengirimSm() {
    $('#pengirim_id').select3({
        theme: "bootstrap5",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Pengirim',
        ajax: {
            dataType: 'json',
            url: 'getpengirim',
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
function pengirim() {
    $('#pengirim_id').select2({
        theme: "bootstrap4",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Pengirim',
        ajax: {
            dataType: 'json',
            url: 'getpengirim',
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