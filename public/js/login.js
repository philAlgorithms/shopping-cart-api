$("body").ready(function(){

   var reg = $("#login");
   var loading = $("#btn-loading");
    var mail;
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $("#login").click(function(){
    showLoading(reg, loading);
	  
    showAlert('primary', 'please wait');

    $.ajax({
        type:"POST",
        url:"auth/login",
	headers: {
            'Accept': 'application/json',
	    'XSRF-TOKEN': $('meta[name="_token"]').attr('content'),
	},
	data: {
	    email: $('#email').val(),
	    password: $('#password').val(),
	},
	datatype: 'json',
        error: function(err){
	    hideLoading(reg, loading);
	    handleCommonErrors(err.responseJSON);
	    if(err.responseJSON.type === 'validation'){
	      hideLoading(reg, loading);
	      showAlert('warning','Incorrect email/password');
	      Swal.fire({
			icon: 'error',
			title: "Login Error", 
			text: 'Incorrect username/password', 
	      });
	    }else
			{
				Swal.fire({
					icon: 'error',
					title: "Login Error", 
					text: err.responseJSON.message, 
				});
			}
        },
        success:function(data){
			hideLoading(reg, loading);
			Swal.fire("Success", "Login Successful", "success");
			window.location.replace(data['dashboard']);
		},
    });
    
  });

});

