function dari() {
    $('#dari_id').select2({
        theme: "bootstrap4",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Dari',
        ajax: {
            dataType: 'json',
            url: 'getdari',
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