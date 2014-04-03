/**
 * 包含于usr.phtml，新增普通用户列表页面
 */
$(document).ready(function(){
	/**
	 * 初始化用户基本信息
	 */
	$(function(){
		//简历公开
		$('input[name="isOpenResume"][value="' + isOpenResume + '"]').attr('checked', true);
	});
	
	
	/**
	 * 简历公开设定
	 */
	$('#btn_modifyIsOpenResume').click(function(){
		var resumeCode = $('#resumeCode').val();
		var isOpenResume = $('input[name="isOpenResume"]:checked').val();
		$.ajax({
			url: '/usr_resume/modifyisopenresume',
			type: 'get',
			async: true,
			dataType: 'json', //xml, json, script or html
			data: {'resumeCode': resumeCode, 'isOpenResume': isOpenResume},
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
	
}); //End: $(document).ready





