window._ = require('lodash');

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
    require('jquery-ui/ui/widgets/autocomplete');
    require('jquery-ui/ui/widgets/datepicker');
    require('jquery-ui/ui/widgets/sortable');
    require('jquery-ui/ui/widgets/datepicker');
    require('jquery-timepicker/jquery.timepicker');
} catch (e) {}


window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
