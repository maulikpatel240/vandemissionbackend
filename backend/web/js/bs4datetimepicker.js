//https://tempusdominus.github.io/bootstrap-4/Usage/
/*
 * No Icon (input field only): <input type="text" class="form-control datetimepicker-input" id="datetimepicker5" data-toggle="datetimepicker" data-target="#datetimepicker5"/>
 * Minimum Setup : $('#datetimepicker1').datetimepicker();
 * Using Locales :  $('#datetimepicker2').datetimepicker({
 locale: 'ru'
 });
 * Time Only : $('#datetimepicker3').datetimepicker({
 format: 'LT'
 });
 * Date Only :  $('#datetimepicker4').datetimepicker({
 format: 'L'
 });
 * Enabled/Disabled Dates : $('#datetimepicker6').datetimepicker({
 defaultDate: "11/1/2013",
 disabledDates: [
 moment("12/25/2013"),
 new Date(2013, 11 - 1, 21),
 "11/22/2013 00:53"
 ]
 });
 * Linked Pickers : $('#datetimepicker7').datetimepicker();
 $('#datetimepicker8').datetimepicker({
 useCurrent: false
 });
 $("#datetimepicker7").on("dp.change", function (e) {
 $('#datetimepicker8').datetimepicker('minDate', e.date);
 });
 $("#datetimepicker8").on("dp.change", function (e) {
 $('#datetimepicker7').datetimepicker('maxDate', e.date);
 });
 * Custom Icons:$('#datetimepicker9').datetimepicker({
 icons: {
 time: "fa fa-clock-o",
 date: "fa fa-calendar",
 up: "fa fa-arrow-up",
 down: "fa fa-arrow-down"
 }
 });
 * View Mode : $('#datetimepicker10').datetimepicker({
 viewMode: 'years'
 });
 * Min View Mode : $('#datetimepicker11').datetimepicker({
 viewMode: 'years',
 format: 'MM/YYYY'
 });
 * Disabled Days of the Week : $('#datetimepicker12').datetimepicker({
 daysOfWeekDisabled: [0, 6]
 });
 * Multidate : $('#datetimepicker14').datetimepicker({
 allowMultidate: true,
 multidateSeparator: ','
 });
 * 
 * 
 * 
 * 
 */
