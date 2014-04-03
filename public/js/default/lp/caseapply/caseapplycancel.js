$(function(){
	$('#btn_confirm').click(function(){
		if (valTable()) {
			$("#dynForm").attr("action", "/lp_caseapply/caseapplycancelconfirm");
			$("#dynForm #usrInfoList").val(tableToJson());
			$("#dynForm").submit();
		}
		return false;
	});
	
	function tableToJson() {
		var str = ["["];
		$("#tab_list").find("tbody tr").each(function() {
			str.push("{");
			$.each($(this).children(), function(i) {
				if (i === 0) {
					str.push("\"usrInfo\":\"");
					str.push($(this).text());
					str.push("\",");
				} else if (i === 1) {
					str.push("\"cancelBody\":\"");
					str.push($(this).text());
					str.push("\",");
				} else if (i === 2) {
					str.push("\"currentStatus\":\"")
					str.push($(this).text());
					str.push("\",");
				} else if (i === 3) {
					var s = $(this).find("select[name='lackReason'] option:selected");
					str.push("\"lackReasonValue\":\"");
					str.push(s.text());
					str.push("\",");
					str.push("\"lackReason\":\"");
					str.push(s.val());
					str.push("\",");
				} else if (i === 4) {
					str.push("\"applyId\":\"");
					str.push($(this).find("input[name='applyId']").val());
					str.push("\",");
					str.push("\"remark\":\"");
					str.push($(this).find("input[name='remark']").val());
					str.push("\"");
				}
			});
			str.push("}");
			str.push(",");
		});
		str.pop();
		str.push("]");
		return str.join("");
	}
	
	function valTable() {
		//TODO 需要怎么验证
		return true;
	}
	
});