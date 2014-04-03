/**
 * be included in: talentsearchresult.phtml
 */
$(document).ready(function(){
	/**
	 * 初始化检索结果，根据检索页面传递过来的条件
	 */
	$(function(){
		var parm = 'parm=' + advancedSearchParms;
		showTalentList(parm);
	});
	
	
	/**
	 * 更改人才高级检索条件
	 */
	$('#btn_changeAdvancedSearchParms').click(function() {
		$('#form_advancedSearchParms').submit();
	});

	
	/**
	 * 提案
	 */
	$('#btn_propose').click(function(){
		window.open('/front_talent/talentpropose');
		return false;
	});
	
	$("#btn_invite").click(function() {
		var manager = $("#talentList").ligerGetGridManager();
		var rows = manager.getSelecteds();
		inviteCase(rows);
		return false;
	});
}); //End: $(document).ready


/**
 * 显示人才列表
 * @param 
 */
function showTalentList(parm) {
	var url = '/front_talent/gettalentlist';
	if (arguments.length == 1) {
		url = '/front_talent/gettalentlist/?' + parm;
	}
	$('#talentList').ligerGrid({
		url: url,
		checkbox: true,
		rownumbers: true,
		dataAction: 'server', 
		page: 1, 
		pageSize: 20,
		isScroll: false, 
		frozen:false,
		method: 'get',
		width: '100%', //height:'100%',
		columnWidth: 130,
		detail: {onShowDetail: showTalentDetail, height: 'auto'},
		columns: [
		          { display: USR_CODE, name: 'talent_code', render:UsrCodeFunction},
		          { display: USR_NAME, name: 'fullname', render: UsrNameFunction},
		          { display: SEX, name: 'tr_sex'},
		          { display: AGE, name: 'age'},
		          { display: TEL, name: 'tel'},
		          { display: UPDATE_DATE, name: 'update_date'}
		 ]
	});
	
	
} //End: function showEmpList()

function UsrCodeFunction(rowData, rowIndex) {
	return UsrCodeFormatFunction(rowData, 'code');
}
function UsrNameFunction(rowData, rowIndex) {
	return UsrNameFormatFunction(rowData, 'fullname', 'code');
}

/**
 * 显示人才详情
 * @returns
 */
function showTalentDetail(row, detailPanel, callback) {
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
				//html为人才详细信息
				var resume = data['resume'];
				
				$(grid).html($('#talentDetail').html());
				$(grid).find('span[id]').each(function() {
					$(this).html(resume[$(this).attr('id')]);
				});
				$(grid).find('#btn_invite').click(function() {
					inviteCase(null, resume['code']);
				});
				$(grid).find('#btn_perview').click(function() {
					window.open('/usr_resume/previewresume/resumeCode/' + resume['code']);
				});
			} else { //一般不会出现错误，所以在Controller里没有设计出错输出
				$('#tip_talent').html('Error');
			}
		}
	});
}


function inviteCase(rows, code, name) {
	if ((!rows || rows.length == 0) && !code) {
		alert(PLEASE_CHOISE_INVITE_EMP);
		return false;
	}
	rows = rows || [{code: code, name: name}];
	$.ligerDialog.confirm(SURE_CHOISE_INVITE_EMP, TITLE_LP_INVITE, function(e) {
		if (!e) return false;
		var ids = "";
		var flag = true;
		$.each(rows, function(i, v) {
			ids += v.code;
			ids += ",";
		});
		if (!flag) {
			alert(INVATE_LP_EMP_SELF);
			return false;
		}
		ids = ids.substring(0, ids.length-1);
		$("#dynForm").attr("action", "/front_invite/chooseinvitecase");
		$("#ids").val(ids);
		$("#dynForm").submit();
	});
}


