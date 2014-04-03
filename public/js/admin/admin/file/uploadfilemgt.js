$(document).ready(function(){
	/**
	 * 根据登陆用户角色，初始化显示简历文件列表
	 */
	$(function(){
		showTalentResumeFileListForAdmin();
	});
	
	
	/**
	 * 选择简历
	 */
	//在html input file里设置方式：class="multi" maxlength="1" 
	$('input[name="resumeFiles[]"]').MultiFile({
		max: 1
	});
	
	
	/**
	 * 上传简历
	 */
	$('#btn_uploadResume').click(function(){
		$('#form_uploadResume').ajaxSubmit({
			url: '/file_resume/handleuploadresume',
//			beforeSubmit: function(){
//				$.fn.MultiFile.disableEmpty(); 
//			},
			dataType: 'json',
			success: function(data, status, xhr, $form) {
//				$.fn.MultiFile.reEnableEmpty();
				$('input[name="resumeFiles[]"]').MultiFile('reset');
				if (data['err'] == 0) {
					$('#tip_uploadResume').html(data['msg']);
					var mgr = $("#resumeFileList").ligerGetGridManager();
					mgr.loadData();
					setTimeout("$('#tip_uploadResume').html('')", 5000);
				} else {
					$('#tip_uploadResume').html(data['msg']);
				}
			}
		});
		return false;
	});
	
	
	/**
	 * 删除选中的简历文件
	 */
	$('#btn_delResumeFile').click(function(){
		var mgr = $("#resumeFileList").ligerGetGridManager();
		var rows = mgr.getSelectedRows();
        if (!rows) {
        	$('#tip_resumeFileList').html(PLEASE_SELECT_ROW);
        	return; 
        }
        var resumeFileIds = new Array();
        var i = 0;
        $.each(rows, function(k, v) {
        	resumeFileIds[i++] = v.id;
        });
        $.ajax({
			url: '/file_resume/delresumefile',
			type: 'get',
			async: true,
			dataType: 'json', //xml, json, script or html
			data: {resumeFileIds: resumeFileIds},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) { //删除简历文件成功
					$('#tip_resumeFileList').html('');
					mgr.loadData();
				} else { //删除简历文件失败
					$('#tip_resumeFileList').html(data['msg']);
				}
			}
		});
	});
	
	
	/**
	 * 下载选中的简历文件
	 */
	$('#btn_dlResumeFile').click(function(){
//		window.open('/file_file/test');
		var mgr = $("#resumeFileList").ligerGetGridManager();
		var rows = mgr.getSelectedRows();
		if (!rows) {
			$('#tip_resumeFileList').html(PLEASE_SELECT_ROW);
			return; 
		}
		var resumeFileIds = new Array();
        var i = 0;
        $.each(rows, function(k, v) {
        	resumeFileIds[i++] = v.id;
        });
		$.ajax({
			url: '/file_resume/dlresumefile',
			type: 'get',
			async: true,
			dataType: 'json', //xml, json, script or html
			data: {resumeFileIds: resumeFileIds},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) { //下载简历文件成功
//					$('#tip_resumeFileList').html('');
//					mgr.loadData();
					var filepath = data['filepath'];
					var downname = data['downname'];
//					filepath = encodeURI(filepath);
					window.open('/file_file/dlfile/?filepath=' + filepath + '&downname=' + downname);
				} else { //下载简历文件失败
					$('#tip_resumeFileList').html(data['msg']);
				}
			}
		});
	});
	
	
	/**
	 * 处理选中的简历文件：录入人员将简历录入系统
	 */
	$('#btn_handleResumeFile').click(function(){
		var mgr = $("#resumeFileList").ligerGetGridManager();
		var rows = mgr.getSelectedRows();
		if (!rows) {
			$('#tip_resumeFileList').html(PLEASE_SELECT_ROW);
			return; 
		}
		var resumeFileIds = new Array();
		var i = 0;
		$.each(rows, function(k, v) {
			resumeFileIds[i++] = v.id;
		});
       $.ajax({
			url: '/file_resume/handleresumefile',
			type: 'get',
			async: true,
			dataType: 'json', //xml, json, script or html
			data: {resumeFileIds: resumeFileIds},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) { //处理简历文件成功
					$('#tip_resumeFileList').html('');
					mgr.loadData();
				} else { //处理简历文件失败
					$('#tip_resumeFileList').html(data['msg']);
				}
			}
		});
	});
	
	
	/**
	 * 只显示未处理
	 */
	$('#onlyDisplayUnhandled').click(function(){
		searchResumeFiles();
	});
	
	
	/**
	 * 搜索简历文件
	 */
	$('#btn_searchResumeFile').click(function(){
		searchResumeFiles();
	});
	
}); //End: $(document).ready


