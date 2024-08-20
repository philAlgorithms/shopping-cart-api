const submit = $('#login');

submit.click(function() {
    const that = $(this);
    startLoading(that);

    $.ajax({
        type: "POST",
        url: '/auth/login/buyer',
        data: {
            email: $('#user_email').val(),
            password: $('#user_password').val()
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