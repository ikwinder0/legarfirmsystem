$(document).ready(() => {
    var _date = $('#appointment_date_crud');
    var timeDiv = $('#appointment_time_crud');
    if(_date.val()) {
        console.log('trigger');
        loadTime(_date.val());
    }

    _date.change(function(){

        $.ajax({
            url: "/api/v1/time-slot",
            data: {
                'date': $(this).val()
            },
            type: 'GET',
            success: function (result) {
                let data = result.available_times;
                timeDiv.empty();
                let options = '';
                if(data.length > 0) {
                    data.forEach((item) => {
                        options += `<option value="${item.value}">${item.label}</option>`
                    })
                }
                timeDiv.append(options);
            },
            error: function (result) {
                console.log('error')
            }
        });
    })

    let _val = $('#appointment_time').val();

    if(_val) {
        let _new = _val.split(' ');
        _date.val(moment(_val).format('YYYY-MM-DD'));
        loadTime(moment(_val).format('YYYY-MM-DD'),_new[1].substring(0,5))
        // timeDiv.append(`<option value="${_new[1]}">${moment(_val).format('LT')}</option>`);

    }
    function loadTime(date, selected = null){

        $.ajax({
            url: "/api/v1/time-slot",
            data: {
                'date': date
            },
            type: 'GET',
            success: function (result) {
                let data = result.available_times;
                timeDiv.empty();
                let options = '';
                if(data.length > 0) {
                    data.forEach((item) => {
                        options += `<option value="${item.value}" ${item.value == selected?'selected':''}>${item.label}</option>`
                    })
                }
                timeDiv.append(options);
            },
            error: function (result) {
                console.log('error')
            }
        });
    }
});
