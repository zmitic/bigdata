const Pjax = require('./bridge/pjax-fork');

let pjaxInstance;

var initButtons = function () {
    var buttons = document.querySelectorAll('button[data-manual-trigger]');

    if (!buttons) {
        return;
    }

    // jshint -W083
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener("click", function (e) {
            var el = e.currentTarget;

            if (el.getAttribute('data-manual-trigger-override') === "true") {
                // Manually load URL with overridden Pjax instance options
                pjaxInstance.loadUrl('/example/page2.html', {cacheBust: false})
            }
            else {
                // Manually load URL with current Pjax instance options
                pjaxInstance.loadUrl('/example/page2.html');
            }
        })
    }
    // jshint +W083
};

console.log('Document initialized:', window.location.href);

document.addEventListener('pjax:send', function () {
    // console.log('Event: pjax:send', arguments)
});

document.addEventListener('pjax:complete', function () {
    console.log(arguments)
});

document.addEventListener('pjax:error', function () {
    // console.log('Event: pjax:error', arguments)
});

document.addEventListener('DOMContentLoaded', function () {
    pjaxInstance = new Pjax({
        elements: ['.pjax'],
        selectors: ['#content', '#test'],
        cacheBust: false
    });
    initButtons();
});

$( document ).ajaxComplete(function () {
   console.log(arguments);
});