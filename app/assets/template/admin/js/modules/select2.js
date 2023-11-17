import "select2/dist/js/select2.min.js";

if (window.$ === undefined) {
    window.$ = window.jQuery = require('jquery');
}

$.fn.select2.defaults.set("theme", "bootstrap4");