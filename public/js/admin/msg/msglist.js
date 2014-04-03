$(document).ready(function(){
	$( "#tabs").tabs();
	/**
	 * 初始化消息列表：收到的消息
	 */
	$(function(){
		var parm = {'msgType': 'PERSONAL'};
		var sParm = JSON.stringify(parm);
		showMsgList(sParm);
	});
	
	
	/**
	 * 初始化消息列表：已发送消息
	 */
	$(function(){
		var parm = {'isSent': 'Y'};
		var strParm = JSON.stringify(parm);
		showMsgSendList(strParm);
	});
	
	
	/**
	 * 显示已发送消息
	 */
	$('#btn_sent').click(function() {
		$('#msgBox').html(HAVE_SENT);
		var parm = {'isSent': 'Y'};
		var strParm = JSON.stringify(parm);
		showMsgSendList(strParm);
	});
	
	
	/**
	 * 显示草稿消息
	 */
	$('#btn_draft').click(function() {
		$('#msgBox').html(DRAFT_BOX);
		var parm = {'isSent': 'N'};
		var strParm = JSON.stringify(parm);
		showMsgSendList(strParm);
	});
	
	
	/**
	 * 删除消息（收到）
	 */
	$('#btn_delMsg').click(function(){
		delMsg();
	});
	
	
	/**
	 * 删除发送的消息
	 */
	$('#btn_delMsgSend').click(function() {
		delMsgSend();
	});
	
	
}); //End: $(document).ready


/**
 * 显示消息(收到)列表，parm用于以后可能扩展的查询
 * @param string parm
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
	        		  var html = rowdata.sender_name;
	        		  if (rowdata.is_read == 'N') {
	        			  html = '<span style="font-weight:bold;">' + html + '</span>';
	        		  }
	        		  return html;
	        	  }
	          },
	          { display: TITLE, name: 'title', 
	        	  render: function (rowdata, rowindex, value)
	        	  {
                    var html = rowdata.title;
                    if (rowdata.is_read == 'N') {
                    	html = '<span style="font-weight:bold;">' + html + '</span>';
                    }
                    return html;
	        	  }
	          },
	          { display: RECEIVE_TIME, name: 'recv_time',
	        	  render: function (rowdata, rowindex, value)
	        	  {
	        		  var html = rowdata.recv_time;
	        		  if (rowdata.is_read == 'N') {
	        			  html = '<span style="font-weight:bold;">' + html + '</span>';
	        		  }
	        		  return html;
	        	  }
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
    	$.ajax({
    		url: '/msg_msg/readmsg',
    		type: 'get',
    		async: true,
    		dataType: 'json', //xml, json, script or html
    		data: {'msgId': row.id, 'msgType': 'PERSONAL'},
    		success: function(data, textStatus, jqXHR) {
    		}
    	});
    }
}


/**
 * 批量删除消息
 */
function delMsg() {
	var mgr = $("#msgList").ligerGetGridManager();
	var rows = mgr.getSelectedRows();
	var msgIds = new Array();
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
				data: {'msgIds': msgIds, 'msgType': 'PERSONAL'},
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
} //End: function delMsg()


/**
 * 显示发送的消息列表
 * @param string parm: 用于传递消息的状态，已发送或草稿
 */
function showMsgSendList(parm) {
	var url = '/admin/msg/getmsgsendlist/parm/' + parm;
	var obj = JSON.parse(parm);
	var TIME = SEND_TIME;
	if (obj.isSent = 'N') {
		TIME = SAVE_TIME;
	}
	
	$('#msgSendList').ligerGrid({
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
		detail: {onShowDetail: showMsgSendDetail, height: 'auto'},
		columns: [
	          { display: TITLE, name: 'title',
	        	  render: function (rowdata, rowindex, value)
	        	  {
	        		  var html = rowdata.title;
	        		  if (rowdata.is_sent == 'N') {
	        			  html = '<a href="/admin/msg/sendmsg/msId/' + rowdata.id + '" target="_blank">' + html + '</a>';
	        		  }
	        		  return html;
	        	  },
	        	  minWidth: 150
	          },
	          { display: RECVER, name: 'recver',
	        	  render: function (rowdata, rowindex, value)
	        	  {
	        		  var html = '';
	        		  if (rowdata.recver != '') {
	        			  var recvers = JSON.parse(rowdata.recver);
	        			  $.each(recvers, function(k, v) {
	        				  html += tr[v] + '; ';
	        			  });
	        		  }
	        		  return html;
	        	  },
	        	  minWidth: 150
	          },
	          { display: TIME, name: 'send_time', minWidth: 150}
		]
	});
} //End: function showMsgSendList()


/**
 * 显示发送的消息详情
 */
function showMsgSendDetail(row, detailPanel, callback) {
	var grid = document.createElement('div'); 
    $(detailPanel).append(grid);
    $(grid).html(row.content);
}


/**
 * 删除发送的消息
 */
function delMsgSend() {
	var mgr = $("#msgSendList").ligerGetGridManager();
	var rows = mgr.getSelectedRows();
	var msgIds = new Array();
	var i = 0;
	
	$.each(rows, function(k, v) {
		msgIds[i++] = v.id;
	});
	
	$.ligerDialog.confirm(CONFIRM_DEL + '？', function(ret) {
		if (ret == true) {
			$.ajax({
				url: '/admin/msg/delmsgsend',
				type: 'get',
				async: true,
				dataType: 'json', //xml, json, script or html
				data: {'msgIds': msgIds},
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
}