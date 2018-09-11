require('../css/app.scss');
const $ = require('jquery');
global.$ = global.jQuery = $;
require('bootstrap');

require('./main/lang.js');
require('./main/flash-message.js');
require('./main/ajax-call.js');