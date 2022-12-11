function penerimaSm() {
    $('#penerima_id').select3({
        theme: "bootstrap5",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Penerima',
        ajax: {
            dataType: 'json',
            url: 'getpenerima',
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
    })
}
function penerima() {
    $('#penerima_id').select2({
        theme: "bootstrap4",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Penerima',
        ajax: {
            dataType: 'json',
            url: 'getpenerima',
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
    })
}