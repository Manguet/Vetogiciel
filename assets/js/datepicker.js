require('bootstrap-datepicker/js/bootstrap-datepicker');
require('bootstrap-datepicker/js/locales/bootstrap-datepicker.fr');
require('bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css');

var datepicker = $.fn.datepicker.noConflict();
$.fn.bootstrapDP = datepicker;

$(document).ready(function () {
    $('.input-daterange input').each(function () {
        $(this).datepicker({
            format     : 'm/yyyy',
            startView  : 'months',
            minViewMode: 'months',
            language   : 'fr'
        })
    })
})