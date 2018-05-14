const $ = require('jquery');
const Barba = require('barba.js');
require('jquery-form');

$(document).on('dom_updated', function (event, dom) {
    dom.find('.filter-form:not(.filter-form-initialized)').each(function () {
        $(this).addClass('filter-form-initialized');
        $(this).on('submit', function (event) {
            event.preventDefault();
            let values = $(this).formSerialize();
            let action = $(this).attr('action');
            Barba.Pjax.goTo(action + '?' + values);

            return false;
        });
    })
});

$(document).on('dom_updated', function (event, dom) {
    dom.find('.ajax-form:not(.ajax-form-initialized)').each(function () {
        $(this).addClass('ajax-form-initialized');
        let parent = $(this).closest('.ajax-form-wrapper');
        $(this).ajaxForm({
            target: parent,
            success: function () {
                $(document).trigger('dom_updated', [parent]);
            }
        });
    });
});

