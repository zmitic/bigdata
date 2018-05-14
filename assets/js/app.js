require('bootstrap-sass');
require('./bridge/select2entity_brige');
require('./bridge/ajax-form-bridge');
const Barba = require('barba.js');


Barba.Dispatcher.on('newPageReady', function (current, prev, rawContainer) {
    $(document).trigger('dom_updated', [$(rawContainer)]);
});

$(document).ajaxComplete(function (event, xhr) {
    let redirectUrl = xhr.getResponseHeader('redirect-to');
    if (redirectUrl) {
        Barba.Pjax.goTo(redirectUrl);
    }
});


Barba.Pjax.cacheEnabled = false;
Barba.Pjax.start();


