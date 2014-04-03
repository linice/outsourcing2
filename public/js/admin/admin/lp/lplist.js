var manager;
$(document).ready(function() {
	$("#lplist").ligerGrid({
		checkbox: true,
		checkboxColWidth: 30,
		url: '/admin/admin_lp/searchlplist',
		//height: "auto", 
		width: "100%",
		columnWidth: 150,
		isScroll: false, frozen:false,
		//rowHeight: 
		//headerRowHeight: 
		pageSize: 20,
		columns: [{display: LPID, name: 'code', width:75},
		        {display: COL_A_COMPANYNAME, name: 'fullname'},
		        {display: COL_A_CREATE_TIME, name: 'create_time', width:85},
		        {display: COL_A_LAST_LOGIN_TIME, name: 'last_login_time', width:85},
		        {display: COL_A_HIGHLIGHT_DATE, name: 'highlightdate', width:85},
		        {display: COL_A_APPLY_VIEW_DATE, name: 'applydate', width:85},
		        {display: COL_A_BALANCE, name: 'balance', width: 70},
		        {display: COL_A_OPERATION, render: opFunction, align: 'center', width:145},
		        {display: COL_A_CASE_NUM, name: 'caseCnt', width:70},
		        {display: COL_A_EMP_NUM, name: 'empCnt', width:70}
		]
	});
	manager = $("#lplist").ligerGetGridManager();
	
	function opFunction(rowData, rowIndex) {
		var html = '<a href="javascript:void(0)" onclick="viewLp(\''+rowData.code+'\')">'+COL_A_VIEW+'</a> | ';
		var disChk = rowData.enabled === 'Y' || rowData.enabled === 'N' ? 'style="color:grey"' : 'onclick="checkLp(\''+rowData.code+'\')"';
		html += '<a href="javascript:void(0)" '+disChk+'>'+COL_A_ADMIT+'</a> | ';
		var disStop = rowData.enabled === 'Y' ? 'onclick="stopLp(\''+rowData.code+'\')"' : 'style="color:grey"';
		html += '<a href="javascript:void(0)" '+disStop+'>'+COL_A_STOP+'</a>';
		return html;
	}
	
	$("#searchBtn").click(function() {
		var sValue = $("#sValue").val();
		var range = $("#range").val();
		manager.setOptions({parms: {"sValue": sValue, "range": range}});
		manager.loadData();
	});
}); //End: $(document).ready


/**
 * 查看法人
 */
function viewLp(lpCode) {
	window.open('/admin/admin_lp/baseinfo/lpCode/' + lpCode);
}


/**
 * 承认法人
 */
function checkLp(code) {
	$.ajax({
		url: '/admin/admin_lp/checklp',
		type: 'post',
		async: false,
		dataType: 'json', //xml, json, script or html
		data: {code: code},
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


/**
 * 停止法人
 */
function stopLp(code) {
	$.ajax({
		url: '/admin/admin_lp/stoplp',
		type: 'post',
		async: false,
		dataType: 'json', //xml, json, script or html
		data: {code: code},
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
