var columns = [{display: COL_A_BASEINFO, name: "baseinfolong", align: "left", width: "70%"},
               {display: COL_A_STATUS, name: "statusValue", width: "10%"},
               {display: COL_A_REASON, name: "lackValue", width: "15%"}];
usrList = usrList || [];
var noVoteList = [];
var voteList = [];
var okList = [];
var loseList = [];
var llist = ["usrList", "noVoteList", "voteList", "okList", "loseList"];
$(function() {
	function setCount() {
		$("#tab2").find("li").find("span[name='count']").each(function(i, v) {
			$(this).text(eval(llist[i]+".length"));
		});
	}
	function init() {
		$.each(usrList, function(i, v) {
			if (!v["apply_status"] || v["apply_status"]=='NO_VOTE' || v["apply_status"]=="RECOMMEND" || v["apply_status"]=="NO_RECOMMEND") 
				noVoteList.push(v);
			else if (v["apply_status"] == 'INTERVIEW_OK')
				okList.push(v);
			else if (v["apply_status"] == 'INTERVIEW_LOSE' || v["apply_status"] == 'APPLY_CANCEL')
				loseList.push(v);
			else 
				voteList.push(v);
		});
		
		setCount();
		
		$("div.tablist").each(function(i) {
			var tab = $(this);
			var table = tab.find("div[name='"+i+"']");
			$(this).find("#btn_cancel").click(function() {//应聘取消
				var manager = table.ligerGetGridManager();
				var rows = manager.getSelecteds();
				if (!rows || rows.length == 0) {
					alert("请选择需要取消应聘的人员!");
					return false;
				}
				if (valCancelList(rows)) {
					var ids = joinIds(rows);
					$("#dynForm").attr("action", "/admin/admin_caseapply/caseapplycancel");
					$("#dynForm #usrList").val(ids);
					$("#dynForm").submit();
					return false;
				} 
			})
			$(this).find("#btn_interview").click(function() {//面试调整
				var manager = table.ligerGetGridManager();
				var rows = manager.getSelecteds();
				if (!rows || rows.length == 0) {
					alert("请选择需要面试调整的人员!");
					return false;
				}
				if (valInterviewList(rows)) {
					var ids = joinIds(rows);
					$("#dynForm").attr("action", "/admin/admin_interview/interviewchange");
					$("#dynForm #usrList").val(ids);
					$("#dynForm").submit();
					return false;
				} else {
					alert("只能选择未处理人员进行面试调整!")
					return false;
				}
			})
			$(this).find("#btn_interviewinform").click(function() {//面试结果通知
				var manager = table.ligerGetGridManager();
				var rows = manager.getSelecteds();
				if (!rows || rows.length == 0) {
					alert("请选择需要通知面试结果的人员!");
					return false;
				}
				if (valInterviewNotifyList(rows)) {
					var ids = joinIds(rows);
					$("#dynForm").attr("action", "/admin/admin_interview/interviewresultinform");
					$("#dynForm #usrList").val(ids);
					$("#dynForm").submit();
					return false;
				} else {
					alert("只能选择已面试人员进行结果通知!")
					return false;
				}
			})
			$(this).find("#btn_edit").click(function() {//面试情报编辑
				var manager = table.ligerGetGridManager();
				var rows = manager.getSelecteds();
				if (!rows || rows.length == 0) {
					alert("请选择需要进行面试情报编辑的人员!");
					return false;
				}
				if (valAdjustList(rows)) {
					var ids = joinIds(rows);
					$("#dynForm").attr("action", "/admin/admin_interview/interviewinfoedit");
					$("#dynForm #usrList").val(ids);
					$("#dynForm").submit();
					return false;
				} else {
					alert("只能选择状态面试调整中的人员进行面试情报编辑!")
					return false;
				}
			})
			$(this).find("#btn_adjust").click(function() {//管理员判定
				var manager = table.ligerGetGridManager();
				var rows = manager.getSelecteds();
				if (!rows || rows.length == 0) {
					alert("请选择需要进行管理员判定的人员!");
					return false;
				}
				if (valAdminAdjustList(rows)) {
					var ids = joinIds(rows);
					$("#dynForm").attr("action", "/admin/admin_judge/judge");
					$("#dynForm #usrList").val(ids);
					$("#dynForm").submit();
					return false;
				} else {
					alert("只能选择未判定的人员进行判定!")
					return false;
				}
			})
		});
		
		if (pTab === '1' || pTab == '2' || pTab == '3' || pTab == '4') {
			setTab(2, pTab);
		}
	}
	init();
	
	function valAdminAdjustList(rows) {
		var flag = true;
		$.each(rows, function(i, v) {
			if (v["apply_status"] != "NO_VOTE") {
				flag = false;
				return false;
			}
		})
		return flag;
	}
	
	function valAdjustList(rows) {
		var flag = true;
		$.each(rows, function(i, v) {
			if (v["apply_status"] != "INTERVIEW_ADJUST") {
				flag = false;
				return false;
			}
		})
		return flag;
	}
	
	function valInterviewList(rows) {
		var flag = true;
		$.each(rows, function(i, v) {
			if (!(!v["apply_status"] || v["apply_status"]=='NO_VOTE' || v["apply_status"]=="RECOMMEND" || v["apply_status"]=="NO_RECOMMEND")) {
				flag = false;
				return false;
			}
		})
		return flag;
	}
	
	function valInterviewNotifyList(rows) {
		var flag = true;
		$.each(rows, function(i, v) {
			if (v["apply_status"]!='INTERVIEW_ADJUST') {
				flag = false;
				return false;
			}
		})
		return flag;
	}
	
	function valCancelList(rows) {
		var flag = true;
		$.each(rows, function(i, v) {
			if (v["apply_status"] == 'INTERVIEW_LOSE' || v["apply_status"] == 'APPLY_CANCEL') {
				alert(cancel_talent_has_result_error);
				flag = false;
				return false;
			} else if (v["apply_status"] == 'INTERVIEW_ADJUST' || v["apply_status"] == 'INTERVIEW_INFORM') {
				alert(cancel_talent_is_proccess_error);
				flag = false;
				return false;
			}
		})
		return flag;
	}
	
	function joinIds(rows) {
		var ids = "";
		$.each(rows, function(i, v) {
			if (!!ids) 
				ids += ",";
			ids += v.apply_id;
		})
		return ids;
	}
	
	$("#userList_all").ligerGrid({
		checkbox: true,
		columns: columns,
		pageSize: usrList.length,
		usePager: false,
		columnWidth: 280,
		data: {"Total": usrList.length, "Rows": usrList}
	});
});

function setTab(m,n){
	$("#tab"+m).find("li").each(function(i) {
		if (i == n)
			$(this).addClass("now");
		else
			$(this).removeClass("now");
	});
	$("#tablist"+m).find("div.tablist").each(function(i) {
		if (i == n) {
			$(this).show();
			if (!$(this).find("div[name]").html()) {
				var d = $(this).find("div[name]");
				var nn = d.attr("name");
				if (nn === "1") {
					var list = noVoteList;
				} else if (nn === "2") {
					var list = voteList;
				} else if (nn === "3") {
					var list = okList;
				} else if (nn === "4") {
					var list = loseList;
				}
				d.ligerGrid({
					checkbox: true,
					columns: columns,
					pageSize: list.length,
					usePager: false,
					columnWidth: 280,
					data: {"Total": list.length, "Rows": list}
				});
			}
		} else 
			$(this).hide();
	});
}