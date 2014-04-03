$(document).ready(function(){
	var today = new Date();
	var yearEnd = today.getFullYear() - 10;
	var yearBegin = yearEnd - 90;
	
	
	//出身年月 
	$('#birthday').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
//		minDate: '1970-01-01',
		yearRange: today.getFullYear() - 10 + ':' + today.getFullYear() - 100
	});
	
	
	//来日年度 & 毕业年度
	$('#date_arrive_jp, #date_graduate').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
//		minDate: '1970-01-01',
		yearRange: today.getFullYear() + ':' + today.getFullYear() - 100
	});
	
	
	/**
	 * 当输入出生年月的输入框失去焦点时，计算满岁
	 */
	$('#birthday').change(function() {
		var birthday = $('#birthday').val();
		var fullAge = calcFullAge(birthday);
		if (!isNaN(fullAge)) {
			$('#fullAge').html(fullAge);
		}
	});
	
	
	//初始化简历
	initResumeBaseInfo();
	initResumeBaseInfo2();
	
	
	//根据现住国家的变化，列出省或县
	$('#actual_residence_cntry').change(function(){
		var html = '';
		if ($(this).val() == 'CN') {
			$.each(provinces, function(k, v) {
				html += '<option value="' + k + '">' + v + '</option>';
			});
		} else if ($(this).val() == 'JP') {
			$.each(counties, function(k, v) {
				html += '<option value="' + k + '">' + v + '</option>';
			});
		}
		$('#actual_residence_province').html(html);
	});
	
	
	/**
	 * dlg：选择个人，则跳转到个人修改简历页面
	 */
	$('#btn_member').click(function(){
		//普通用户列表
		$('#memberList').ligerGrid({
	        url: '/usr_usr/getusrs/roleCode/MEMBER',
	        checkbox: false,
	        rownumbers: true,
	        dataAction: 'server', 
	        page: 1, 
	        pageSize: 20,
	        width: '100%', //height:'100%',
	        onDblClickRow: function(rowdata, rowindex, rowDomElement) {
	        	location.href = '/usr_resume/modifyresume/talentCode/' + rowdata.usr_code;
	        },
			columns: [
		          { display: MEMBER_CODE, name: 'usr_code', minWidth: 100 },
		          { display: EMAIL, name: 'email', minWidth: 250 },
		          { display: FULLNAME, name: 'fullname', minWidth: 150 },
		    ]
	    });
	
		//dlg: 普通用户列表
		$('#dlg_memberList').dialog({
			autoOpen: true,
			modal: true,
			width: 800,
			buttons: {
				'取消': function(){
					$(this).dialog('close');
				}
			}
		});
	}); //End: $('#btn_member').click
	
	
	/**
	 * 检索个人
	 */
	$('#btn_searchMember').click(function(){
		//输入查询条件
        var mgr = $("#memberList").ligerGetGridManager();
        mgr.setOptions({
            parms: [{ name: 'fullname', value: $("#s_fullname").val()}
	            //{name:"ComCity",value: $("#txtCity").val()},
	            //{name:"ComCounty",value: $("#txtCounty").val()}
            ]
        });
        //按查询条件导入到grid中
        mgr.loadData();
	});


	/**
	 * dlg：选择法人，同时设置usr_code
	 */
	$('#btn_lp').click(function(){
		//法人列表
		$('#lpList').ligerGrid({
	        checkbox: false,
	        rownumbers: true,
	        dataAction: 'server', 
	        page: 1, 
	        pageSize: 20,
	        url: '/usr_usr/getusrs/roleCode/LP',
	        width: '100%', //height:'100%',
	        onDblClickRow: function(rowdata, rowindex, rowDomElement) {
	        	$('#usr_code').val(rowdata.usr_code);
	        	$('#roleCode').val('LP');
				$('#username').val(rowdata.fullname);
				$('#roleName').html(LP);
				$('#tel').val(rowdata.tel);
				$('#email').val(rowdata.email);
				$('#dlg_lpList').dialog('close');
	        },
			columns: [
		          { display: LP_CODE, name: 'usr_code', minWidth: 100 },
		          { display: EMAIL, name: 'email', minWidth: 250 },
		          { display: LP_NAME, name: 'fullname', minWidth: 150 }
		    ] 
	    });
	

		//dlg: 法人列表
		$('#dlg_lpList').dialog({
			autoOpen: true,
			modal: true,
			width: 800,
			buttons: {
				'取消': function(){
					$(this).dialog('close');
				}
			}
		});
	}); //End: $('#btn_lp').click


	/**
	 * 检索法人
	 */
	$('#btn_searchLp').click(function(){
		//输入查询条件
        var mgr = $("#lpList").ligerGetGridManager();
        mgr.setOptions({
            parms: [{ name: 'fullname', value: $("#s_lpName").val()}
	            //{name:"ComCity",value: $("#txtCity").val()},
	            //{name:"ComCounty",value: $("#txtCounty").val()}
            ]
        });
        //按查询条件导入到grid中
        mgr.loadData();
	});
	
	
	/**
	 * dlg：选择管理员，同时设置usr_code
	 */
	$('#btn_admin').click(function(){
		//管理员列表
		$('#adminList').ligerGrid({
	        url: '/usr_usr/getusrs/roleCode/ADMIN',
	        checkbox: false,
	        rownumbers: true,
	        dataAction: 'server', 
	        page: 1, 
	        pageSize: 20,
	        width: '100%', //height:'100%',
	        onDblClickRow: function(rowdata, rowindex, rowDomElement) {
	        	$('#usr_code').val(rowdata.usr_code);
	        	$('#roleCode').val('ADMIN');
	        	$('#username').val(rowdata.fullname);
	        	$('#roleName').html(ADMIN);
	        	$('#dlg_adminList').dialog('close');
	        },
	        columns: [
	            { display: ADMIN_CODE, name: 'usr_code', minWidth: 100 },
				{ display: EMAIL, name: 'email', minWidth: 250 },
				{ display: ADMIN_NAME, name: 'fullname', minWidth: 150 },
	        ]
	    });
	
		//dlg: 管理员列表
		$('#dlg_adminList').dialog({
			autoOpen: true,
			modal: true,
			width: 800,
			buttons: {
				'取消': function(){
					$(this).dialog('close');
				}
			}
		});
	}); //End: $('#btn_admin').click
	
	
	/**
	 * 检索管理员
	 */
	$('#btn_searchAdmin').click(function(){
		//输入查询条件
        var mgr = $("#adminList").ligerGetGridManager();
        mgr.setOptions({
            parms: [{ name: 'fullname', value: $("#s_adminName").val()}
	            //{name:"ComCity",value: $("#txtCity").val()},
	            //{name:"ComCounty",value: $("#txtCounty").val()}
            ]
        });
        //按查询条件导入到grid中
        mgr.loadData();
	});
	
	
	/**
	 * 保存创建简历——基本信息
	 */
	$('#btn_saveResumeBaseInfo').click(function(){
//		var btn = $(this);
//		btn.html(BEING_SAVED);
		var enabled = $(this).attr('class');
		if (enabled == 'ENABLED_Y') {
			$(this).attr('class', 'ENABLED_N');
			var resumeCode = $('#resumeCode').val();
			$.ajax({
				url: '/usr_resume/saveresumebaseinfo',
				type: 'post',
				async: false,
				dataType: 'json', //xml, json, script or html
				data: $('#form_resumeBaseInfo').serialize() + '&resumeCode=' + resumeCode,
				success: function(data, textStatus, jqXHR) {
//					btn.html('<img src="/img/default/front/<?=$auth->locale?>/btn_keep.jpg" width="79" height="28" class="btn_marR" />');
					$('label').css('color', 'black');
					if (data['err'] == 0) { //用户简历基本信息通过验证，即已保存至数据库，提示 已保存
						$('#tip_resumeBaseInfo').html(data['msg']);
						setTimeout("$('#tip_resumeBaseInfo').html('')", 5000);
					} else if (data['err'] == 1) { //用户简历基本信息保存失败
						$('#tip_resumeBaseInfo').html(data['msg']);
					} else { //用户简历基本信息不合法，提示不合法的内容
						var html = '';
						var msgs = eval(data['msg']);
						$.each(msgs, function(k, v) {
							html += v + '<br />';
						});
						$('#tip_resumeBaseInfo').html(html);
						
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
	
	
}); //End: $(document).ready


/**
 * 如果用户在创建简历，或刷新页面时，初始化已保存的简历信息
 */
function initResumeBaseInfo() {
	//如果用户创建简历时，保存了简历信息，此后，再刷新页面
	if (resume) {
		var fullAge = calcFullAge(resume['birthday']);
		if (!isNaN(fullAge)) {
			$('#fullAge').html(fullAge);
		}
		//form的输入标签ID和Name修改为数据库字段名后，统一初始化输入标签内容
		$('#form_resumeBaseInfo').find(':text, select, textarea').each(function() {
			$(this).val(resume[$(this).attr('id')]);
		});
		//修正现居国家和省份
		initCntryAndProvince();
		$('#actual_residence_province').val(resume['actual_residence_province']);
		$('#form_resumeBaseInfo').find(':radio').each(function() {
			var radioName = $(this).attr('name');
			$(':radio[name="' + radioName + '"][value="' + resume[radioName] + '"]').attr('checked', true);
		});
		
		//如果是管理员代理用户维护简历，则显示该用户的用户名和角色（EMP，或MEMBER）
		if (usr['role_code'] == 'ADMIN') {
			if (resume['lp_code']) {
				ajaxReq(resume['lp_code']);
				$('#roleName').html(LP);
			} else if (resume['admin_code']) {
				ajaxReq(resume['admin_code']);
				$('#roleName').html(ADMIN);
			} else {
				$('#username').val();
				$('#roleName').html(INVIDIDUAL);
			}
		}
	} else { //根据用户注册信息，初始化简历信息
		if (usr['role_code'] == 'MEMBER') { //如果是普通用户
			$('#email').val(usr['email']);
			$('#sex').val(usr['sex']);
			$('#birthday').val(usr['birthday']);
			$('#fullname_p').val(usr['fullname_p']);
			$('#fullname').val(usr['fullname']);
			$('#tel').val(usr['tel']);
			$('#actual_residence_cntry').val(usr['country']);
			initCntryAndProvince();
			$('#actual_residence_province').val(usr['province']);
		} else { //如果是法人、管理员
			$('#tel').val(usr['tel']);
			$('#email').val(usr['email']);
			$('#actual_residence_cntry').val('JP');
			initCntryAndProvince();
			$('#actual_residence_province').val('Dongjing');
		}
	}
}


/**
 * 如果用户在创建简历，或刷新页面时，初始化已保存的简历信息。
 * 这个初始化，在initResumeBaseInfo()的基础之上。
 */
function initResumeBaseInfo2() {
	//初始化满岁
	var birthday = $('#birthday').val();
	
	if (birthday != '') {
		var fullAge = calcFullAge(birthday);
		if (!isNaN(fullAge)) {
			$('#fullAge').html(fullAge);
		}
	}
}


/**
 * 初始化国家及其省份
 */
function initCntryAndProvince() {
	var country = $('#actual_residence_cntry').val();
	
	//根据现住国家的变化，列出省或县
	var html = '';
	if (country == 'CN') {
		$.each(provinces, function(k, v) {
			html += '<option value="' + k + '">' + v + '</option>';
		});
	} else if (country == 'JP') {
		$.each(counties, function(k, v) {
			html += '<option value="' + k + '">' + v + '</option>';
		});
	}
	$('#actual_residence_province').html(html);
}


/**
 * 获取法人的公司名或管理员的名称
 */
function ajaxReq(usrCode) {
	$.ajax({
		url: '/usr_resume/getcom',
		type: 'get',
		async: true,
		dataType: 'json', //xml, json, script or html
		data: {'usrCode': usrCode},
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
				$('#username').val(data['username']);
			} else {
			}
		}
	});
}