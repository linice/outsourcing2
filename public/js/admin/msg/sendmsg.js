$(document).ready(function(){
	/**
	 * 初始化草稿消息
	 */
	$(function() {
		CKEDITOR.replace('content');
		initDraft();
	});
	
	
	/**
	 * 发送消息
	 */
	$('#btn_send').click(function() {
		sendMsg('Y');
	});
	
	
	/**
	 * 存草稿消息
	 */
	$('#btn_save').click(function() {
		sendMsg('N');
	});
	
	
	
}); //End: $(document).ready


/**
 * 初始化草稿消息
 */
function initDraft() {
	if (oMs && JSON.stringify(oMs) != '[]') {
		$.each(oMs.recver, function(k, v) {
			$('input[name="recvers[]"][value="' + v + '"]').attr('checked', true);
		});
		$('#title').val(oMs.title);
		CKEDITOR.instances.content.setData(oMs.content);
		$('#msId').val(oMs.id);
	}
}


/**
 * 发送消息
 * @param string isSent: N, Y
 */
function sendMsg(isSent) {
	$('#content').val(CKEDITOR.instances.content.getData());
	$.ajax({
		url: '/admin/msg/handlesendmsg',
		type: 'post',
		async: true,
		dataType: 'json', //xml, json, script or html
//		data: {'msId': msId, 'recvers': recvers, 'title': title, 'content': content, 'isSent': isSent},
		data: $('#form_sendMsg').serialize() + '&isSent=' + isSent,
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
//				$('#tip_msg').html(data['msg']);
//				setTimeout("$('#tip_msg').html('')", 5000);
				location.href = "/admin/msg/msglist";
			} else {
				$('#tip_msg').html(data['msg']);
			}
		}
	});
}





