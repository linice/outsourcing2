/**
 * 列表中处理取消关注案件事件
 * 前台案件检索列表，
 * @param rows		需要取消关注的行
 * @param manager	当前列表的ligerui_manager
 * @param succFun	取消成功后的执行函数
 */
function cancelAttentionCase(rows, manager, succFun) {
	if(!valLogin()) {
		return false;
	}
	if (!rows || rows.length == 0) {
		alert(cancel_attention_selected_null);
		return false;
	}
	var html = ['<div style="width:500px;"><div class="notice_con">'+SURE_CANCEL_INTEREST_CHOOSE_CASE+'</div>'];
	html.push('<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tab_del">');
	html.push('<tr>');
	html.push('<th width="25%">'+CASE_ID+'</th>');
	html.push('<th width="75%" align="left"> '+CASE_NAME+' </th>');
	html.push('</tr>');
	$.each(rows, function(i, v) {
		html.push('<tr>');
		html.push('<th width="25%">'+v.code+'</th>');
		html.push('<th width="75%" align="left"> '+v.name+' </th>');
		html.push('</tr>');
	});
	html.push('</table>');
	html.push('</div>');
	html = html.join("");
	ligerConfirm(TITLE_CANCEL_INTEREST, html, {width: 550}, function() {
		var ids = "";
		var flag = true;
		$.each(rows, function(i, v) {
			if (!v.attention_usr_code) {
				alert(ERROR_CHOOSE_INTEREST_CASE)
				flag = false;
				return false;
			}
			ids += v.code;
			ids += ",";
		});
		if (!flag) return false;
		ids = ids.substring(0, ids.length-1);
		$.ajax({
			url: '/front_case/cancelattention',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: {ids: ids},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					if (!!succFun) {
						//如果有成功后的操作函数，则直接执行之
						succFun.call(data, rows)
					} else {
						//如果没有，则认为是在列表中进行操作
						alert(data['msg']);
						$.each(rows, function(i, v) {
							var rowObj = manager.getRowObj(rows[i]);
							var nextrow = $(rowObj).next("tr.l-grid-detailpanel");
							nextrow.find("#cancelAttentionBtn").hide();
							nextrow.find("#attentionBtn").show()
							rows[i].attention_usr_code = '';
						});
					}
				} else {
					alert(data['msg']);
				}
			}
		});
	});
}

/**
 * 列表中处理关注案件事件
 * 前台案件检索列表，
 * @param rows		需要取消关注的行
 * @param manager	当前列表的ligerui_manager
 * @param succFun	关注成功后的执行函数
 */
function attentionCase(rows, manager, succFun) {
	if(!valLogin()) {
		return false;
	}
	if (!rows || rows.length == 0) {
		alert(attention_selected_null);
		return false;
	}
	var html = ['<div style="width:500px;"><div class="notice_con">'+SURE_INTEREST_CHOOSE_CASE+'</div>'];
	html.push('<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tab_del">');
	html.push('<tr>');
	html.push('<th width="25%">'+CASE_ID+'</th>');
	html.push('<th width="75%" align="left"> '+CASE_NAME+' </th>');
	html.push('</tr>');
	$.each(rows, function(i, v) {
		html.push('<tr>');
		html.push('<th width="25%">'+v.code+'</th>');
		html.push('<th width="75%" align="left"> '+v.name+' </th>');
		html.push('</tr>');
	});
	html.push('</table>');
	html.push('</div>');
	html = html.join("");
	ligerConfirm(TITLE_INTEREST, html, {width: 550}, function() {
		var ids = "";
		var flag = true;
		$.each(rows, function(i, v) {
			if (!!v.attention_usr_code) {
				alert(ERROR_CHOOSE_UNINTEREST_CASE)
				flag = false;
				return false;
			}
			ids += v.code;
			ids += ",";
		});
		if (!flag) return false;
		ids = ids.substring(0, ids.length-1);
		$.ajax({
			url: '/front_case/attention',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: {ids: ids},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					if (!!succFun) {
						//如果有成功后的操作函数，则直接执行之
						succFun.call(data, rows)
					} else {
						//如果没有，则认为是在列表中进行操作
						alert(data['msg']);
						$.each(rows, function(i, v) {
							var rowObj = manager.getRowObj(rows[i]);
							var nextrow = $(rowObj).next("tr.l-grid-detailpanel");
							nextrow.find("#attentionBtn").hide();
							nextrow.find("#cancelAttentionBtn").show();
							rows[i].attention_usr_code = '1';
						});
					}
				} else {
					alert(data['msg']);
				}
			}
		});
	});
}

/**
 * 查看案件详情
 * 前台案件检索列表，
 * @param case_id	案件ID
 * @param btn		详情页面可以显示的按钮
 * @param form		case_id所在的Form，默认为dynForm
 * @param applyId	应聘ID，需要应聘取消的时候传此参数
 */
function viewCase(caseCode, btn, form, applyId) {
	form = form || $("#dynForm");
	if (form.length == 0) {
		window.open("/front_case/casedetail/caseCode/"+caseCode);
	} else {
		form.attr("action", "/front_case/casedetail/caseCode/"+caseCode);
		if (!!applyId)
			form.find("#applyId").val(applyId);
		form.attr("target", "_blank");
		form.find("#btnlist").val(btn);
		form.submit();
	}
}

