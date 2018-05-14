const $ = require('jquery');
require('select2');
require('./select2entity-fork');

$(document).on('dom_updated', function (event, dom) {
    dom.find('.select2entity[data-autostart="true"]:not(.select2-initialized)').each(function () {
        $(this).addClass('select2-initialized');
        let parent = $(this).closest('.form-group');
        $(this).select2entity({
            dropdownParent: parent
        });
    });
});
