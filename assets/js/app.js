require('../css/app.scss');
const $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap');

$(document).ready(function () {
    $("#selectLocale .dropdown-item").click(function () {
        var $selectedLocale = $(this).data('value');
        var $currentLocale = $('html')[0].lang;
        if ($currentLocale !== $selectedLocale) {
            var $url = $(this).data('href');
            window.location.replace($url);
        }
    });
});