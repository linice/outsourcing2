$(document).ready(function(){
	/**
	 * 清空Memcache
	 */
	$('#btn_flushMc').click(function(){
		ajaxReq('/admin/sys/flushmc');
	});
	
	
	/**
	 * 清空所有资源
	 */
	$('#btn_clearRscs').click(function(){
//		$.ligerDialog.confirm(CONFIRM_DEL + '？', function(r) {
		var r = confirm(CONFIRM_DEL + '？');
		if (r) {
//			console.log(r);
			ajaxReq('/admin/sys/clearrscs');
		};
	});
	
	
	/**
	 * 搜索并保存所有资源
	 */
	$('#btn_searchAndSaveRsc').click(function(){
		ajaxReq('/admin/sys/searchandsaveresources');
	});
	
	
	/**
	 * 将管理员发送给法人、用户或营业员的消息，再通过邮件发送
	 */
	$('#btn_adminSendMail').click(function(){
		ajaxReq('/admin/mail/timeadminsendmail');
	});
	
	
	/**
	 * 在日本VPS上，从中国VPS恢复数据库
	 */
	$('#btn_recoverDbFromCn').click(function(){
		ajaxReq('/admin/sys/recoverdbfromcn');
	});
	
	
	/**
	 * 备份数据库 
	 */
	$('#btn_backupDb').click(function(){
		ajaxReq('/admin/sys/backupdb');
	});
	
	
	
	
}); //End: $(document).ready


/**
 * Ajax执行Action
 * @param string url: ajax请求的url
 */
function ajaxReq(url) {
	$.ajax({
		url: url,
		type: 'get',
		async: true,
		dataType: 'json', //xml, json, script or html
		data: null,
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
				$('#tip_sys').html(data['msg']);
//					setTimeout("$('#tip_sys').html('')", 5000);
			} else {
				$('#tip_sys').html(data['msg']);
			}
		}
	});
}





