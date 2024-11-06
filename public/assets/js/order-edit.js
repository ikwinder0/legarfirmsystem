$("#receiveOrder").click(function (e) {
    e.preventDefault();

    const form = $(this).parent().parent().parent();
    form.append(`
        <input type="hidden" name="receive_order" value="1"/>
    `);

    form.submit();
});
