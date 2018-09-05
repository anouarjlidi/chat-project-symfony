require('../css/app.scss');
const $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap');

$(document).ready(function () {
    $("#selectLocale").change(function () {
        var $selectedLocale = $(this).val();
        var $currentLocale = $('html')[0].lang;
        if ($currentLocale !== $selectedLocale) {
            var $url = $(this).find(':selected').data('href');
            window.location.replace($url);
        }
    });
});