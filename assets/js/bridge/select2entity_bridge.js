require('select2');
require('./select2entity-fork');

function init() {
    $('.select2entity[data-autostart="true"]:not(.select2entity-initialized)').each(function () {
        $(this).addClass('select2entity-initialized');
        let parent = $(this).closest('.form-group');
        $(this).select2entity({
            dropdownParent: parent
        });
    });
}

document.addEventListener("pjax:success", function () {
    init();
});

init();

