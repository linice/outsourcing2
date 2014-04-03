$(document).ready(function(){
	//发送密码邮件
	$('#btn_sendEmail').click(function(){
		$(this).attr('disabled', true);
		var email = $('#email').val();
		$.ajax({
			url: '/front_passwd/emailpasswd',
			type: 'get',
			dataType: 'json', //xml, json, script or html
			data: {'email': email},
			success: function(data, textStatus, jqXHR) {
				$('#btn_sendEmail').attr('disabled', false);
				if (data['err'] == 0) {
					$('#tip_sendEmail').html(data['msg']);
					setTimeout("$('#tip_sendEmail').html('')", 5000);
				} else {
					$('#tip_sendEmail').html(data['msg']);
				}
			}
		});
	});
	
	
	
}); //End: $(document).ready