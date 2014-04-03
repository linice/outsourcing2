var today = new Date();
var yearEnd = today.getFullYear();
var yearBegin = yearEnd - 100;


$(document).ready(function(){
	
	/**
	 * 初始简历————项目经验
	 */
	$(function(){
		initResumePrj();
	});
	
	
	/**
	 * 添加项目经验项行
	 */
	$('#btn_addPrj').click(function() {
		//增加项目行
		addPrj();
		
        return false;
	});
	
	
	/**
	 * 删除项目经验项行
	 */
	$('#btn_delPrj').click(function(){
		delResumePrj();
	});
	
	
	/**
	 * 保存简历-项目经验
	 */
	$('#btn_saveResumePrj').click(function(){
		saveResumePrj();
	});
	
	
	
}); //End: $(document).ready




/**
 * 初始化简历——项目经验
 */
function initResumePrj() {
	var prjsCnt = resumePrjs.length;
	var i = 0;
	if (prjsCnt == 0) {
		addPrj();
	} else {
		for (i = 0; i < prjsCnt; i++) {
			addPrj();
		}
	}
	if (prjsCnt == 0 || prjsCnt == 1) {
		$('input[name="chkPrjs[]"]:eq(0)').attr('onclick', 'javascript:return false;');
	}
	i = 0;
	$.each(resumePrjs, function(k, v) {
		$('input[name="dateBegins[]"]:eq(' + i + ')').val(v['date_begin']);
		$('input[name="dateEnds[]"]:eq(' + i + ')').val(v['date_end']);
		$('textarea[name="prjContents[]"]:eq(' + i + ')').val(v['content']);
		$('textarea[name="machine_oss[]"]:eq(' + i + ')').val(v['machine_os']);
		$('textarea[name="lang_dbs[]"]:eq(' + i + ')').val(v['lang_db']);
		$('textarea[name="poss[]"]:eq(' + i + ')').val(v['pos']);
		if (v['seg_mgt'] == 'Y') {
			$('input[name="seg_mgts[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_biz_analy'] == 'Y') {
			$('input[name="seg_bizAnalys[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_cmpnt_def'] == 'Y') {
			$('input[name="seg_cmpntDefs[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_preliminary_design'] == 'Y') {
			$('input[name="seg_preliminaryDesigns[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_db_design'] == 'Y') {
			$('input[name="seg_dbDesigns[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_detail_design'] == 'Y') {
			$('input[name="seg_detailDesigns[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_coding'] == 'Y') {
			$('input[name="seg_codings[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_unit_test'] == 'Y') {
			$('input[name="seg_unitTests[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_module_test'] == 'Y') {
			$('input[name="seg_moduleTests[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_integration_test'] == 'Y') {
			$('input[name="seg_integrationTests[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_advisory'] == 'Y') {
			$('input[name="seg_advisorys[]"]:eq(' + i + ')').attr('checked', true);
		}
		if (v['seg_maintaince'] == 'Y') {
			$('input[name="seg_maintainces[]"]:eq(' + i + ')').attr('checked', true);
		}
		i++;
	});
}


/**
 * 保存简历——项目经验
 */
