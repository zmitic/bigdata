const Pjax = require('./bridge/pjax-fork');


let pjaxInstance;

var initButtons = function () {
    var buttons = document.querySelectorAll('a[data-pjax-trigger]');
    if (!buttons) {
        return;
    }

    // jshint -W083
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener('click', function (event) {
            event.preventDefault();
            var el = event.currentTarget;
            let blocks = JSON.parse(el.getAttribute('data-pjax-trigger'));

            let url = el.getAttribute('href');
            pjaxInstance.options.selectors = blocks;
            pjaxInstance.loadUrl(url, {selectors: blocks});
        });
    }
    // jshint +W083
};

console.log('Document initialized:', window.location.href);

document.addEventListener('pjax:send', function () {
    // console.log('Event: pjax:send', arguments)
});

document.addEventListener('pjax:complete', function () {
});

document.addEventListener('pjax:error', function () {
    // console.log('Event: pjax:error', arguments)
});

document.addEventListener('DOMContentLoaded', function () {
    pjaxInstance = new Pjax({
        elements: ['form[data-pjax-trigger]'],
        // selectors: ['#content', '#test'],
        cacheBust: false,
        debug: false
    });
    initButtons();
});

document.addEventListener("pjax:success", function () {
    initButtons();
});

