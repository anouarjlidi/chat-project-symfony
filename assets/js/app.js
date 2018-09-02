require('../css/app.scss');
const $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap');

$(document).ready(function () {
    console.log("ready!");
});