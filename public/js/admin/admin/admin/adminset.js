$(document).ready(function(){
	
	
}); //End: $(document).ready



/**
 * 确认修改注册信息
 */
function saveAdminInfo() {
	$.ajax({
		url: '/admin/admin_admin/saveadmininfo',
		type: 'get',
		async: false,
		dataType: 'json', //xml, json, script or html
		data: $('#form_adminInfo').serializeArray(),
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
				$('#tip_adminInfo').html(data['msg']);
				setTimeout("$('#tip_adminInfo').html('')", 3000);
			} else {
				$('#tip_adminInfo').html(data['msg']);
			}
		}
	});
}
