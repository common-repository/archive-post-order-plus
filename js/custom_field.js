jQuery(function ($) {
    $('.select-custom-field-input').autocomplete({
        source: function (req, resp) {
            $.ajax({
                type: 'GET',
                url: localize.ajax_url,
                dataType: 'json',
                data: {
                    action: localize.action,
                    param: req.term
                }
            }).done(function (o) {
                resp(o);
            }).fail(function () {
                resp(['']);
            });
        },
        autoFocus: true,
        minLength: 2
    });
});