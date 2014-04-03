$(function(){
	$('#btn_confirm').click(function(){
		if (val()) {
			$("#dynForm").attr("action", "/lp_interview/interviewresultinformconfirm");
			$("#dynForm #usrInfoList").val(tableToJson());
			$("#dynForm").submit();
		}
		return false;
	});
	
	$("#tab_list input.Wdate").datetimepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
		minDate: new Date(),
		yearRange: "1970:2222"
	});
	
	$("#tab_list select[name='result']").change(function() {
		var a = $(this).val();
		$.each($(this).parent().nextAll(), function(i, v) {
			if (i == 0) {
				if (a == 'OK') {
					$(this).find("input[name='OKReason']").show();
					$(this).find("select[name='NGReason']").hide();
				} else {
					$(this).find("input[name='OKReason']").hide();
					$(this).find("select[name='NGReason']").show();
				}
			} else {
				var input = $(this).find("input[type='text']");
				if (a == "OK") {
					input.show();
				} else {
					input.val("");
					input.hide();
				}
			}
		});
	})
	
	function tableToJson() {
		var str = ["["];
		$("#tab_list tbody tr").each(function() {
			str.push("{");
			var result = "";
			$.each($(this).children(), function(i, v) {
				if (i == 0) {
					genOb(str, "baseinfo", $(this).text());
				} else if (i == 1) {
					result = $(this).find("select[name='result']").val();
					genOb(str, "result", result);
				} else if (i == 2) {
					if (result == 'OK') {
						var okreason = $(this).find("input[name='OKReason']").val();
						genOb(str, "reason", okreason);
						genOb(str, "reasonValue", okreason);
					} else {
						var select = $(this).find("select[name='NGReason'] option:selected");
						genOb(str, "reason", select.val());
						genOb(str, "reasonValue", select.text());
					}
				} else {
					var input = $(this).find("input[type='text']")
					genOb(str, input.attr("name"), input.val());
				}
				if (i == 6) {
					genOb(str, "applyId", $(this).find("input[name='applyId']").val(), true);
				}
			})
			str.push("}");
			str.push(",")
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
		var flag = true;
		$.each($("#tab_list tbody").find("tr"), function() {
			var ret = $(this).find("select[name='result']").val();
			if (ret == 'OK') {
				if (!$(this).find("input[name='admission_date']").val()) {
					alert(ADMISSION_TIME_IS_REQUIRED)
					flag = false;
					return false;
				}
				if (!$(this).find("input[name='admission_place']").val()) {
					alert(INTERVIEWPLACE_IS_REQUIRED)
					flag = false;
					return false;
				}
				if (!$(this).find("input[name='admission_linkman']").val()) {
					alert(CONTACT_IS_REQUIRED)
					flag = false;
					return false;
				}
				if (!$(this).find("input[name='admission_telephone']").val()) {
					alert(PHONE_IS_REQUIRED)
					flag = false;
					return false;
				}
			}
		})
		return flag;
	}
});