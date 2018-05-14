require('bootstrap-sass');
require('./bridge/select2entity_brige');
require('./bridge/ajax-form-bridge');
const Barba = require('barba.js');

Barba.Pjax.cacheEnabled = false;
Barba.Pjax.start();

$(document).on('click', 'a.confirm', function () {
    let href = $(this).attr('data-href');
    if (confirm('Are you sure you want to delete this object?')) {
        $.ajax(href, {
            method: 'DELETE'
        });
    }
});

$(document).ajaxComplete(function (event, xhr) {
    let redirectUrl = xhr.getResponseHeader('redirect-to');
    if (redirectUrl) {
        Barba.Pjax.goTo(redirectUrl);
    }
});


