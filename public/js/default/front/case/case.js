$(function(){
	//普通检索
	$('#btn_search').click(function() {
		$("#dynForm").attr("action", "/front_case/casesearchresult");
		$("#dynForm").submit();
		return false;
	});
	
	//高级检索
	$('#btn_search_advanced').click(function(){
		$("#dynForm").attr("action", "/front_case/casesearchresult");
		$("#dynForm").submit();
		return false;
	});
	
	$('input.Wdate').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
		minDate: new Date(1970,1,1),
		yearRange: "1970:2222"
	});
	
	$('input.Wdate').attr("disabled", true);
	$("input:radio[name='radiobutton']").click(function() {
		if($(this).val() == '1') {
			$('input.Wdate').val("");
			$('input.Wdate').attr("disabled", true);
		} else if($(this).val() == '2') {
			$('input.Wdate').attr("disabled", false);
		}
	});
	
	$("input[name='unitprice']").keyup(function() {
		$(this).val($(this).val().replace(/\D/, ""));
	});
	
	$("input:checkbox[name='careersAll']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.careerslist").attr("checked", true);
		} else {
			$("input:checkbox.careerslist").attr("checked", false);
		}
	});
	
	$("input:checkbox[name='languagesAll']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.languageslist").attr("checked", true);
		} else {
			$("input:checkbox.languageslist").attr("checked", false);
		}
	});
	
	$("input:checkbox[name='industriesAll']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.industrieslist").attr("checked", true);
		} else {
			$("input:checkbox.industrieslist").attr("checked", false);
		}
	});
	
	$("input:checkbox[name='overseas']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.overlist").attr("checked", true);
		} else {
			$("input:checkbox.overlist").attr("checked", false);
		}
	});
	
	$("input:checkbox[name='japans']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.japanlist").attr("checked", true);
		} else {
			$("input:checkbox.japanlist").attr("checked", false);
		}
	});
	
	$("input:checkbox.careerslist").click(function() {
		var checked = true;
		$.each($("input:checkbox.careerslist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='careersAll']").attr("checked", checked);
	})
	
	$("input:checkbox.languageslist").click(function() {
		var checked = true;
		$.each($("input:checkbox.languageslist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='languagesAll']").attr("checked", checked);
	})
	
	$("input:checkbox.industrieslist").click(function() {
		var checked = true;
		$.each($("input:checkbox.industrieslist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='industriesAll']").attr("checked", checked);
	})
	
	$("input:checkbox.overlist").click(function() {
		var checked = true;
		$.each($("input:checkbox.overlist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='overseas']").attr("checked", checked);
	});
	
	$("input:checkbox.japanlist").click(function() {
		var checked = true;
		$.each($("input:checkbox.japanlist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='japans']").attr("checked", checked);
	});
	
	if (!!orgSearchFields && !$.isEmptyObject(orgSearchFields)) {
		if (!!orgSearchFields['casename']) {
			$("#range").val(orgSearchFields['range']);
			$("#casename").val(orgSearchFields['casename']);
		}
		if (!!orgSearchFields['casetype']) {
			$("input:radio[name='casetype'][value='"+orgSearchFields['casetype']+"']").attr("checked", true);
		}
		if (!!orgSearchFields['careers']) {
			$.each(orgSearchFields['careers'], function(i, v) {
				$("input:checkbox[name='careers[]'][value='"+v+"']").attr("checked", true);
			});
		}
		if (!!orgSearchFields['languages']) {
			$.each(orgSearchFields['languages'], function(i, v) {
				$("input:checkbox[name='languages[]'][value='"+v+"']").attr("checked", true);
			});
		}
		if (!!orgSearchFields['industries']) {
			$.each(orgSearchFields['industries'], function(i, v) {
				$("input:checkbox[name='industries[]'][value='"+v+"']").attr("checked", true);
			});
		}
		if (!!orgSearchFields['workplace']) {
			$.each(orgSearchFields['workplace'], function(i, v) {
				$("input:checkbox[name='workplace[]'][value='"+v+"']").attr("checked", true);
			});
			var jpchecked = true;
			$.each($("input:checkbox.japanlist"), function() {
				if (!$(this).attr("checked")) {
					jpchecked = false;
					return false;
				}
			});
			if (jpchecked) {
				$("input:checkbox[name='japans']").attr("checked", true);
			}
			var oschecked = true;
			$.each($("input:checkbox.overlist"), function() {
				if (!$(this).attr("checked")) {
					oschecked = false;
					return false;
				}
			});
			if (oschecked) {
				$("input:checkbox[name='overseas']").attr("checked", true);
			}
		}
		var careerschecked = true;
		$.each($("input:checkbox.careerslist"), function() {
			if (!$(this).attr("checked")) {
				careerschecked = false;
				return false;
			}
		});
		if (careerschecked) {
			$("input:checkbox[name='careersAll']").attr("checked", true);
		}
		var languageschecked = true;
		$.each($("input:checkbox.languageslist"), function() {
			if (!$(this).attr("checked")) {
				languageschecked = false;
				return false;
			}
		});
		if (languageschecked) {
			$("input:checkbox[name='languagesAll']").attr("checked", true);
		}
		var industrieschecked = true;
		$.each($("input:checkbox.industrieslist"), function() {
			if (!$(this).attr("checked")) {
				industrieschecked = false;
				return false;
			}
		});
		if (industrieschecked) {
			$("input:checkbox[name='industriesAll']").attr("checked", true);
		}
	}
});


