const submit = $('#add');

submit.click(function() {
    const that = $(this);
    startLoading(that);

    $.ajax({
        type: "POST",
        url: '/cart/add',
        data: {
            product_id: $('#product').val(),
            quantity: $('#quantity').val()
        },
        error: function(err)
        {
            console.log(err);
            showResponseModal(err);
            stopLoading(that);
        },
        success: function(data, statusText, xhr)
        {
            console.log(xhr);
            showResponseModal(xhr);
            stopLoading(that);
            // window.location = url('/account/admin');
        }
    });
});