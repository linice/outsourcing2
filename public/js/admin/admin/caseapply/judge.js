$(function(){
	$('#btn_confirm').click(function(){
		if (valTable()) {
			$("#dynForm").attr("action", "/admin/admin_judge/judgeconfirm");
			$("#dynForm #usrInfoList").val(tableToJson());
			$("#dynForm").submit();
		}
		return false;
	});
	
	$("select[name='judgeResult']").change(function() {
		var value = $(this).val();
		if (value == 'RECOMMEND') {
			$(this).parent().parent().next().find("select").hide();
		} else {
			$(this).parent().parent().next().find("select").show();
		}
	})
	
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
					var s = $(this).find("select[name='judgeResult'] option:selected");
					str.push("\"judgeResultValue\":\"");
					str.push(s.text());
					str.push("\",");
					str.push("\"judgeResult\":\"");
					str.push(s.val());
					str.push("\",");
				} else if (i === 2) {
					var s = $(this).find("select[name='lackReason']:visible option:selected");
					str.push("\"lackReasonValue\":\"");
					str.push(s.length>0 ? s.text() : "");
					str.push("\",");
					str.push("\"lackReason\":\"");
					str.push(s.length>0 ? s.val() : "");
					str.push("\",");
				} else if (i === 3) {
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