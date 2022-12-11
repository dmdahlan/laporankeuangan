function produkSm() {
    $('#produk_id').select3({
        theme: "bootstrap5",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Produk',
        ajax: {
            dataType: 'json',
            url: 'getproduk',
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
function produk() {
    $('#produk_id').select2({
        theme: "bootstrap4",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Produk',
        ajax: {
            dataType: 'json',
            url: 'getproduk',
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