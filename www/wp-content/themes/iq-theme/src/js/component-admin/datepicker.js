import '../../../node_modules/bootstrap-datepicker/dist/js/bootstrap-datepicker';
import '../../../node_modules/bootstrap-datepicker/dist/locales/bootstrap-datepicker.cs.min';

$(document).ready(function() {
    $(".datePicker").datepicker({
        language: 'cs',
        format: 'd.m.yyyy',
        clearBtn: true,
    });
});