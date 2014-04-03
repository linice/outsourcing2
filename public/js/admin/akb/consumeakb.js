$(document).ready(function() {
	/**
	 * 初始化消费查询：查询所有消费，按消费时间倒序排列
	 */
	searchConsumeAkb();
	
	
	/**
	 * 查询消费
	 */
	$('#btn_searchAkb').click(function() {
		var param = $('#form_consumeAkb').serializeArray();
		var sParam = JSON.stringify(param);
		searchConsumeAkb(sParam);
	});
	
	
}); //End: $(document).ready


/**
 * 查询消费
 * @param string: 格式：[{"k":"v", "k2":"v2"}, {"k20":"v20", "k21":"v21"}]
 * 转换方法：将js数组，通过函数JSON.stringify，转换为相应的字符串，格式一致，只是类型变成了String。
 */
function searchConsumeAkb(param) {
	var lpCode = $('#lpCode').val();
	var url = '/admin/akb/getconsumeakb/lpCode/' + lpCode;
	if (arguments.length == 1) {
		url = '/admin/akb/getconsumeakb/param/' + param;
	}
	$('#consumeAkbList').ligerGrid({
		url: url,
		checkbox: false,
		rownumbers: true,
		dataAction: 'server', 
		minColumnWidth: 128,
		pageSize: 20,
		isScroll: false, 
		frozen:false,
		method: 'post',
		width: '100%',
		//detail: {onShowDetail: showDetail, height: 'auto'},
		//onSuccess: funcName,
		columns: [{ display: USERNAME, name: 'fullname'},
		          { display: DATE, name: 'create_date'},
				  { display: CLASSIFICATION, name: 'in_out_tr'},
				  { display: POINTS, name: 'points_consume'},
				  { display: REST, name: 'points_rest'},
				  { display: PURPOSE, name: 'svc_tr'},
				  { display: REMARK, name: 'remark'},
				  { display: OPERATOR, name: 'operator_name'}
		 ]
	});
}