$(document).ready(function(){
	/**
	 * 初始化邮件模板内容的ckeditor控件
	 */
	$(function() {
		CKEDITOR.replace('content');
		initEmailTpl();
	});
	
	/**
	 * 提交设置邮件模板
	 */
	$('#btn_setEmailTpl').click(function() {
		setEmailTpl();
	});
}); //End: $(document).ready


/**
 * 初始化草稿消息
 */
function initEmailTpl() {
	if (emailTpl) {
		$('#name').val(emailTpl.name);
		$('#title').val(emailTpl.title);
		CKEDITOR.instances.content.setData(emailTpl.content);
	}
}


/**
 * 提交设置邮件模板
 */
function setEmailTpl() {
	$('#content').val(CKEDITOR.instances.content.getData());
	$.ajax({
		url: '/admin/admin_email/setemailtpl',
		type: 'post',
		async: true,
		dataType: 'json', //xml, json, script or html
		data: $('#form_setEmailTpl').serializeArray(),
		success: function(data) {
			if (data['err'] == 0) {
				$('#tip_setEmailTpl').html(data['msg']);
				$('span.label').css({'color': 'black'});
				setTimeout("$('#tip_setEmailTpl').html('')", 5000);
			} else if (data['err'] == 1) {
				$('#tip_setEmailTpl').html(data['msg']);
			} else {
				$('#tip_setEmailTpl').html(data['msg']);
				$('#' + data['label']).css({'color': 'red'});
			}
		}
	});
}





