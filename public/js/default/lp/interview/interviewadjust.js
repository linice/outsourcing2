$(function(){
	$('#btn_confirm').click(function(){
		if (val()) {
			$("#dynForm").attr("action", "/lp_interview/interviewadjustconfirm");
			$("#dynForm input[name='linkman']").val($("#linkman").val());
			$("#dynForm input[name='telephone']").val($("#telephone").val());
			$("#dynForm input[name='interviewPlace']").val($("#interviewPlace").val());
			$("#dynForm #usrInfoList").val(interviewToJson());
			$("#dynForm").submit();
		}
		return false;
	});
	
	$("#tab_list").find("input.Wdate").datetimepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
		minDate: new Date(),
		yearRange: "1970:2222"
	});
	
	$("#chkAll").click(function() {
		if(!!$(this).attr("checked")) {
			var t;
			$.each($("#tab_list tbody").find("tr"), function(i, v) {
				if(i == 0) {
					t = $(this).find("input.Wdate").val();
				} else {
					$(this).find("input.Wdate").val(t);
				}
			});
		}
	})
	
	$("#tab_list tbody tr:first").find("input.Wdate").blur(function() {
		if($("#chkAll").attr("checked")) {
			var t;
			$.each($("#tab_list tbody").find("tr"), function(i, v) {
				if(i == 0) {
					t = $(this).find("input.Wdate").val();
				} else {
					$(this).find("input.Wdate]").val(t);
				}
			});
		}
	})
	
	function interviewToJson() {
		var str = ["["];
		$("#tab_list tbody tr").each(function() {
			str.push("{");
			$.each($(this).children(), function(i, v) {
				if (i == 0) {
					genOb(str, "baseinfo", $(this).text());
				} else if(i == 4) {
					genOb(str, "interviewTime", $(this).find("input.Wdate").val());
					genOb(str, "interviewId", $(this).find("input[name='interviewId']").val(), true);
				}
			})
			str.push("}");
			str.push(",");
		})
		str.pop();
		str.push("]");
		return str.join("")
	}
	
	function genOb(str, name, value, isend) {
		str.push("\""+name+"\":\"");
		str.push(value);
		if (!isend) 
			str.push("\",");
		else 
			str.push("\"");
	}
	
	function valList() {
		var flag = true;
		$.each($("#tab_list tbody").find("tr"), function() {
			var t1 = $(this).find("input.Wdate").val();
			if(!t1) {
				alert("面试时间不能为空!")
				flag = false;
				return false;
			}
		})
		return flag;
	}
	
	function val() {
		if(!$("#linkman").val()) {
			alert("联系人不能为空!")
			return false;
		} else if(!$("#telephone").val()) {
			alert("联系电话不能为空!")
			return false;
		} else if(!$("#interviewPlace").val()) {
			alert("面试地点不能为空!")
			return false;
		}
		return valList();
	}
});