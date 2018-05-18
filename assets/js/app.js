require('bootstrap-sass');
require('./bridge/select2entity_bridge');

require('./_pjax-init');

$.ajaxSetup({cache:false});
$.cache = {};
$.expr.cacheLength = 1;




