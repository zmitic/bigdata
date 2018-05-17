require('select2');
require('./select2entity-fork');

$(document).on('dom_updated', function (event, dom, oldDom) {
    // $('.select2-initialized').each(function () {
    //     let old = $(this);
    //     old.select2('destroy');
    //     old.off('select2:select');
    // });

    dom.find('.select2entity[data-autostart="true"]').each(function () {
        $(this).addClass('select2-initialized');
        let parent = $(this).closest('.form-group');
        $(this).select2entity({
            dropdownParent: parent
        });
    });
});
