$(document).ready(function() {
	var today = new Date();
	var yearEnd = today.getFullYear() + 10;
	var yearBegin = yearEnd - 100;
	
	
	/**
	 * 初始化时间选择框：服务生效日期 & 服务截止日期
	 */
	$('#dateBegin, #dateEnd').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
		yearRange: yearBegin + ':' + yearEnd
	});
	
	
	/**
	 * 初始化
	 */
	$(function() {
		//把input的边框去掉，以及设为只读
		$('#form_lpBaseinfo input').css({'border': 0});
		
		//法人信息
		$.each(lp, function(k, v) {
			$('#' + k).val(v);
		});
		
		//按钮是否可用: 承认按钮
		if (lp['enabled'] == 'Y') {
			$('#btn_stop').attr('class', 'ENABLED_Y');
		} else {
			$('#btn_admit').attr('class', 'ENABLED_Y');
		}
		
		
	});
	
	
	/**
	 * 当鼠标聚焦于input标签上时，加上边框;
	 * 当内容有改动时，确定按钮“可用”
	 */
	$('#form_lpBaseinfo input').mouseover(function() {
		$(this).css({'border': 'solid 1px black'});
	}).mouseout(function() {
		$(this).css({'border': 0});
	}).change(function() {
		$('#btn_save').attr('class', 'ENABLED_Y');
	});
	
	
	/**
	 * 保存法人信息
	 */
	$('#btn_save').click(function() {
		var enabled = $(this).attr('class');
		if (enabled == 'ENABLED_N') {
			$('#tip_lpBaseinfo').html(NO_CHANGE);
			setTimeout("$('#tip_lpBaseinfo').html('')", 5000);
		} else {
			$.ajax({
				url: '/admin/admin_lp/modlp',
				type: 'get',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: $('#form_lpBaseinfo').serializeArray(),
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						$('#btn_save').attr('class', 'ENABLED_N');
						$('#tip_lpBaseinfo').html(data['msg']);
						setTimeout("$('#tip_lpBaseinfo').html('')", 5000);
					} else if (data['err'] == 1) {
						$('#btn_save').attr('class', 'ENABLED_N');
						$('#tip_lpBaseinfo').html(data['msg']);
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
	 * 承认法人
	 */
	$('#btn_admit').click(function() {
		var enabled = $(this).attr('class');
		if (enabled == 'ENABLED_N') {
			$('#tip_lpBaseinfo').html(ALREADY_ADMIT);
			setTimeout("$('#tip_lpBaseinfo').html('')", 5000);
		} else {
			$.ajax({
				url: '/admin/admin_lp/checklp',
				type: 'get',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: {'code': lp['code']},
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						$('#btn_admit').attr('class', 'ENABLED_N');
						$('#tip_lpBaseinfo').html(data['msg']);
						setTimeout("$('#tip_lpBaseinfo').html('')", 5000);
					} else {
						$('#btn_admit').attr('class', 'ENABLED_N');
						$('#tip_lpBaseinfo').html(data['msg']);
					}
				}
			});
		}
	});
	
	
	/**
	 * 停止法人服务
	 */
	$('#btn_stop').click(function() {
		var enabled = $(this).attr('class');
		if (enabled == 'ENABLED_N') {
			$('#tip_lpBaseinfo').html(ALREADY_STOP);
			setTimeout("$('#tip_lpBaseinfo').html('')", 5000);
		} else {
			$.ajax({
				url: '/admin/admin_lp/stoplp',
				type: 'post',
				async: false,
				dataType: 'json', //xml, json, script or html
				data: {'code': lp['code']},
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						$('#btn_stop').attr('class', 'ENABLED_N');
						$('#tip_lpBaseinfo').html(data['msg']);
						setTimeout("$('#tip_lpBaseinfo').html('')", 5000);
					} else {
						$('#btn_stop').attr('class', 'ENABLED_N');
						$('#tip_lpBaseinfo').html(data['msg']);
					}
				}
			});
		}
	});
	
	/**
	 * 删除法人服务
	 */
	$('#btn_delete').click(function() {
		$.ajax({
			url: '/admin/admin_lp/deletelp',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: {'code': lp['code']},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					$('#tip_lpBaseinfo').html(data['msg']);
					setTimeout("$('#tip_lpBaseinfo').html('')", 5000);
				} else {
					$('#tip_lpBaseinfo').html(data['msg']);
				}
			}
		});
	});
	
	/**
	 * 查看法人员工
	 */
	$('#btn_viewEmps').click(function() {
		$('#form_dyn').attr('action', '/lp_emp/emplist');
		$('#form_dyn').submit();
	});
	
	
	/**
	 * 查看法人单数消费明细
	 */
	$('#btn_consumeDetail').click(function() {
		$('#form_dyn').attr('action', '/admin/akb/consumeakb');
		$('#form_dyn').submit();
	});
	
	
	/**
	 * 创建入金
	 */
	$('#btn_deposit').click(function() {
		depositAkb();
	});
	
	
	/**
	 * 查询法人入金历史
	 */
	showDepositAkb();
}); //End: $(document).ready


/**
 * 创建入金
 */
function depositAkb() {
	$('#dlg_createDepositAkb').dialog({
		autoOpen: true,
		modal: true,
		width: 400,
		buttons: {
			'确定': function(){
				$.ajax({
					url: '/admin/akb/savedepositakb',
					type: 'post',
					async: false,
					dataType: 'json', //xml, json, script or html
					data: $('#form_createDepositAkb').serializeArray(),
					success: function(data, textStatus, jqXHR) {
						if (data['err'] == 0) { //创建入金成功
//							$('#tip_createDepositAkb').html(data['msg']);
//							setTimeout("$('#tip_createDepositAkb').html('')", 5000);
							$('#dlg_createDepositAkb').dialog('close');
							var mgr = $('#depositAkbList').ligerGetGridManager();
							mgr.loadData();
						} else { //创建入金失败
							$('#tip_createDepositAkb').html(data['msg']);
						}
					}
				});
			},
			'取消': function(){
				$(this).dialog('close');
			}
		}
	});
}


/**
 * 查询法人入金历史
 */
function showDepositAkb() {
	var url = '/admin/akb/getlpdepositakb/lpCode/' + lp['code'];
	
	$('#depositAkbList').ligerGrid({
		url: url,
		checkbox: false,
		rownumbers: true,
		dataAction: 'server', 
		columnWidth: 100,
		pageSize: 20,
		isScroll: false, 
		frozen:false,
		method: 'post',
		width: '100%',
		//detail: {onShowDetail: showDetail, height: 'auto'},
		//onSuccess: funcName,
		columns: [{ display: DATE, name: 'create_date'},
				  { display: DEPOSIT_AMT, name: 'amt'},
				  { display: PURPOSE, name: 'svc_tr'},
				  { display: SERVICE_TAKE_EFFECT_DATE, name: 'date_begin'},
				  { display: SERVICE_DEADLINE, name: 'date_end'},
				  { display: BUY_POINTS, name: 'points'}
		 ]
	});
}