function searchResumeFiles() {
	var onlyDisplayUnhandled = $('input[name="onlyDisplayUnhandled"]:checked').val();
	var resumeName = $('#resumeName').val();
	showTalentResumeFileListForAdmin(onlyDisplayUnhandled, resumeName);
}


/**
 * 人才简历文件列表
 */
function showTalentResumeFileList() {
	$('#resumeFileList').ligerGrid({
		url: '/file_resume/getresumefile',
		checkbox: true,
		rownumbers: true,
		dataAction: 'server', 
		page: 1, 
		pageSize: 20,
		width: '100%',
//      height:'100%'
		columns: [
	            { display: RESUME_NAME, name: 'basename_c', minWidth: 150 },
	            { display: UPLOAD_DATE, name: 'create_time', minWidth: 100 },
	            { display: STATUS, name: 'status', minWidth: 100 }
	    ]
	});
}


/**
 * 人才简历文件列表for ADMIN 
 * @param string onlyDisplayUnhandled：Y或''
 */
function showTalentResumeFileListForAdmin(onlyDisplayUnhandled, resumeName) {
	if (arguments.length == 2) {
		var url = '/file_resume/getresumefile/onlyDisplayUnhandled/' + onlyDisplayUnhandled + '/resumeName/' + resumeName;
	} else {
		var url = '/file_resume/getresumefile';
	}
	$('#resumeFileList').ligerGrid({
		url: url,
		checkbox: true,
		rownumbers: true,
		dataAction: 'server', 
		page: 1, 
		pageSize: 20,
		width: '100%',
//      height:'100%',
		columns: [
            { display: TALENT_CODE, name: 'talent_code'},
            { display: LP_CODE, name: 'lp_code'},
//			{ display: ADMIN_CODE, name: 'admin_code'},
			{ display: RESUME_NAME, name: 'basename_c'},
			{ display: UPLOAD_DATE, name: 'create_time'},
			{ display: STATUS, name: 'status'}
    	]
});
}


/**
 * 普通用户简历文件列表
 */
function showMemberResumeFileList() {
	$('#resumeFileList').ligerGrid({
        url: '/file_resume/getresumefile',
        checkbox: true,
        rownumbers: true,
        width: '100%',
//      height:'100%'
    	dataAction: 'server', 
    	page: 1, 
    	pageSize: 20,
    	columns: [
    	          { display: RESUME_NAME, name: 'basename_c', minWidth: 150 },
    	          { display: UPLOAD_DATE, name: 'create_time', minWidth: 100 },
    	          { display: STATUS, name: 'status', minWidth: 100 }
    	]
    });
}


/**
 * 法人员工简历文件列表
 */
function showLpEmpResumeFileList() {
	$('#resumeFileList').ligerGrid({
		checkbox: true,
		rownumbers: true,
		dataAction: 'server', 
		page: 1, 
		pageSize: 20,
  		url: '/file_resume/getresumefile',
  		width: '100%',
//        height:'100%'
        columns: [
//			{ display: EMP_NAME, name: 'fullname'},
			{ display: RESUME_NAME, name: 'basename_c'},
			{ display: UPLOAD_DATE, name: 'create_time'},
			{ display: STATUS, name: 'status'}
		] 
	});
}





