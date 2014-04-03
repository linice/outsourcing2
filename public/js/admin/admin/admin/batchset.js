$(document).ready(function() {
	/**
	 * 初始化，把input的边框去掉，以及设为只读
	 */
	$(function() {
		$('#form_batchset input').css({'border': 0});
	});
	
	
	/**
	 * 当鼠标聚焦于input标签上时，加上边框，并去掉只读属性
	 */
	$('#form_batchset input').mouseover(function() {
		$(this).css({'border': 'solid 1px black'});
	}).mouseout(function() {
		$(this).css({'border': 0});
	}).change(function() {
		$('#btn_save').attr('class', 'DISABLED_N');
	});
	
	
	/**
	 * 保存
	 */
	$('#btn_save').click(function() {
		var disabled = $(this).attr('class');
		if (disabled == 'DISABLED_Y') {
			$('#tip_batchset').html(NO_CHANGE);
			setTimeout("$('#tip_batchset').html('')", 5000);
		} else {
			$.ajax({
				url: '/admin/admin_admin/savebatchset',
				type: 'get',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: $('#form_batchset').serializeArray(),
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						$('#btn_save').attr('class', 'DISABLED_Y');
						$('#tip_batchset').html(data['msg']);
						setTimeout("$('#tip_batchset').html('')", 5000);
					} else {
						$.each(data['tips'], function(k, v) {
							$('#' + v['label']).html(v['desc']);
						});
					}
				}
			});
		}
	});
	
	
	/**
	 * 数据库备份记录列表
	 */
	$('#dbDumpList').ligerGrid({
		url: '/admin/admin_admin/getdbdumplist',
		checkbox: false,
		rownumbers: true,
		dataAction: 'server', 
		columnWidth: 144,
		pageSize: 20,
		isScroll: false, 
		frozen:false,
		method: 'post',
		width: '100%',
		//onSuccess: showCurrPageEmpDetails,
		columns: [{ display: PATH, name: 'path', width: 500},
				  { display: BEGIN_TIME, name: 'dt_begin'},
				  { display: END_TIME, name: 'dt_end'},
				  { display: RESULT, name: 'status'}
		]
	});
	
	
}); //End:$(document).ready