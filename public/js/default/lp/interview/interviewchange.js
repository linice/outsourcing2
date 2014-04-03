$(function(){
	$('#btn_confirm').click(function(){
		if (val()) {
			$("#dynForm").attr("action", "/lp_interview/interviewchangeconfirm");
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
			var t1,t2,t3;
			$.each($("#tab_list tbody").find("tr"), function(i, v) {
				if(i == 0) {
					t1 = $(this).find("input[name='interviewTime1']").val();
					t2 = $(this).find("input[name='interviewTime2']").val();
					t3 = $(this).find("input[name='interviewTime3']").val();
				} else {
					$(this).find("input[name='interviewTime1']").val(t1);
					$(this).find("input[name='interviewTime2']").val(t2);
					$(this).find("input[name='interviewTime3']").val(t3);
				}
			});
		}
	})
	
	$("#tab_list tbody tr:first").find("input.Wdate").blur(function() {
		if($("#chkAll").attr("checked")) {
			var t1,t2,t3;
			$.each($("#tab_list tbody").find("tr"), function(i, v) {
				if(i == 0) {
					t1 = $(this).find("input[name='interviewTime1']").val();
					t2 = $(this).find("input[name='interviewTime2']").val();
					t3 = $(this).find("input[name='interviewTime3']").val();
				} else {
					$(this).find("input[name='interviewTime1']").val(t1);
					$(this).find("input[name='interviewTime2']").val(t2);
					$(this).find("input[name='interviewTime3']").val(t3);
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
				} else {
					genOb(str, "interviewTime"+i, $(this).find("input.Wdate").val());
				}
				if (i == 3) {
					genOb(str, "applyId", $(this).find("input[name='applyId']").val(), true);
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
	
	function val() {
		if(!$("#linkman").val()) {
			alert(CONTACT_IS_REQUIRED)
			return false;
		} else if(!$("#telephone").val()) {
			alert(PHONE_IS_REQUIRED)
			return false;
		} else if(!$("#interviewPlace").val()) {
			alert(INTERVIEWPLACE_IS_REQUIRED)
			return false;
		}
		return valList();
	}
	
	function valList() {
		var flag = true;
		$.each($("#tab_list tbody").find("tr"), function() {
			var t1 = $(this).find("input[name='interviewTime1']").val();
			var t2 = $(this).find("input[name='interviewTime2']").val();
			var t3 = $(this).find("input[name='interviewTime3']").val();
			if(!t1 && !t2 && !t3) {
				alert(EXPECT_INTERVIEW_IS_REQUIRED)
				flag = false;
				return false;
			}
		})
		return flag;
	}
});