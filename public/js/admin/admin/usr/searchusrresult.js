/**
 * be included in: searchusr.phtml
 */
$(document).ready(function(){
	/**
	 * 初始化检索结果，根据检索页面传递过来的条件
	 */
	$(function(){
		var parm = '';
		if (searchType == 'SIMPLE') {
			$('#searchKey').val(searchKey);
			$('#searchVal').val(searchVal);
			parm = 'searchType=SIMPLE&searchKey=' + searchKey + '&searchVal=' + searchVal;
		} else {
			parm = 'searchType=ADVANCED&parm=' + advancedSearchParms;
		}
		showTalentList(parm);
	});
	
	
	/**
	 * 更改人才高级检索条件
	 */
	$('#btn_changeAdvancedSearchParms').click(function() {
		$('#form_advancedSearchParms').submit();
	});
	
	
	/**
	 * 检索人才：简单
	 */
	$('#btn_simple_search').click(function() {
		var searchKey = $('#searchKey').val();
		var searchVal = $('#searchVal').val();
		var parm = 'searchType=SIMPLE&searchKey=' + searchKey + '&searchVal=' + searchVal;
		
		showTalentList(parm);
	});
	
	
	/**
	 * 下载选中的人才的简历
	 */
	$('#btn_dlResumes').click(function(){
		dlResumes();
	});
}); //End: $(document).ready


/**
 * 显示人才列表
 * @param 
 */
function showTalentList(parm) {
	var url = '/admin/admin_usr/gettalentlist';
	if (arguments.length == 1) {
		url = '/admin/admin_usr/gettalentlist/?' + parm;
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
		columnWidth: 142,
		detail: {onShowDetail: showTalentDetail, height: 'auto'},
		columns: [
		          { display: USR_CODE, name: 'talent_code', render: UsrCodeFunction},
		          { display: USR_NAME, name: 'fullname', render: UsrNameFunction},
		          { display: SEX, name: 'tr_sex'},
		          { display: AGE, name: 'age'},
		          { display: TEL, name: 'tel'},
		          { display: UPDATE_DATE, name: 'update_date'}
		 ]
	});
	
	$("#btn_invite").click(function() {
		var manager = $("#talentList").ligerGetGridManager();
		var rows = manager.getSelecteds();
		inviteCase(rows);
		return false;
	});
	
	
	//change this to the following function
//	function UsrCodeFunction(rowData, rowIndex) {
//		return UsrCodeFormatFunction(rowData, 'code');
//	}
	function UsrCodeFunction(rowData, rowIndex) {
		return "<a href=\"javascript:void(0)\" onclick=\"window.open('/usr_resume/previewresume/resumeCode/"+rowData['code']+"')\">"+rowData['talent_code']+"</a>";
	}
	
	
	function UsrNameFunction(rowData, rowIndex) {
		return UsrNameFormatFunction(rowData, 'fullname', 'code');
	}
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
		url: '/usr_resume/gettalentdetail4admin',
		type: 'get',
		async: true,
		dataType: 'json', //xml, json, script or html
		data: {'resumeCode': row.code},
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
//				$('#tip_detail_talent').html(data['msg']);
//				setTimeout("$('#tip_detail_talent').html('')", 5000);
				//html为人才详细信息
				var resume = data['resume'];
				var rsmCode = resume['code'];
				
				$(grid).html($('#talentDetail').html());
				$(grid).find('span[id]').each(function() {
					$(this).html(resume[$(this).attr('id')]);
				});
				$(grid).find('#btn_invite').click(function() {
					inviteCase(null, rsmCode);
				});
				$(grid).find('#btn_perview').click(function() {
					window.open('/usr_resume/previewresume/resumeCode/' + rsmCode);
				});
				$(grid).find('#btn_viewApply').click(function() {
					viewApply(rsmCode);
				});
				$(grid).find('#btn_edit').click(function() {
					window.open('/usr_resume/modifyresume/resumeCode/' + rsmCode);
				});
				$(grid).find('#btn_delTalent').click(function() {
					stopTalent(row.__index, rsmCode);
				});
			} else {
				$('#tip_talent').html(data['msg']);
			}
		}
	});
} //End: function showTalentDetail


/**
 * 将人才停止服务，批量
 * @param Array(string) resumeCodes: 简历编号数组
 */
function stopTalents(rowidxs, resumeCodes) {
	var mgr = $("#talentList").ligerGetGridManager();
	
	$.ligerDialog.confirm(CONFIRM_DEL + '？', function(ret) {
		if (ret == true) {
			$.ajax({
				url: '/admin/admin_usr/stoptalent',
				type: 'get',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: {'resumeCodes': resumeCodes},
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						$('#tip_talent').html();
						rowidxs.sort(sortNumberDesc);
						$.each(rowidxs, function(k, rowidx){
							mgr.deleteRow(rowidx);
						});
					} else {
						$('#tip_talent').html(data['msg']);
					}
				}
			});
		}
    });
} //End: function stopTalents(rowidxs, resumeCodes)


/**
 * 将人才停止服务，一个
 */
function stopTalent(rowidx, resumeCode) {
	var rowidxs = new Array();
	var resumeCodes = new Array(resumeCode);
	rowidxs[0] = rowidx;
	stopTalents(rowidxs, resumeCodes);
}


/**
 * 下载简历
 */
function dlResumes() {
}


/**
 * 募集邀请
 * @param rows
 * @param code
 * @param name
 * @returns {Boolean}
 */
function inviteCase(rows, code, name) {
	if ((!rows || rows.length == 0) && !code) {
		alert(PLEASE_CHOISE_INVITE_EMP);
		return false;
	}
	rows = rows || [{code: code, name: name}];
	$.ligerDialog.confirm(SURE_CHOISE_INVITE_EMP, TITLE_INVITE, function(e) {
		if (!e) return false;
		var ids = "";
		$.each(rows, function(i, v) {
			ids += v.code;
			ids += ",";
		});
		ids = ids.substring(0, ids.length-1);
		$("#dynForm").attr("target", "_blank");
		$("#dynForm").attr("action", "/admin/admin_casecollect/collectcasesearch");
		$("#ids").val(ids);
		$("#dynForm").submit();
	});
}


/**
 * 应聘案件查看
 * @param code
 */
function viewApply(code) {
	$("#dynForm").attr("action", "/lp_caselist/collectcaselist");
	$("#dynForm").attr("target", "_blank");
	$("#dynForm input[name='code']").val(code);
	$("#dynForm").submit();
}