const $ = require('jquery');
require('select2');
require('./select2entity-fork');

$(document).on('dom_updated', function (event, dom) {
    dom.find('.select2entity[data-autostart="true"]:not(.initialized)').each(function () {
        let parent = $(this).closest('.form-group');
        $(this).addClass('initialized');
        $(this).select2entity({
            dropdownParent: parent
        });
    });
});
