$(document).ready(function() {
	/**
	 * 初始化入金查询：查询所有入金，按入金时间倒序排列
	 */
	searchDepositAkb();
	
	
	/**
	 * 查询入金
	 */
	$('#btn_searchAkb').click(function() {
		var param = $('#form_depositAkb').serializeArray();
		var sParam = JSON.stringify(param);
		searchDepositAkb(sParam);
	});
	
	
	
	
	
	
	
}); //End: $(document).ready



/**
 * 查询入金
 * @param string: 格式：{"k":"v", "k2":"v2"}
 * 转换方法：将js数组，通过函数JSON.stringify，转换为相应的字符串，格式一致，只是类型变成了String。
 */
function searchDepositAkb(param) {
	var url = '/akb/getdepositakb';
	if (arguments.length == 1) {
		url = '/akb/getdepositakb/param/' + param;
	}
	
	$('#depositAkbList').ligerGrid({
		url: url,
		checkbox: false,
		rownumbers: true,
		dataAction: 'server', 
		columnWidth: 144,
		pageSize: 20,
		isScroll: false, 
		frozen:false,
		method: 'post',
		width: '100%',
		//detail: {onShowDetail: showDetail, height: 'auto'},
		//onSuccess: funcName,
		columns: [{ display: DATE, name: 'create_date'},
				  { display: DEPOSIT_AMT, name: 'amt'},
				  { display: PURPOSE, name: 'svc_tr'},
				  { display: SERVICE_TAKE_EFFECT_DATE, name: 'date_begin'},
				  { display: SERVICE_DEADLINE, name: 'date_end'},
				  { display: BUY_POINTS, name: 'points'}
		 ]
	});
}