/**
 * 案件应聘管理
 * 法人-案件一览，
 * @param case_id	案件ID
 * @param btn		详情页面可以显示的按钮
 * @param form		case_id所在的Form，默认为dynForm
 */
function applyManager(caseCode, tab) {
	if(!valLogin()) {
		return false;
	}
	if (!!tab)
		window.open("/lp_caseapply/caseapplymgt/caseCode/"+caseCode+"/t/"+tab);
	else
		window.open("/lp_caseapply/caseapplymgt/caseCode/"+caseCode);
}

/**
 * 在列表中应聘案件，法人应聘，个人应聘通用
 * 前台案件检索列表，
 * @param rows
 * @param manager
 * @param succFun
 */
function applyCase(row, manager, succFun) {
	if(!valLogin()) {
		return false;
	}
	if (!row) {
		alert(PLEASE_CHOISE_APPLY_CASE);
		return false;
	}
	$.ligerDialog.confirm(SURE_CHOISE_APPLE_CASE, TITLE_APPLY_CASE, function(e) {
		if (!e) return false;
		if (isMember()) {
			window.open("/front_apply/applycheck/caseCode/"+row['code']);
		} else if (isLp()) {
			$.ajax({
				url: "/front_apply/applycheck/",
				type: 'post',
				async: false,
				dataType: 'json', //xml, json, script or html
				data: {caseCode: row['code']},
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						window.open("/front_apply/chooseemp/caseCode/"+row['code'])
					} else {
						alert(data['msg']);
					}
				}
			});
		}
	});
}

/**
 * 列表中法人关闭案件
 * 法人-案件一览
 * @param rows
 * @param manager
 * @param succFun
 */
function closeCase(rows, manager, succFun) {
	if(!valLogin()) {
		return false;
	}
	if (!rows || rows.length == 0) {
		alert(case_close_selected_null);
		return false;
	}
	var html = ['<div style="width:500px;"><div class="notice_con">'+SURE_CLOSE_CHOOSE_CASE+'</div>'];
	html.push('<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tab_del">');
	html.push('<tr>');
	html.push('<th width="25%">'+CASE_ID+'</th>');
	html.push('<th width="75%" align="left"> '+CASE_NAME+' </th>');
	html.push('</tr>');
	$.each(rows, function(i, v) {
		html.push('<tr>');
		html.push('<th width="25%">'+v.code+'</th>');
		html.push('<th width="75%" align="left"> '+v.name+' </th>');
		html.push('</tr>');
	});
	html.push('</table>');
	html.push('</div>');
	html = html.join("");
	ligerConfirm(TITLE_CLOSE_CASE, html, {width: 550}, function() {
		var ids = "";
		var flag = true;
		$.each(rows, function(i, v) {
			if (v.type == 'C') {
				alert(ERROR_CHOOSE_UNCLOSED_CASE);
				flag = false;
				return false;
			}
			ids += v.id;
			ids += ",";
		});
		if (!flag) return false;
		ids = ids.substring(0, ids.length-1);
		$.ajax({
			url: '/lp_case/caseclose',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: {ids: ids},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					alert(data['msg']);
					$.each(rows, function(i, v) {
						rows[i].type = 'C'
					})
				} else {
					alert(data['msg']);
				}
			}
		});
	});
}

/**
 * 个人取消应聘
 * @deprecated
 * @param rows
 * @param caseCode
 * @param caseName
 * @param status
 * @returns {Boolean}
 */
function cancelApplyCaseForUsr(rows, manager, form, succFun) {
	if(!valLogin()) {
		return false;
	}
	if (!rows || rows.length == 0) {
		alert(PLEASE_CHOISE_CANCEL_APPLY_CASE);
		return false;
	}
	form = form || $("#dynForm");
	var html = ['<div style="width:500px;"><div class="notice_con">'+SURE_CANCEL_APPLY_CHOOSE_CASE+'</div>'];
	html.push('<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tab_del">');
	html.push('<tr>');
	html.push('<th width="25%">'+CASE_ID+'</th>');
	html.push('<th width="75%" align="left"> '+CASE_NAME+' </th>');
	html.push('</tr>');
	$.each(rows, function(i, v) {
		html.push('<tr>');
		html.push('<th width="25%">'+v.code+'</th>');
		html.push('<th width="75%" align="left"> '+v.name+' </th>');
		html.push('</tr>');
	});
	html.push('</table>');
	html.push('</div>');
	html = html.join("");
	ligerConfirm(TITLE_CANCEL_APPLY_CASE, html, {width: 550}, function() {
		var ids = "";
		$.each(rows, function(i, v) {
			ids += v.apply_id;
			ids += ",";
		});
		ids = ids.substring(0, ids.length-1);
		$.ajax({
			url: '/usr_caseapply/valcancelapplycase',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: {ids: ids},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					form.attr("action", "/usr_caseapply/cancelapplycase");
					form.find("#ids").val(ids);
					form.submit();
				} else {
					alert(data['msg']);
				}
			}
		});
		return false;
	});
}