jQuery(document).ready(function($){
    var loading_image = $('#wpjd-form .wpjd-loading');
	$('#wpjd-form button').click(function(e){
        loading_image.show();
		e.preventDefault();
		var _formdata = $('#wpjd-form').serialize();
		$.ajax({
            type: "POST",
            url: ajaxurl,
            data: _formdata,
            success: function(data) {
                loading_image.hide();
                alert(data);
            }
        });
	});
});