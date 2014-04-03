$(document).ready(function(){
	/**
	 * 显示列表
	 */
	showEmailTplList();
	
	/**
	 * 删除邮件模板
	 */
	$('#btn_delEmailTpl').click(function() {
		delEmailTpl();
	});
}); //End: $(document).ready


/**
 * 显示列表
 */
function showEmailTplList() {
	$('#emailTplList').ligerGrid({
		url: '/admin/admin_email/getemailtpllist',
		checkbox: true,
		checkboxColWidth: 30,
		rownumbers: true,
		dataAction: 'server', 
		page: 1, 
		pageSize: 20,
		isScroll: false, 
		frozen: false,
		method: 'get',
		width: '100%', //height:'100%',
		headerRowHeight: 30,
		columnWidth: 250,
		detail: {onShowDetail: showEmailTplDetail, height: 'auto'},
		columns: [
	          {display: TPL_NAME, name: 'name'},
	          {display: TPL_TITLE, name: 'title'},
	          {display: OPERATION, align: 'center',
	        	  render: function (rowdata, rowindex, value) {
	        		  var html = '<a href="/admin/admin_email/emailset/etId/' + rowdata.id + '" target="_blank">' + MODIFY + '</a>';
	        		  return html;
	        	  }
	          }
		]
	});
} //End: function showEmailTplList()



/**
 * 显示邮件模板详情
 */
function showEmailTplDetail(row, detailPanel, callback) {
	var grid = document.createElement('div'); 
    $(detailPanel).append(grid);
    $(grid).html(row.content);
}


/**
 * 批量删除邮件模板
 */
function delEmailTpl() {
	var mgr = $("#emailTplList").ligerGetGridManager();
	var rows = mgr.getSelectedRows();
	var etIds = new Array();
	var i = 0;
	
	$.each(rows, function(k, v) {
		etIds[i++] = v.id;
	});
	
	$.ligerDialog.confirm(CONFIRM_DEL + '？', function(ret) {
		if (ret == true) {
			$.ajax({
				url: '/admin/admin_email/delemailtpl',
				type: 'get',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: {'etIds': etIds},
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						$('#tip_emailTplList').html('');
						mgr.loadData();
					} else {
						$('#tip_emailTplList').html(data['msg']);
					}
				}
			});
		}
    });
} //End: function delEmailTpl()