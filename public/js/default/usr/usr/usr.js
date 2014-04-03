$(document).ready(function(){
	/**
	 * 修改简历——基本信息
	 */
	$('#btn_modify_resume').click(function(){
		window.open('/usr_resume/modifyresume');
		return false;
	});
	
	
	/**
	 * 修改简历——业务 
	 */
	$('#btn_modify_resumeBiz').click(function(){
		window.open('/usr_resume/modifyresume');
		return false;
	});
	
	
	/**
	 * 修改简历——项目 
	 */
	$('#btn_modify_resumePrj').click(function(){
		window.open('/usr_resume/modifyresume');
		return false;
	});
	
	
	/**
	 * 修改简历——其它 
	 */
	$('#btn_modify_resumeOther').click(function(){
		window.open('/usr_resume/modifyresume');
		return false;
	});
	
	
	/**
	 * 初始化用户中心首页
	 */
	$(function(){
		//邮件通知
		$('input[name="isReceiveNews"][value="' + isReceiveNews + '"]').attr('checked', true);
		//简历公开
		$('input[name="isOpenResume"][value="' + isOpenResume + '"]').attr('checked', true);
	});
	
	
	/**
	 * 邮件通知设定
	 */
	$('#btn_modifyIsReceiveNews').click(function(){
		var isReceiveNews = $('input[name="isReceiveNews"]:checked').val();
		$.ajax({
			url: '/usr_usr/modifyisreceivenews',
			type: 'get',
			async: true,
			dataType: 'json', //xml, json, script or html
			data: {'isReceiveNews': isReceiveNews},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					$('#tip_isReceiveNews').html(data['msg']);
					setTimeout("$('#tip_isReceiveNews').html('')", 5000);
				} else {
					$('#tip_isReceiveNews').html(data['msg']);
				}
			}
		});
	}); //End: $('#btn_modifyIsReceiveNews').click
	
	
	/**
	 * 简历公开设定
	 */
	$('#btn_modifyIsOpenResume').click(function(){
		var isOpenResume = $('input[name="isOpenResume"]:checked').val();
//		console.log(isOpenResume);
		$.ajax({
			url: '/usr_resume/modifyisopenresume',
			type: 'get',
			async: true,
			dataType: 'json', //xml, json, script or html
			data: {'isOpenResume': isOpenResume},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					$('#tip_isOpenResume').html(data['msg']);
					setTimeout("$('#tip_isOpenResume').html('')", 5000);
				} else {
					$('#tip_isOpenResume').html(data['msg']);
				}
			}
		});
	}); //End: $('#btn_modifyIsReceiveNews').click
	
	
	/**
	 * 退会 
	 */
	$('#btn_unsubscribe').click(function(){
		$.ligerDialog.confirm(CONFIRM_UNSUBSCRIBE, function (ret) {
		    if (ret == true) {
		    	$.ajax({
		    		url: '/usr_usr/unsubscribe',
		    		type: 'get',
		    		async: true,
		    		dataType: 'json', //xml, json, script or html
		    		data: null,
		    		success: function(data, textStatus, jqXHR) {
		    			if (data['err'] == 0) {
		    				location.href = '/?pwd=10';
		    			} else {
		    				$('#tip_unsubscribe').html(data['msg']);
		    			}
		    		}
		    	});
		    } //End: if (ret == true)
		});
	});
	
	
	
	
}); //End: $(document).ready