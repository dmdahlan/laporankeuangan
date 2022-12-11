function tujuan() {
    $('#tujuan_id').select2({
        theme: "bootstrap4",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Tujuan',
        ajax: {
            dataType: 'json',
            url: 'gettujuan',
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