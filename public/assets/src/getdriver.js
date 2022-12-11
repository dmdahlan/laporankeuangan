function driver() {
    $('#driver_id').select2({
        theme: "bootstrap4",
        minimumInputLength: 3,
        allowClear: true,
        placeholder: 'Pilih Driver',
        ajax: {
            dataType: 'json',
            url: 'getdriver',
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