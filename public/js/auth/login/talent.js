const submit = $('#login');

submit.click(function() {
    const that = $(this);
    startLoading(that);

    $.ajax({
        type: "POST",
        url: api('auth/login/talent'),
        data: {
            email: $('#user_email').val(),
            password: $('#user_password').val()
        },
        error: function(err)
        {
            showResponseModal(err);
            stopLoading(that);
        },
        success: function(data, statusText, xhr)
        {
            showResponseModal(xhr);
            stopLoading(that);
            window.location = url('/account/talent');
        }
    });
});