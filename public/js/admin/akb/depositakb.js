$(document).ready(function() {
	var today = new Date();
	var yearEnd = today.getFullYear() + 10;
	var yearBegin = yearEnd - 100;

	
	/**
	 * 初始化入金查询：查询所有入金，按入金时间倒序排列
	 */
	searchDepositAkb();
	
	
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
	 * 查询入金
	 */
	$('#btn_searchAkb').click(function() {
		var param = $('#form_depositAkb').serializeArray();
		var sParam = JSON.stringify(param);
		searchDepositAkb(sParam);
	});
	
	
	/**
	 * 创建入金
	 */
	$('#btn_createDepositAkb').click(function() {
		depositAkb();
	});
	
	
	/**
	 * dlg：选择个人，同时设置usr_code
	 */
	$('#btn_mbr').click(function(){
		//普通用户列表
		$('#mbrList').ligerGrid({
	        url: '/usr_usr/getusrs/roleCode/MEMBER',
	        checkbox: false,
	        rownumbers: true,
	        dataAction: 'server', 
	        page: 1, 
	        pageSize: 20,
	        width: '100%', //height:'100%',
	        onDblClickRow: function(rowdata, rowindex, rowDomElement) {
				$('#c_usrCode').val(rowdata.usr_code);
				$('#c_fullname').val(rowdata.fullname);
				$('#c_roleName').html(INVIDIDUAL);
				$('#dlg_mbrList').dialog('close');
	        },
			columns: [
		          { display: MEMBER_CODE, name: 'usr_code', minWidth: 100 },
		          { display: EMAIL, name: 'email', minWidth: 250 },
		          { display: FULLNAME, name: 'fullname', minWidth: 150 },
		    ]
	    });
	
		//dlg: 普通用户列表
		$('#dlg_mbrList').dialog({
			autoOpen: true,
			modal: true,
			width: 800,
			buttons: {
				'取消': function(){
					$(this).dialog('close');
				}
			}
		});
	}); //End: $('#btn_mbr').click
	
	
	/**
	 * 检索个人
	 */
	$('#btn_searchMbr').click(function(){
		//输入查询条件
        var mgr = $("#mbrList").ligerGetGridManager();
        mgr.setOptions({
            parms: [
                    { name: 'fullname', value: $("#s_fullname").val()}
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
	        	$('#c_usrCode').val(rowdata.usr_code);
				$('#c_fullname').val(rowdata.fullname);
				$('#c_roleName').html(LP);
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
            parms: [
                    { name: 'fullname', value: $("#s_lpName").val()}
            ]
        });
        //按查询条件导入到grid中
        mgr.loadData();
	});
	
}); //End: $(document).ready



/**
 * 查询入金
 * @param string: 格式：{"k":"v", "k2":"v2"}
 * 转换方法：将js数组，通过函数JSON.stringify，转换为相应的字符串，格式一致，只是类型变成了String。
 */
function searchDepositAkb(param) {
	var url = '/admin/akb/getdepositakb';
	if (arguments.length == 1) {
		url = '/admin/akb/getdepositakb/param/' + param;
	}
	
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
		columns: [{ display: USR_ID, name: 'usr_code'},
		          { display: USERNAME, name: 'fullname'},
		          { display: DATE, name: 'create_date'},
				  { display: DEPOSIT_AMT, name: 'amt'},
				  { display: PURPOSE, name: 'svc_tr'},
				  { display: SERVICE_TAKE_EFFECT_DATE, name: 'date_begin'},
				  { display: SERVICE_DEADLINE, name: 'date_end'},
				  { display: BUY_POINTS, name: 'points'}
		 ]
	});
}


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
					async: true,
					dataType: 'json', //xml, json, script or html
					data: $('#form_createDepositAkb').serializeArray(),
					success: function(data, textStatus, jqXHR) {
						if (data['err'] == 0) { //创建入金成功
//							$('#tip_createDepositAkb').html(data['msg']);
//							setTimeout("$('#tip_createDepositAkb').html('')", 5000);
							$('#dlg_createDepositAkb').dialog('close');
							var mgr = $("#depositAkbList").ligerGetGridManager();
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