function saveResumePrj() {
//	var btn = $('#btn_saveResumePrj');
//	btn.html(BEING_SAVED);
	var enabled = $(this).attr('class');
	if (enabled == 'ENABLED_Y') {
		$(this).attr('class', 'ENABLED_N');
		//声明prj变量
		var dateBegins = new Array();
		var dateEnds = new Array();
		var prjContents = new Array();
		var machine_oss = new Array();
		var lang_dbs = new Array();
		var poss = new Array();
		var seg_mgts = new Array();
		var seg_bizAnalys = new Array();
		var seg_cmpntDefs = new Array();
		var seg_preliminaryDesigns = new Array();
		var seg_dbDesigns = new Array();
		var seg_detailDesigns = new Array();
		var seg_codings = new Array();
		var seg_unitTests = new Array();
		var seg_moduleTests = new Array();
		var seg_integrationTests = new Array();
		var seg_advisorys = new Array();
		var seg_maintainces = new Array();
		var i = 0;
		
		//获取项目经验数据
		var resumeCode = $('#resumeCode').val();
		var prjCnt = $('#prjCnt').val();
		i = 0;
		$('input[name="dateBegins[]"]').each(function(){
			dateBegins[i++] = $(this).val();
		});
		i = 0;
		$('input[name="dateEnds[]"]').each(function(){
			dateEnds[i++] = $(this).val();
		});
		i = 0;
		$('textarea[name="prjContents[]"]').each(function(){
			prjContents[i++] = $(this).val();
		});
		i = 0;
		$('textarea[name="machine_oss[]"]').each(function(){
			machine_oss[i++] = $(this).val();
		});
		i = 0;
		$('textarea[name="lang_dbs[]"]').each(function(){
			lang_dbs[i++] = $(this).val();
		});
		i = 0;
		$('textarea[name="poss[]"]').each(function(){
			poss[i++] = $(this).val();
		});
		i = 0;
		$('input[name="seg_mgts[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_mgts[i++] = 'Y';
			} else {
				seg_mgts[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_bizAnalys[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_bizAnalys[i++] = 'Y';
			} else {
				seg_bizAnalys[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_cmpntDefs[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_cmpntDefs[i++] = 'Y';
			} else {
				seg_cmpntDefs[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_preliminaryDesigns[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_preliminaryDesigns[i++] = 'Y';
			} else {
				seg_preliminaryDesigns[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_dbDesigns[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_dbDesigns[i++] = 'Y';
			} else {
				seg_dbDesigns[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_detailDesigns[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_detailDesigns[i++] = 'Y';
			} else {
				seg_detailDesigns[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_codings[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_codings[i++] = 'Y';
			} else {
				seg_codings[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_unitTests[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_unitTests[i++] = 'Y';
			} else {
				seg_unitTests[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_moduleTests[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_moduleTests[i++] = 'Y';
			} else {
				seg_moduleTests[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_integrationTests[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_integrationTests[i++] = 'Y';
			} else {
				seg_integrationTests[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_advisorys[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_advisorys[i++] = 'Y';
			} else {
				seg_advisorys[i++] = 'N';
			}
		});
		i = 0;
		$('input[name="seg_maintainces[]"]').each(function(){
			if ($(this).attr('checked') == 'checked') {
				seg_maintainces[i++] = 'Y';
			} else {
				seg_maintainces[i++] = 'N';
			}
		});
		
		$.ajax({
			url: '/usr_resume/saveresumeprj',
			type: 'post',
			async: true,
			dataType: 'json', //xml, json, script or html
			data: {'resumeCode': resumeCode, 'prjCnt': prjCnt,
				'dateBegins': dateBegins, 'dateEnds': dateEnds,
				'prjContents': prjContents, 'machine_oss': machine_oss,
				'lang_dbs': lang_dbs, 'poss': poss,
				'seg_mgts': seg_mgts, 'seg_bizAnalys': seg_bizAnalys,
				'seg_cmpntDefs': seg_cmpntDefs, 'seg_preliminaryDesigns': seg_preliminaryDesigns,
				'seg_dbDesigns': seg_dbDesigns, 'seg_detailDesigns': seg_detailDesigns,
				'seg_codings': seg_codings, 'seg_unitTests': seg_unitTests,
				'seg_moduleTests': seg_moduleTests, 'seg_integrationTests': seg_integrationTests,
				'seg_advisorys': seg_advisorys, 'seg_maintainces': seg_maintainces
			},
			success: function(data, textStatus, jqXHR) {
//			btn.html('<img src="/img/default/front/<?=$auth->locale?>/btn_keep.jpg" width="79" height="28" class="btn_marR" />');
//			var cnt = 20;
				$('input[name="chkPrjs[]"]').parent().css('background-color', 'white');
				if (data['err'] == 0) { //用户简历-项目经验通过验证，即已保存至数据库，提示已保存
					$('#tip_resumePrj').html(data['msg']);
					setTimeout("$('#tip_resumePrj').html('')", 3000);
				} else if (data['err'] == 1) { //用户简历-项目经验保存失败或还没有保存简历——基本信息
					$('#tip_resumePrj').html(data['msg']);
				} else { //用户简历-项目经验不合法，提示不合法的内容
					$('#tip_resumePrj').html(data['msg']);
					if (typeof data['labels'] != 'undefined') {
						var labels = eval(data['labels']);
						$.each(labels, function(k, v){
//						$('#' + v).css('color', 'red');
							$('input[name="chkPrjs[]"]:eq(' + v + ')').parent().css('background-color', 'red');
						});
					}
				}
			}
		}); //End: $.ajax
	} //End: if (enabled == 'ENABLED_Y')
	$(this).attr('class', 'ENABLED_Y');
}


/**
 * 添加项目经验项
 */
function addPrj() {
	var html = '<tr>'
		+ '<td height="80" align="center" bgcolor="#FFFFFF"><input name="chkPrjs[]" type="checkbox" value="Y" /></td>'
		+ '<td align="center" bgcolor="#FFFFFF">'
			+ '<p><input type="text" name="dateBegins[]" class="myinput_w76" value="" /></p>'
			+ '<p align="center">～</p>'
			+ '<p><input type="text" name="dateEnds[]" class="myinput_w76" value="" style="text-align:right;" /></p>'
		+ '</td>'
		+ '<td align="center" bgcolor="#FFFFFF">'
			+ '<textarea name="prjContents[]" rows="3" cols="36"></textarea>'
		+ '</td>'
		+ '<td align="center" bgcolor="#FFFFFF">'
			+ '<textarea name="machine_oss[]" rows="3" cols="4"></textarea>'
		+ '</td>'
		+ '<td align="center" bgcolor="#FFFFFF">'
			+ '<textarea name="lang_dbs[]" rows="3" cols="4"></textarea>'
		+ '</td>'
		+ '<td bgcolor="#FFFFFF">'
			+ '<textarea name="poss[]" rows="3" cols="4"></textarea>'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_mgts[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_bizAnalys[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_cmpntDefs[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_preliminaryDesigns[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_dbDesigns[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_detailDesigns[]" type="checkbox" value=""/>'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_codings[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_unitTests[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_moduleTests[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_integrationTests[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_advisorys[]" type="checkbox" value="" />'
		+ '</td>'
		+ '<td align="center" valign="middle" bgcolor="#FFFFFF">'
			+ '<input name="seg_maintainces[]" type="checkbox" value="" />'
		+ '</td>'
		+ '</tr>';
	$('#tbl_prj').append(html);
	
	//项目开始时间 & 项目结束时间
	$('input[name="dateBegins[]"], input[name="dateEnds[]"]').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm',
		yearRange: yearBegin + ':' + yearEnd
	});
}


/**
 * 删除选中的简历——项目经验
 */
function delResumePrj() {
	$('input[name="chkPrjs[]"]:checked').each(function(){
//		$(this + ':parent:parent').remove();
		$(this).parent().parent().remove();
	});
}



