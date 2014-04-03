$(document).ready(function(){
	/**
	 * 刷新页面时，根据保存的业务经历信息，初始化业务经历
	 */
	$(function(){
		if (resumeBizs != false) {
			$.each(resumeBizs, function(k, v) {
				//当业务为用户选择，或用户输入的业务名时，初始化业务名
				if (v['biz'].substr(0, 3) == 'os_' || v['biz'].substr(0, 3) == 'db_' 
					|| v['biz'].substr(0, 10) == 'framework_' || v['biz'].substr(0, 4) == 'biz_') {
					$('#' + v['biz']).val(v['biz_name']);
				}
				$('#' + v['biz'] + '_level').val(v['level']);
				$('#' + v['biz'] + '_age').val(v['age']);
			});
		}
	});
	
	
	/**
	 * 保存创建简历——业务
	 */
	$('#btn_saveResumeBiz').click(function(){
//		var btn = $(this);
//		btn.html(BEING_SAVED);
		var enabled = $(this).attr('class');
		if (enabled == 'ENABLED_Y') {
			$(this).attr('class', 'ENABLED_N');
			var resumeCode = $('#resumeCode').val();
			$.ajax({
				url: '/usr_resume/saveresumebiz',
				type: 'post',
				async: false,
				dataType: 'json', //xml, json, script or html
				data: $('#form_resumeBiz').serialize() + '&resumeCode=' + resumeCode,
				success: function(data, textStatus, jqXHR) {
//					btn.html('<img src="/img/default/front/<?=$auth->locale?>/btn_keep.jpg" width="79" height="28" class="btn_marR" />');
					$('#label_os').css('color', 'black');
					$('#label_db').css('color', 'black');
					$('#label_framework').css('color', 'black');
					$('#label_biz').css('color', 'black');
					if (data['err'] == 0) { //用户简历业务信息通过验证，即已保存至数据库，提示 已保存
						$('#tip_resumeBiz').html(data['msg']);
						setTimeout("$('#tip_resumeBiz').html('')", 5000);
					} else if (data['err'] == 1) { //用户简历业务信息保存失败或还没有保存简历——基本信息
						$('#tip_resumeBiz').html(data['msg']);
					} else { //用户简历业务信息不合法，提示不合法的内容
						$('#tip_resumeBiz').html(data['msg']);
						
						if (typeof data['labels'] != 'undefined') {
							var labels = eval(data['labels']);
							$.each(labels, function(k, v){
								$('#' + v).css('color', 'red');
							});
						}
					}
				} //End: success
			}); //End: $.ajax
		} //End: if (enabled == 'ENABLED_Y')
		$(this).attr('class', 'ENABLED_Y');
	});
	
	
	
}); //End: $(document).ready