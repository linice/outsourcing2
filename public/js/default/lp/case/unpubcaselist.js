var manager;
$(function(){
	var url = "/lp_caseunpublish/searchunpublishcaselist";
	
	$("#caselist").ligerGrid({
		checkbox: true,
		checkboxColWidth: 30,
        columns: [{display: CASE_ID, name: 'code', width:62, render: CaseCodeFunction},
                  {display: CASE_NAME, name: 'name', render: CaseNameFunction},
                  {display: case_business_req, name: 'businessReqValue'},
                  {display: case_technical_req, name: 'technicalReqValue'},
                  {display: case_end_range, name: 'period', width:110},
                  {display: WORKPLACE, name: 'workingPlaceValue', width:100},
                  {display: PRICE_UNIT_MONTHLY, name: 'unitPriceValue', width:95},
                  {display: case_timeliness, name: 'timeliness', width:73},
                  {display: RELATION_OPERATE, render: opFunction, align: "center", width:88}],
        url: url,
        width: "100%",
        columnWidth: 131,
        isScroll: false, frozen:false,
        //detail: {onShowDetail: showDetail, height: "auto"},
        pageSize: 20
    });

	function CaseNameFunction(rowData, rowIndex) {
		return CaseNameFormatFunction(rowData, 'name', 'code');
	}
	function CaseCodeFunction(rowData, rowIndex) {
		return CaseCodeFormatFunction(rowData, 'code');
	}
	function opFunction(rowData, rowIndex) {
		return '<a onclick="editCase(\''+rowData.code+'\')" href="javascript:void(0)">'+COL_A_CASE_EDIT+'</a>|<a onclick="delCase('+null+',\''+rowData.code+'\',\''+rowData.name+'\',\''+rowData.id+'\')" href="javascript:void(0)">'+COL_A_CASE_DELETE+'</a>';
	}
	
	manager = $("#caselist").ligerGetGridManager();
	
	$("#delBtn").click(function(){
		var rows = manager.getSelecteds();
		delCase(rows);
	});
});

function editCase(code) {
	$("#dynForm").attr("action", "/lp_case/editcase");
	$("#dynForm").find("#caseCode").val(code);
	$("#dynForm").find("#btnlist").val("delete,edit");
	$("#dynForm").submit();
}

function delCase(rows, caseCode, caseName, caseId) {
	if ((!rows || rows.length == 0) && !caseCode && !caseName && !caseId) {
		alert(case_delete_selected_null);
		return false;
	}
	rows = rows || [{code: caseCode, name: caseName, id: caseId}];
	var html = ['<div style="width:500px;"><div class="notice_con">'+SURE_DELETE_CHOOSE_CASE+'</div>'];
	html.push('<table width="100%" border="0" cellpadding="0" cellspacing="0" class="tab_del">');
	html.push('<tr>');
	html.push('<th width="25%">'+CASE_ID+'</th>');
	html.push('<th width="75%" align="left"> '+CASE_NAME+' </th>');
	html.push('</tr>');
	$.each(rows, function(i, v) {
		html.push('<tr>');
		html.push('<th width="25%">'+v.code+'</th>');
		html.push('<th width="75%" align="left"> '+v.name+' </th>');
		html.push('</tr>');
	});
	html.push('</table>');
	html.push('</div>');
	html = html.join("");
	ligerConfirm(TITLE_LP_DELETE_CASE, html, {width: 550}, function() {
		var ids = "";
		$.each(rows, function(i, v) {
			ids += v.id;
			ids += ",";
		});
		ids = ids.substring(0, ids.length-1);
		$.ajax({
			url: '/lp_case/casedelete',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: {ids: ids},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					alert(data['msg']);
					var manager = $("#caselist").ligerGetGridManager();
					manager.loadServerData({"casename": $("#caseName").val()});
				} else {
					alert(data['msg']);
				}
			}
		});
	});
}
