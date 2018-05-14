const $ = require('jquery');
const Barba = require('barba.js');
require('jquery-form');

Barba.Dispatcher.on('newPageReady', function (current, prev, rawContainer) {
    let container = $(rawContainer);

    let filterForm = container.find('#filter-form');

    filterForm.on('submit', function (event) {
        event.preventDefault();
        let values = $(this).formSerialize();
        let action = $(this).attr('action');
        Barba.Pjax.goTo(action + '?' + values);

        return false;
    });
});