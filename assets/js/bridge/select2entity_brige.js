const $ = require('jquery');
// JS is equivalent to the normal "bootstrap" package
const Barba = require('barba.js');

Barba.Dispatcher.on('newPageReady', function (current, prev, rawContainer) {
    let container = $(rawContainer);
    container.find('.select2entity[data-autostart="true"]:not(.initialized)').each(function () {
        let parent = $(this).closest('.form-group');
        $(this).addClass('initialized');
        $(this).select2entity({
            dropdownParent: parent
        });
    });
});
