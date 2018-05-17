require('bootstrap-sass');
require('./bridge/select2entity_brige');
require('./bridge/ajax-form-bridge');

$.ajaxSetup({cache:false});
$.ajaxSetup({ headers: { 'x-barba': 'yes' } });

$.cache = {};
$.expr.cacheLength = 1;

Barba.Dispatcher.on('newPageReady', function (current, prev, rawContainer, old) {
    $(document).trigger('dom_updated', [$(rawContainer)]);

    let oldDom = $(old);
    oldDom.find('*').each(function () {
        $(this).off();
    });
    oldDom.off();
    oldDom.empty().remove();
    Barba.BaseCache.reset();
    $.cache = {};
});

$(document).ajaxComplete(function (event, xhr) {
    let redirectUrl = xhr.getResponseHeader('redirect-to');
    if (redirectUrl) {
        Barba.Pjax.goTo(redirectUrl);
    }
});

Barba.Pjax.cacheEnabled = false;
Barba.Pjax.start();


