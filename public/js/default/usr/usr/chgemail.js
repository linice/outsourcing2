$(document).ready(function(){
	/**
	 * 修改登陆邮箱
	 */
	$('#btn_chgEmail').click(function() {
		var disabled = $(this).attr('class');
		$(this).attr('class', 'DISABLED_Y');
		chgEmail();
	});
}); //End: $(document).ready


/**
 * 确认修改邮箱
 */
function chgEmail() {
	$.ajax({
		url: '/usr_usr/handlechgemail',
		type: 'post',
		async: false,
		dataType: 'json', //xml, json, script or html
		data: $('#form_chgEmail').serializeArray(),
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
				$('#tip_chgEmail').html(data['msg']);
				setTimeout("$('#tip_chgEmail').html('')", 5000);
			} else {
				$('#tip_chgEmail').html(data['msg']);
			}
			$('#btn_chgEmail').attr('class', 'DISABLED_N');
		}
	});
}


