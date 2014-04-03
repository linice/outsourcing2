/**
 * 包含于newusrlist.phtml，新增普通用户列表页面
 */
$(document).ready(function(){
	//显示新增的普通用户列表
	showNewMemberList();
	
	
	/**
	 * 检索员工
	 */
	$('#btn_search').click(function(){
		var searchKey = $('#searchKey').val();
		var searchVal = $('#searchVal').val();
		
		showNewMemberList(searchKey, searchVal);
	});
	
	
}); //End: $(document).ready


/**
 * 显示新增普通用户列表
 */
function showNewMemberList(searchKey, searchVal) {
	var url = null;
	if (arguments.length == 2) {
		url = '/admin/admin_usrlist/getnewmemberlist/searchKey/' + searchKey + '/searchVal/' + searchVal;
	} else {
		url = '/admin/admin_usrlist/getnewmemberlist';
	}
	$('#newMemberList').ligerGrid({
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
		detail: {onShowDetail: showMemberDetail, height: 'auto'},
		columns: [
	          { display: USR_CODE, name: 'talent_code'},
	          { display: NAME, name: 'fullname'},
	          { display: SEX, name: 'sex'},
	          { display: AGE, name: 'age'},
	          { display: TEL, name: 'tel'},
	          { display: UPDATE_DATE, name: 'update_date'}
	    ]
	});
} //End: function showNewMemberList()


/**
 * 显示员工详情
 * @returns
 */
function showMemberDetail(row, detailPanel, callback) {
	$(detailPanel).append($('#talentDetail').html());
	//查询员工详情
	var resumeCode = row.code;
	$.ajax({
		url: '/usr_resume/gettalentdetail',
		type: 'get',
		async: false,
		dataType: 'json', //xml, json, script or html
		data: {'resumeCode': resumeCode},
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
//				$('#tip_empDetail').html(data['msg']);
//				setTimeout("$('#tip_empDetail').html('')", 5000);
				//html为员工详细信息
				var resume = eval(data['resume']);
				$(detailPanel).find('span[id]').each(function() {
					$(this).html(resume[$(this).attr('id')]);
				});
				$(detailPanel).find('#btn_preview').click(function() {
					window.open('/usr_resume/previewresume/resumeCode/' + resume['code']);
				});
				$(detailPanel).find('#btn_edit').click(function() {
					window.open('/admin/admin_usr/usr/resumeCode/' + resume['code']);
				});
				$(detailPanel).find('#btn_del').click(function() {
					delTalent(row.__index, resumeCode);
				});
			} else {
				$('#tip_newMember').html(data['msg']);
			}
		}
	});
} //End: function showMemberDetail


/**
 * 删除人才（停止服务）
 * @param string resumeCode: 简历编号
 */
function delTalent(rowindex, resumeCode) {
	var mgr = $("#newMemberList").ligerGetGridManager();
	
	$.ligerDialog.confirm(CONFIRM_DEL + '？', function(ret) {
		if (ret == true) {
			$.ajax({
				url: '/admin/admin_usrlist/deltalent',
				type: 'get',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: {'resumeCode': resumeCode},
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						$('#tip_newMember').html();
						mgr.deleteRow(rowindex);
					} else {
						$('#tip_newMember').html(data['msg']);
					}
				}
			});
		}
    });
} //End: function delTalent(rowindex, resumeCode)




