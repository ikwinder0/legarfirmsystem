function openModal(ele) {
    let _id = $(ele).data('id');
    $("#modalId").val(_id);
    $('#caseDetailStatus').modal('show');
}