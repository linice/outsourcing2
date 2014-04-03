$(document).ready(function(){
	/**
	 * 初始化：普通用户显示个人消息，法人显示系统消息
	 */
	$(function() {
		var parm;
		if (roleCode == 'LP') {
			parm = {'msgType': 'SYS'};
			$('#msgType').val('SYS');
		} else {
			parm = {'msgType': 'PERSONAL'};
			$('#msgType').val('PERSONAL');
		}
		var strParm = JSON.stringify(parm);
		showMsgList(strParm);
	});
	
	
	/**
	 * 显示个人消息
	 */
	$('#btn_personalMsg').click(function() {
		$('#msgBox').html(PERSONAL_MSG);
		$('#msgType').val('PERSONAL');
		var parm = {'msgType': 'PERSONAL'};
		var strParm = JSON.stringify(parm);
		showMsgList(strParm);
	});
	
	
	/**
	 * 显示系统消息
	 */
	$('#btn_sysMsg').click(function() {
		$('#msgBox').html(SYS_MSG);
		$('#msgType').val('SYS');
		var parm = {'msgType': 'SYS'};
		var strParm = JSON.stringify(parm);
		showMsgList(strParm);
	});
	
	
	/**
	 * 删除消息
	 */
	$('#btn_delMsg').click(function(){
		delMsgs();
	});
	
	
}); //End: $(document).ready


/**
 * 显示消息列表，parm用于以后可能扩展的查询
 */
function showMsgList(parm) {
	var url = null;
	if (arguments.length == 1) {
		url = '/msg_msg/getmsglist/parm/' + parm;
	} else {
		url = '/msg_msg/getmsglist';
	}
	$('#msgList').ligerGrid({
		url: url,
		checkbox: true,
		rownumbers: true,
		dataAction: 'server', 
		page: 1, 
		pageSize: 20,
		isScroll: false, 
		frozen: false,
		method: 'get',
		width: '100%', //height:'100%',
		headerRowHeight: 30,
		detail: {onShowDetail: showMsgDetail, height: 'auto'},
		columns: [
	          { display: SENDER, name: 'sender_name', 
	        	  render: function (rowdata, rowindex, value)
	        	  {
	        		  var html;
	        		  if (typeof rowdata.sender_name != 'undefined') {
	        			  html = rowdata.sender_name;
	        		  } else {
	        			  html = SYS;
	        		  }
	        		  if (rowdata.is_read == 'N') {
	        			  html = '<span style="font-weight:bold;">' + html + '</span>';
	        		  }
	        		  return html;
	        	  },
	        	  align: 'left'
	          },
	          { display: TITLE, name: 'title', 
	        	  render: function (rowdata, rowindex, value)
	        	  {
                    var html = rowdata.title;
                    if (rowdata.is_read == 'N') {
                    	html = '<span style="font-weight:bold;">' + html + '</span>';
                    }
                    return html;
	        	  },
	        	  minWidth: 560,
	        	  align: 'left'
	          },
	          { display: RECEIVE_TIME, name: 'recv_time',
	        	  render: function (rowdata, rowindex, value)
	        	  {
	        		  var html = rowdata.recv_time;
	        		  if (rowdata.is_read == 'N') {
	        			  html = '<span style="font-weight:bold;">' + html + '</span>';
	        		  }
	        		  return html;
	        	  },
	        	  align: 'left'
	          }
		]
	});
} //End: function showMsgList()


/**
 * 显示消息详情
 */
function showMsgDetail(row, detailPanel, callback) {
	var grid = document.createElement('div'); 
    $(detailPanel).append(grid);
    $(grid).html(row.content);
    //标识为已读
    if (row.is_read == 'N') {
    	//数据库
    	var msgType = $('#msgType').val();
    	$.ajax({
    		url: '/msg_msg/readmsg',
    		type: 'get',
    		async: true,
    		dataType: 'json', //xml, json, script or html
    		data: {'msgId': row.id, 'msgType': msgType},
    		success: function(data, textStatus, jqXHR) {
    		}
    	});
    }
}


/**
 * 批量删除消息
 */
function delMsgs() {
	var mgr = $("#msgList").ligerGetGridManager();
	var rows = mgr.getSelectedRows();
	var msgIds = new Array();
	var msgType = $('#msgType').val();
	var i = 0;
	
	$.each(rows, function(k, v) {
		msgIds[i++] = v.id;
	});
	
	$.ligerDialog.confirm(CONFIRM_DEL + '？', function(ret) {
		if (ret == true) {
			$.ajax({
				url: '/msg_msg/delmsg',
				type: 'get',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: {'msgIds': msgIds, 'msgType': msgType},
				success: function(data, textStatus, jqXHR) {
					if (data['err'] == 0) {
						$('#tip_msg').html('');
						mgr.loadData();
					} else {
						$('#tip_msg').html(data['msg']);
					}
				}
			});
		}
    });
} //End: function delMsgs()













