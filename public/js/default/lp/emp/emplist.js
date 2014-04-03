var manager;
$(document).ready(function(){
	$(function() {
		var lpCode = $('#lpCode').val();
		var url = '/lp_emp/getemplist/lpCode/' + lpCode;
		
		manager = $('#empList').ligerGrid({
			url: url,
			checkbox: true,
			rownumbers: true,
			dataAction: 'server', 
			columnWidth: 144,
			pageSize: 20,
			isScroll: false, 
			frozen:false,
			method: 'post',
			width: '100%',
			detail: {onShowDetail: showDetail/*showEmpDetail*/, height: 'auto'},
			//onSuccess: showCurrPageEmpDetails,
			columns: [{ display: EMP_CODE, name: 'talent_code', render:UsrCodeFunction},
			          { display: EMP_NAME, name: 'fullname', render:UsrNameFunction},
			          { display: SEX, name: 'tr_sex'},
			          { display: AGE, name: 'age'},
			          { display: TEL, name: 'tel'},
			          { display: UPDATE_DATE, name: 'update_date'}
			]
		});
	});
	
	function UsrCodeFunction(rowData, rowIndex) {
		return UsrCodeFormatFunction(rowData, 'code');
	}
	function UsrNameFunction(rowData, rowIndex) {
		return UsrNameFormatFunction(rowData, 'fullname', 'code');
	}
	
	/**
	 * 员工检索
	 */
	$('#searchBtn').click(function(){
		var searchKey = $('#searchKey').val();
		var searchVal = $('#searchVal').val();
		var hasApply = $(':checkbox[name="hasApply"]:checked').val();
		manager.setOptions({parms: {'searchKey': searchKey, 'searchVal': searchVal, 'hasApply': hasApply}});
		manager.loadData();
	});
	
	
	$("#cases_expend").click(function() {
		expendDetailAll(manager, manager.getData().length);
	});
	
	
	$('#btn_delEmps').click(function() {
		var rows = manager.getSelectedRows();
		delEmps(rows);
	});
	
	
	$("#btn_proposal").click(function() {
		var maneger = $('#empList').ligerGetGridManager();
		var rows = maneger.getSelecteds();
		if (!rows || rows.length == 0) {
			alert(PLEASE_CHOISE_PROPOSAL_EMP);
		}
		var html = ['<div style="width:500px;"><div class="notice_con">'+SURE_PROPOSAL_CHOOSE_EMP+'</div>'];
		html.push('<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tab_del">');
		html.push('<tr>');
		html.push('<th width="25%">'+EMP_CODE+'</th>');
		html.push('<th width="75%" align="left"> '+EMP_NAME+' </th>');
		html.push('</tr>');
		$.each(rows, function(i, v) {
			html.push('<tr>');
			html.push('<th width="25%">'+v.talent_code+'</th>');
			html.push('<th width="75%" align="left"> '+v.fullname+' </th>');
			html.push('</tr>');
		});
		html.push('</table>');
		html.push('</div>');
		html = html.join("");
		ligerConfirm(TITLE_PROPOSAL_EMP, html, {width: 550}, function() {
			var ids = "";
			$.each(rows, function(i, v) {
				ids += v.code;
				ids += ",";
			});
			ids = ids.substring(0, ids.length-1);
			$("#dynForm").attr("action", "/lp_talent/talentpropose");
			$("#dynForm").attr("target", "_blank");
			$("#dynForm input[name='ids']").val(ids);
			$("#dynForm").submit();
		});
		return false;
	});
}); //End: $(document).ready


/**
 * 显示员工详情
 */
function showDetail(row, detailPanel, callback) {
	var grid = document.createElement('div'); 
    $(detailPanel).append(grid);
	//查询人才详情
	$.ajax({
		url: '/usr_resume/gettalentdetail',
		type: 'get',
		async: true,
		dataType: 'json', //xml, json, script or html
		data: {'resumeCode': row.code},
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
				var resume = data['resume'];
				$(grid).html($('#empDetail').html());
				$(grid).find('span[id]').each(function() {
					$(this).html(resume[$(this).attr('id')]);
				});
				$(grid).find('#btn_preview').click(function() {
					window.open('/usr_resume/previewresume/resumeCode/' + resume['code']);
				});
				$(grid).find('#btn_edit').click(function() {
					window.open('/usr_resume/modifyresume/resumeCode/' + resume['code']);
				});
				$(grid).find('#btn_del').click(function() {
					delEmps([row]);
				});
				$(grid).find('#btn_viewApply').click(function() {
					viewApply(resume['code']);
				});
			} else { //一般不会出现错误，所以在Controller里没有设计出错输出
				$('#tip_talent').html('Error');
			}
		}
	});
}


/**
 * 批量删除员工
 */
function delEmps(rows) {
	if (!rows || rows.length == 0) {
		alert(PLEASE_SELECT_ROW);
		return false; 
	}
	var resumeCodes = [];
	$.each(rows, function(k, v) {
		resumeCodes[k] = v.code;
	});
	$.ligerDialog.confirm(CONFIRM_DEL + '?', function(ret) {
		if (ret) {
			$.ajax({
				url: '/lp_emp/delemp',
				type: 'post',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: {'resumeCodes': resumeCodes},
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						alert(data['msg']);
						$("#searchBtn").click();
					} else {
						alert(data['msg']);
					}
				}
			});
		}
	});
}

function viewApply(code) {
	$("#dynForm").attr("action", "/lp_caselist/collectcaselist");
	$("#dynForm").attr("target", "_blank");
	$("#dynForm input[name='code']").val(code);
	$("#dynForm").submit();
}