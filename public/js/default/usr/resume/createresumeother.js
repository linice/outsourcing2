$(document).ready(function(){
	var today = new Date();
	var yearEnd = today.getFullYear() + 1;
	var yearBegin = today.getFullYear();
	
	
	//项目开始时间 & 竞价结束日期
	$('#able_work_date, #bid_date_end').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
		yearRange: yearBegin + ':' + yearEnd
	});
	
	
	/**
	 * 根据所选择的“稼动可能日”，决定是否显示日期输入框
	 */
	$('#able_work_date_choice').change(function(){
		if($(this).val() == 'SPECIFY_DATE') {
			$('#able_work_date').css('visibility', 'visible');
		} else {
			$('#able_work_date').css('visibility', 'hidden');
		}
	});
	
	
	/**
	 * 根据所选择的“当前营业状态”，决定是否显示：面试等待及面试结果等待
	 */
	$('#is_apply_prj').change(function(){
		if($(this).val() == 'Y') {
			$('#span_interview_wait').css('visibility', 'visible');
		} else {
			$('#span_interview_wait').css('visibility', 'hidden');
		}
	});
	
	
	/**
	 * 增加不可见公司输入框
	 */
	$('#btn_addUnseenComs').click(function(){
		var html = '&nbsp;&nbsp;<input name="unseen_coms[]" type="text" value="" /><br />';
		$('#td_unseenComs').append(html);
	});
	
	
	/**
	 * 如果用户在创建简历，或刷新页面时，初始化已保存的简历信息
	 */
	$(function(){
		if (resume) {
			//把input text找出来，循环赋值
			$('#form_resumeOther').find(':text, select').each(function() {
				$(this).val(resume[$(this).attr('id')]);
			});
			$('#form_resumeOther').find(':radio').each(function() {
				var radioName = $(this).attr('name');
				$(':radio[name="' + radioName + '"][value="' + resume[radioName] + '"]').attr('checked', true);
			});
			var insurances = JSON.parse(resume['insurance']);
			if (insurances.length > 0) {
				$.each(insurances, function(k, v) {
					$('input[name="insurance[]"][value="' + v + '"]').attr('checked', true);
				});
			}
			var expWorkplaces = JSON.parse(resume['exp_workplace']);
			if (expWorkplaces.length > 0) {
				$.each(expWorkplaces, function(k, v) {
					$('input[name="exp_workplace[]"][value="' + v + '"]').attr('checked', true);
				});
			}
			var unseenComs = JSON.parse(resume['unseen_coms']);
			if (unseenComs.length > 0) {
				$.each(unseenComs, function(k, v) {
					if (unseenComs.length == 1) {
						$('input[name="unseen_coms[]"]').val(v);
					} else {
						var html = '&nbsp;&nbsp;<input name="unseen_coms[]" type="text" value="' + v + '" /><br />';
						$('#td_unseenComs').append(html);
					}
				});
			}
			if (resume['able_work_date_choice'] == 'SPECIFY_DATE') {
				$('#able_work_date').css('visibility', 'visible');
			}
			if (resume['is_apply_prj'] == 'Y') {
				$('#span_interview_wait').css('visibility', 'visible');
			}
		}
	});
	
	
	/**
	 * 保存创建简历——其它
	 */
	$('#btn_saveResumeOther').click(function(){
//		var btn = $(this);
//		btn.html(BEING_SAVED);
		var enabled = $(this).attr('class');
		if (enabled == 'ENABLED_Y') {
			$(this).attr('class', 'ENABLED_N');
			var resumeCode = $('#resumeCode').val();
			
			$.ajax({
				url: '/usr_resume/saveresumeother',
				type: 'post',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: $('#form_resumeOther').serialize() + '&resumeCode=' + resumeCode,
				success: function(data, textStatus, jqXHR) {
//				btn.html('<img src="/img/default/front/<?=$auth->locale?>/btn_keep.jpg" width="79" height="28" class="btn_marR" />');
					$('label').css('color', 'black');
					if (data['err'] == 0) { //用户简历-其它，通过验证，即已保存至数据库，提示 已保存
						$('#tip_resumeOther').html(data['msg']);
						setTimeout("$('#tip_resumeOther').html('')", 3000);
					} else if (data['err'] == 1) { //用户简历-其它保存失败或还没有保存简历——基本信息
						$('#tip_resumeOther').html(data['msg']);
					} else { //用户简历基本信息不合法，提示不合法的内容
						var html = '';
						data['msg'] = eval(data['msg']);
						$.each(data['msg'], function(k, v){
							html += v + '<br />';
						});
						$('#tip_resumeOther').html(html);
						
						if (typeof data['labels'] != 'undefined') {
							var labels = eval(data['labels']);
							$.each(labels, function(k, v) {
								$('#' + v).css('color', 'red');
							});
						}
					}
				}
			}); //End: $.ajax
		} //End: if (enabled == 'ENABLED_Y')
		$(this).attr('class', 'ENABLED_Y');
	});
	
	
	/**
	 * 设定员工推荐度竞价排名
	 */
	$('#btn_set').click(function(){
		//按推荐度从高到低排序列出员工
		$('#recmdEmpList').ligerGrid({
	        url: '/usr_resume/getbidtalent',
	        checkbox: false,
	        rownumbers: true,
	        dataAction: 'server', 
	        page: 1, 
	        pageSize: 20,
	        width: '100%',
//	        height:'100%'
        	columns: [
        	          { display: PERSONNEL_ID, name: 'talent_code', minWidth: 80 },
        	          { display: SEX, name: 'sex', minWidth: 50 },
        	          { display: AGE, name: 'birthday', minWidth: 50 },
        	          { display: EXPERIENCE, name: 'pr', minWidth: 200 },
        	          { display: WORK_DATE, name: 'able_work_date', minWidth: 100 },
        	          { display: UNIT_PRICE_WITH_UNIT, name: 'salary_min', minWidth: 100 }, 
        	          { display: POINTS, name: 'bid_points', minWidth: 100 },
        	]
	    });
	    
		//弹出设定竞价点数对话框
		$('#dlg_recmdEmp').dialog({
			autoOpen: true,
			modal: true,
			width: 800,
			buttons: {
				'确定': function(){
					var balance = Number($('#balance').val());
					var point = Number($('#point').val());
					if (point > balance) {
						$('#tip_recmdEmp').html(point_set_can_not_be_larger_than_balance_point);
						return;
					}
					$('#tip_recmdEmp').html('');
					$('#bid_points').val(point);
					$(this).dialog('close');
				},
				'取消': function(){
					$(this).dialog('close');
				}
			}
		});
	});
	
	
	
}); //End: $(document).ready