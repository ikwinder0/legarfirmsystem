$(document).ready(() => {

    var status = $('select[name="status"]').find(":selected").val();
    getCaseDetailStatusLog(status);


    $('select[name="status"]').change(function(){
        var status= $(this).val();
        getCaseDetailStatusLog(status);
    });
});

function getCaseDetailStatusLog(selectedstatus)
{
    var status = selectedstatus;
    var case_detail_id = $('input[name="case_detail_id"]').val();
    $.ajax({
        url: "/api/v1/get-latest-case-detail-status-log",
        data: {
            'case_detail_id': case_detail_id,
            'status': status,
        },
        type: 'POST',
        success: function (result) {
            var remarks = result.remarks;
            $('textarea[name="remarks"]').val(remarks);
        },
        error: function (result) {
            console.log('error')
            alert("Pls try it later!!");
        }
    });
}