function timePicker(timepickerId = "timepicker", format = "HH:mm", locale = "en", stepping = "1", timevalue = "") {

    $('#' + timepickerId).datetimepicker({
        format: format,
        stepping: stepping,
        locale: locale,
        useCurrent: false,
        custompicker: true
    });

    var d = new Date();

    var month = d.getMonth() + 1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;

    current_date = output;
    var dateFormat = "YYYY-MM-DDTHH:mm:ss";
    if ($('#' + timepickerId).val()) {
        s_time = $('#' + timepickerId).val();
    } else {
        s_time = "00:00";
    }
    $('#' + timepickerId).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
    if (timevalue) {
        s_time = timevalue;
        $('#' + timevalue).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
}
}
function timeLinkedPicker(starttimeId = "starttime", endtimeID = "endtime", format = "HH:mm", locale = "en", stepping = "1", starttimevalue = "", endtimevalue = "") {

    $('#' + starttimeId).datetimepicker({
        format: format,
        stepping: stepping,
        locale: locale,
        useCurrent: false,
        custompicker: true
    });
    $('#' + endtimeID).datetimepicker({
        format: format,
        stepping: stepping,
        locale: locale,
        useCurrent: false,
        custompicker: true
    });

    var d = new Date();

    var month = d.getMonth() + 1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;

    current_date = output;
    var dateFormat = "YYYY-MM-DDTHH:mm:ss";
    if ($('#' + starttimeId).val()) {
        s_time = $('#' + starttimeId).val();
    } else {
        s_time = "08:00";
    }
    if ($('#' + endtimeID).val()) {
        e_time = $('#' + endtimeID).val();
    } else {
        e_time = "17:00";
    }
//    s_time = "08:00";
//    e_time = "17:00";
    $('#' + starttimeId).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
    $('#' + endtimeID).val(moment("" + current_date + "T" + e_time + "", dateFormat).format(format));
    $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
    if (starttimevalue) {
        s_time = starttimevalue;
        e_time = endtimevalue;
        var splittime = s_time.split(':');
        if (splittime) {
            if (splittime[0] == '00' && splittime[1] == '01') {
                stepping = '14';
            } else if (splittime[0] == '23' && splittime[1] == '45') {
                stepping = '14';
            } else {
                stepping = '15';
            }
        }
        $('#' + starttimeId).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
        $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
    }
    if (endtimevalue) {
        e_time = endtimevalue;
        $('#' + endtimeID).val(moment("" + current_date + "T" + e_time + "", dateFormat).format(format));
    }
    $("#" + starttimeId).on("dp.change", function (e) {
        var d = new Date();
        var month = d.getMonth() + 1;
        var day = d.getDate();
        var output = d.getFullYear() + '-' +
                (month < 10 ? '0' : '') + month + '-' +
                (day < 10 ? '0' : '') + day;

        current_date = output;
        var dateFormat = "YYYY-MM-DDTHH:mm:ss";
        s_time = $('#' + starttimeId).val();
        var splittime = s_time.split(':');
        if (splittime) {
            if (splittime[0] == '00' && splittime[1] == '00') {
                jointime = splittime[0] + ':01';
            } else if (splittime[0] == '23' && splittime[1] == '59') {
                jointime = splittime[0] + ':45';
            } else if (splittime[0] != '00' && splittime[1] == '01') {
                jointime = splittime[0] + ':00';
            } else if (splittime[0] != '23' && splittime[1] == '59') {
                jointime = splittime[0] + ':00';
            } else {
                jointime = s_time;
            }
            s_time = $('#' + starttimeId).val(moment("" + current_date + "T" + jointime + "", dateFormat).format(format));

        }
        s_time = $('#' + starttimeId).val();
        var splittime_new = s_time.split(':');
        if (splittime_new) {
            if (splittime_new[0] == '00' && splittime_new[1] == '01') {
                stepping = '14';
            } else if (splittime_new[0] == '23' && splittime_new[1] == '45') {
                stepping = '14';
            } else {
                stepping = '15';
            }
        }
        if (e.date == "undefined" || e.date == false) {
            if (starttimevalue) {
                s_time = starttimevalue;
            } else {
                s_time = "08:00";
            }
            if (endtimevalue) {
                e_time = endtimevalue;
            } else {
                e_time = "17:00";
            }
            $('#' + starttimeId).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
            $('#' + endtimeID).val(moment("" + current_date + "T" + e_time + "", dateFormat).format(format));
            $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
        } else {
            $('#' + endtimeID).val(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
            $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
        }
    });
    $("#" + endtimeID).on("dp.change", function (e) {
        s_time = $('#' + starttimeId).val();
        e_time = $('#' + endtimeID).val();
        var d = new Date();
        var month = d.getMonth() + 1;
        var day = d.getDate();
        var output = d.getFullYear() + '-' +
                (month < 10 ? '0' : '') + month + '-' +
                (day < 10 ? '0' : '') + day;

        current_date = output;
        var dateFormat = "YYYY-MM-DDTHH:mm:ss";
//        var splittime = e_time.split(':');
//        if(splittime){
//            if(splittime[0] == '00' && splittime[1] == '00'){
//                jointime = splittime[0]+':01';
//            }else if(splittime[0] == '23' && splittime[1] == '59'){
//                jointime = splittime[0]+':45';
//            }else if(splittime[0] != '00' && splittime[1] == '01'){
//                jointime = splittime[0]+':00';
//            }else if(splittime[0] != '23' && splittime[1] == '59'){
//                jointime = splittime[0]+':00';
//            }else{
//                jointime = e_time;
//            }
//            e_time = $('#'+endtimeID).val(moment(""+current_date+"T"+jointime+"",dateFormat).format(format));
//            
//        }
        if (e.date == "undefined" || e.date == false) {
            if (starttimevalue) {
                s_time = starttimevalue;
            } else {
                s_time = "08:00";
            }
            if (endtimevalue) {
                e_time = endtimevalue;
            } else {
                e_time = "17:00";
            }
            var splittime = s_time.split(':');
            if (splittime) {
                if (splittime[0] == '00' && splittime[1] == '01') {
                    stepping = '14';
                } else if (splittime[0] == '23' && splittime[1] == '45') {
                    stepping = '14';
                } else {
                    stepping = '15';
                }
            }
            $('#' + endtimeID).val(moment("" + current_date + "T" + e_time + "", dateFormat).format(format));
            $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
        }
    });

}

function datePicker(datepickerId = "datepicker", format = "YYYY-MM-DD", locale = "en", Multidate = "", value = "", maxDate = "", minDate = "") {
    var d = new Date();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var current_date = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    var dateFormat = "YYYY-MM-DD";
    if (Multidate) {
        $('#' + datepickerId).datetimepicker({
            format: format,
            locale: locale,
            allowMultidate: true,
            multidateSeparator: ','
        });
        $('#' + datepickerId).data("DateTimePicker").minDate(moment("" + current_date + "", dateFormat));
    } else {
        $('#' + datepickerId).datetimepicker({
            format: format,
            locale: locale
        });
        if (value) {
            $('#' + datepickerId).val(value);
        }
        if (maxDate && maxDate == "current") {
            $('#' + datepickerId).data("DateTimePicker").maxDate(moment("" + current_date + "", dateFormat));
        }
        if (maxDate && maxDate != "current") {
            $('#' + datepickerId).data("DateTimePicker").maxDate(moment("" + maxDate + "", dateFormat));
        }
        if (minDate && minDate == "current") {
            $('#' + datepickerId).data("DateTimePicker").minDate(moment("" + current_date + "", dateFormat));
        }
        if (minDate && minDate != "current") {
            $('#' + datepickerId).data("DateTimePicker").minDate(moment("" + minDate + "", dateFormat));
        }
}
}

function dateLinkedPicker(startdateId = "startdate", enddateID = "enddate", format = "YYYY-MM-DD", locale = "en") {
    var d = new Date();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var currentdate = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;

    $('#' + startdateId).datetimepicker({
        format: format,
        locale: locale
    });
    $('#' + enddateID).datetimepicker({
        format: format,
        locale: locale
    });

    $("#" + startdateId).on("dp.change", function (e) {
        $('#' + enddateID).data("DateTimePicker").minDate(e.date);
    });
}

function dateLinkedPickerWorkexp(startdateId = "startdate", enddateID = "enddate", format = "YYYY-MM-DD", locale = "en", startdatevalue = "", enddatevalue = "") {
    var d = new Date();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var currentdate = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    $('#' + startdateId).datetimepicker({
        format: format,
        locale: locale
    });
    $('#' + enddateID).datetimepicker({
        format: format,
        locale: locale
    });
    $('#' + startdateId).data("DateTimePicker").maxDate(currentdate);
    $('#' + enddateID).data("DateTimePicker").minDate(currentdate);
    $('#' + enddateID).data("DateTimePicker").maxDate(currentdate);

    if (startdatevalue) {
        $('#' + startdateId).val(startdatevalue);
        $('#' + enddateID).data("DateTimePicker").minDate(startdatevalue);
    }
    if (enddatevalue) {
        $('#' + enddateID).val(enddatevalue);
    }
    $("#" + startdateId).on("dp.change", function (e) {
        $('#' + enddateID).data("DateTimePicker").minDate(e.date);
    });
}

// Slot single or multiple date & timepicker
function dateLinkedPickermultislot(startdateId = "startdate", enddateID = "enddate", format = "YYYY-MM-DD", locale = "en", startdatevalue = "", enddatevalue = "", day_limit_slot_multiple = "") {
    var d = new Date();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var currentdate = d.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;
    if (startdatevalue) {
        var next30days = new Date(startdatevalue);
        var nextday = new Date(startdatevalue);
    } else {
        var next30days = new Date();
        var nextday = new Date();
    }
    nextday.setDate(nextday.getDate() + 1);
    var next_y = nextday.getFullYear();
    var next_m = nextday.getMonth() + 1;
    var next_d = nextday.getDate();
    var nextdate = next_y + '-' + (next_m < 10 ? '0' : '') + next_m + '-' + (next_d < 10 ? '0' : '') + next_d;

    next30days.setDate(next30days.getDate() + day_limit_slot_multiple);
    var next30_y = next30days.getFullYear();
    var next30_m = next30days.getMonth() + 1;
    var next30_d = next30days.getDate();
    var maxdate = next30_y + '-' + (next30_m < 10 ? '0' : '') + next30_m + '-' + (next30_d < 10 ? '0' : '') + next30_d;
    var dateFormat = "YYYY-MM-DD";
    $('#' + startdateId).datetimepicker({
        format: format,
        locale: locale,
        useCurrent: false
    });
    $('#' + enddateID).datetimepicker({
        format: format,
        locale: locale,
        useCurrent: false
    });
    if (startdatevalue && enddatevalue) {
        $('#' + startdateId).val(startdatevalue);
        $('#' + enddateID).val(enddatevalue);
//        $('#'+enddateID).data("DateTimePicker").maxDate(moment(""+maxdate+"",dateFormat));
//        $('#'+enddateID).data("DateTimePicker").minDate(moment(""+nextdate+"",dateFormat));
    }
    $('#' + startdateId).data("DateTimePicker").minDate(currentdate);

    $("#" + startdateId).on("dp.change", function (e) {
        var next30days_in = new Date(e.date);
        next30days_in.setDate(next30days_in.getDate() + day_limit_slot_multiple);
        var next30_y_in = next30days_in.getFullYear();
        var next30_m_in = next30days_in.getMonth() + 1;
        var next30_d_in = next30days_in.getDate();
        var maxdate_in = next30_y_in + '-' + (next30_m_in < 10 ? '0' : '') + next30_m_in + '-' + (next30_d_in < 10 ? '0' : '') + next30_d_in;

        var nextday_in = new Date(e.date);
        nextday_in.setDate(nextday_in.getDate() + 1);
        var next_y_in = nextday_in.getFullYear();
        var next_m_in = nextday_in.getMonth() + 1;
        var next_d_in = nextday_in.getDate();
        var nextdate_in = next_y_in + '-' + (next_m_in < 10 ? '0' : '') + next_m_in + '-' + (next_d_in < 10 ? '0' : '') + next_d_in;

        if (e.date == "undefined" || e.date == false) {
            if (startdatevalue) {
                $('#' + startdateId).val(startdatevalue);
            } else {
                $('#' + startdateId).val(currentdate);
            }

            if (enddatevalue) {
                $('#' + enddateID).val(enddatevalue);
            } else {
                $('#' + enddateID).val(nextdate);
            }
            $('#' + enddateID).data("DateTimePicker").minDate(nextdate_in);
            $('#' + enddateID).data("DateTimePicker").maxDate(maxdate_in);
        } else {
            $('#' + enddateID).data("DateTimePicker").minDate(nextdate_in);
            $('#' + enddateID).data("DateTimePicker").maxDate(maxdate_in);
            $('#' + enddateID).val(nextdate_in);
        }
    });

    $("#" + enddateID).on("dp.change", function (e) {

    });

}
function timeLinkedPickerslot(starttimeId = "starttime", endtimeID = "endtime", format = "HH:mm", locale = "en", stepping = "1", starttimevalue = "", endtimevalue = "", date = "", slot_type = "1", breaktimeid = "") {

    $('#' + starttimeId).datetimepicker({
        format: format,
        stepping: stepping,
        locale: locale,
        useCurrent: false,
        custompicker: true
    });
    $('#' + endtimeID).datetimepicker({
        format: format,
        stepping: stepping,
        locale: locale,
        useCurrent: false,
        custompicker: true
    });

    var d = new Date();

    var month = d.getMonth() + 1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;

    current_date = output;
    var dateFormat = "YYYY-MM-DDTHH:mm:ss";
    if ($('#' + starttimeId).val()) {
        s_time = $('#' + starttimeId).val();
    } else {
        s_time = "08:00";
    }
    if ($('#' + endtimeID).val()) {
        e_time = $('#' + endtimeID).val();
    } else {
        e_time = "17:00";
    }
//    s_time = "08:00";
//    e_time = "17:00";
    $('#' + starttimeId).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
    $('#' + endtimeID).val(moment("" + current_date + "T" + e_time + "", dateFormat).format(format));
    $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
    if (starttimevalue) {
        s_time = starttimevalue;
        e_time = endtimevalue;
        var splittime = s_time.split(':');
        if (splittime) {
            if (splittime[0] == '00' && splittime[1] == '01') {
                stepping = '14';
            } else if (splittime[0] == '23' && splittime[1] == '45') {
                stepping = '14';
            } else {
                stepping = '15';
            }
        }
        $('#' + starttimeId).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
        $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
    }
    if (endtimevalue) {
        e_time = endtimevalue;
        $('#' + endtimeID).val(moment("" + current_date + "T" + e_time + "", dateFormat).format(format));
    }
    $("#" + starttimeId).on("dp.change", function (e) {
        var d = new Date();
        var month = d.getMonth() + 1;
        var day = d.getDate();
        var output = d.getFullYear() + '-' +
                (month < 10 ? '0' : '') + month + '-' +
                (day < 10 ? '0' : '') + day;

        current_date = output;
        var dateFormat = "YYYY-MM-DDTHH:mm:ss";
        s_time = $('#' + starttimeId).val();
        var splittime = s_time.split(':');
        if (splittime) {
            if (splittime[0] == '00' && splittime[1] == '00') {
                jointime = splittime[0] + ':01';
            } else if (splittime[0] == '23' && splittime[1] == '59') {
                jointime = splittime[0] + ':45';
            } else if (splittime[0] != '00' && splittime[1] == '01') {
                jointime = splittime[0] + ':00';
            } else if (splittime[0] != '23' && splittime[1] == '59') {
                jointime = splittime[0] + ':00';
            } else {
                jointime = splittime[0] + ':' + splittime[1];
            }
            s_time = $('#' + starttimeId).val(moment("" + current_date + "T" + jointime + "", dateFormat).format(format));
        }
        s_time = $('#' + starttimeId).val();
        var splittime_new = s_time.split(':');
        if (splittime_new) {
            if (splittime_new[0] == '00' && splittime_new[1] == '01') {
                stepping = '14';
            } else if (splittime_new[0] == '23' && splittime_new[1] == '45') {
                stepping = '14';
            } else {
                stepping = '15';
            }
        }
        if (e.date == "undefined" || e.date == false) {
            if (starttimevalue) {
                s_time = starttimevalue;
            } else {
                s_time = "08:00";
            }
            if (endtimevalue) {
                e_time = endtimevalue;
            } else {
                e_time = "17:00";
            }
            $('#' + starttimeId).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
            $('#' + endtimeID).val(moment("" + current_date + "T" + e_time + "", dateFormat).format(format));
            $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
        } else {
            $('#' + endtimeID).val(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
            $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
        }

        s_time = $('#' + starttimeId).val();
        e_time = $('#' + endtimeID).val();
        var resultInMinutes = getMinutes(s_time, e_time, 'time');
        //120 Minutes = 2 Hour
        var InMinutes;
        if (resultInMinutes <= 120) {
            InMinutes = resultInMinutes;
        } else {
            InMinutes = 120;
        }
        if (breaktimeid) {
            b_time = $('#' + breaktimeid).val();
            $('#' + breaktimeid).data("DateTimePicker").minDate(moment('00:00', "HH:mm"));
            $('#' + breaktimeid).data("DateTimePicker").maxDate(moment("" + current_date + "T00:", dateFormat).add(InMinutes, "minutes").format(format));
            if (slot_type == 2) {
                multiple_clearbreakktime(date);
            } else {
                single_clearbreakktime(date);
            }
        } else {
            b_time = "00:00";
        }
        changetime = {slot_type: slot_type, date: date, starttime: s_time, endtime: e_time, breaktime: b_time};
        myonchangetime(changetime);
    });
    $("#" + endtimeID).on("dp.change", function (e) {
        s_time = $('#' + starttimeId).val();
        e_time = $('#' + endtimeID).val();
        var d = new Date();
        var month = d.getMonth() + 1;
        var day = d.getDate();
        var output = d.getFullYear() + '-' +
                (month < 10 ? '0' : '') + month + '-' +
                (day < 10 ? '0' : '') + day;

        current_date = output;
        var dateFormat = "YYYY-MM-DDTHH:mm:ss";
//        var splittime = e_time.split(':');
//        if(splittime){
//            if(splittime[0] == '00' && splittime[1] == '00'){
//                jointime = splittime[0]+':01';
//            }else if(splittime[0] == '23' && splittime[1] == '59'){
//                jointime = splittime[0]+':45';
//            }else if(splittime[0] != '00' && splittime[1] == '01'){
//                jointime = splittime[0]+':00';
//            }else if(splittime[0] != '23' && splittime[1] == '59'){
//                jointime = splittime[0]+':00';
//            }else{
//                jointime = e_time;
//            }
//            e_time = $('#'+endtimeID).val(moment(""+current_date+"T"+jointime+"",dateFormat).format(format));
//            
//        }
        if (e.date == "undefined" || e.date == false) {
            if (starttimevalue) {
                s_time = starttimevalue;
            } else {
                s_time = "08:00";
            }
            if (endtimevalue) {
                e_time = endtimevalue;
            } else {
                e_time = "17:00";
            }
            var splittime = s_time.split(':');
            if (splittime) {
                if (splittime[0] == '00' && splittime[1] == '01') {
                    stepping = '14';
                } else if (splittime[0] == '23' && splittime[1] == '45') {
                    stepping = '14';
                } else {
                    stepping = '15';
                }
            }
            $('#' + endtimeID).val(moment("" + current_date + "T" + e_time + "", dateFormat).format(format));
            $('#' + endtimeID).data("DateTimePicker").minDate(moment("" + current_date + "T" + s_time + "", dateFormat).add(stepping, "minutes").format(format));
        }
        var resultInMinutes = getMinutes(s_time, e_time, 'time');
        //120 Minutes = 2 Hour
        var InMinutes;
        if (resultInMinutes <= 120) {
            InMinutes = resultInMinutes;
        } else {
            InMinutes = 120;
        }
        if (breaktimeid) {
            b_time = $('#' + breaktimeid).val();
            $('#' + breaktimeid).data("DateTimePicker").minDate(moment('00:00', "HH:mm"));
            $('#' + breaktimeid).data("DateTimePicker").maxDate(moment("" + current_date + "T00:", dateFormat).add(InMinutes, "minutes").format(format));
            if (slot_type == 2) {
                multiple_clearbreakktime(date);
            } else {
                single_clearbreakktime(date);
            }
        } else {
            b_time = "00:00";
        }
        if (slot_type == 2) {
            multiple_clearbreakktime(date);
        } else {
            single_clearbreakktime(date);
        }
        changetime = {slot_type: slot_type, date: date, starttime: s_time, endtime: e_time, breaktime: b_time};
        myonchangetime(changetime);
    });

    s_time = $('#' + starttimeId).val();
    e_time = $('#' + endtimeID).val();
    var resultInMinutes = getMinutes(s_time, e_time, 'time');
    //120 Minutes = 2 Hour
    var InMinutes;
    if (resultInMinutes <= 120) {
        InMinutes = resultInMinutes;
    } else {
        InMinutes = 120;
    }
    if (breaktimeid) {
        b_time = $('#' + breaktimeid).val();
        $('#' + breaktimeid).data("DateTimePicker").minDate(moment('00:00', "HH:mm"));
        $('#' + breaktimeid).data("DateTimePicker").maxDate(moment("" + current_date + "T00:", dateFormat).add(InMinutes, "minutes").format(format));

    } else {
        b_time = "00:00";
    }
    changetime = {slot_type: slot_type, date: date, starttime: s_time, endtime: e_time, breaktime: b_time};
    myonchangetime(changetime);
}


function timebreakPicker(timepickerId = "timepicker", format = "HH:mm", locale = "en", stepping = "1", timevalue = "", date = '', slot_type = '1', minuite = "") {
    var maxDate = "02:00";
    var minDate = '00:00';
    $('#' + timepickerId).datetimepicker({
        format: format,
        stepping: stepping,
        locale: locale,
        maxDate: moment(maxDate, "HH:mm"),
        minDate: moment(minDate, "HH:mm"),
        useCurrent: false,
        allowInputToggle: true,
        custompicker: true
    });

    $('#' + timepickerId).on("dp.change", function (e) {
        b_time = $('#' + timepickerId).val();
        if (slot_type == 2) {
            $("#breakhourspan_multiple" + date).html(b_time);
            s_time = $("#slot_starttime_multiple" + date).val();
            e_time = $("#slot_endtime_multiple" + date).val();
        } else {
            $("#breakhourspan_single" + date).html(b_time);
            s_time = $("#slot_starttime_single" + date).val();
            e_time = $("#slot_endtime_single" + date).val();
        }
        changetime = {slot_type: slot_type, date: date, starttime: s_time, endtime: e_time, breaktime: b_time};

        myonchangetime(changetime);
    });

    var d = new Date();

    var month = d.getMonth() + 1;
    var day = d.getDate();

    var output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;

    current_date = output;
    var dateFormat = "YYYY-MM-DDTHH:mm:ss";
    if ($('#' + timepickerId).val()) {
        s_time = $('#' + timepickerId).val();
    } else {
        s_time = "00:00";
    }
    $('#' + timepickerId).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
    if (timevalue) {
        s_time = timevalue;
        $('#' + timevalue).val(moment("" + current_date + "T" + s_time + "", dateFormat).format(format));
}
}

function getMinutes(start, end, type) {
    var d = new Date();
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;

    var current_date = output;

    var startdate;
    var enddate;
    if (type == 'time') {
        startdate = current_date + ' ' + start;
        enddate = current_date + ' ' + end;
    } else {
        startdate = start;
        enddate = end;
    }
    var startTime = new Date(startdate); //'2012/10/09 12:00'
    var endTime = new Date(enddate); //2013/10/09 12:00
    var difference = endTime.getTime() - startTime.getTime(); // This will give difference in milliseconds
    var resultInMinutes = Math.round(difference / 60000) - 1;
    // console.log(resultInMinutes);
    return resultInMinutes;
}