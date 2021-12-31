//Select2 script
$(document).ready(function () {
    $('[data-toggle="popover"]').popover();

    $('#reservationdate').datetimepicker({
        format: 'L'
    });
});