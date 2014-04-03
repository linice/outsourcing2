$(document).ready(function(){
	function valChkbox(name, errMsg) {
		var flag = false;
		$("input[name='"+name+"[]']").each(function() {
			if (!!$(this).attr("checked")) {
				flag = true;
				return false;
			}
		});
		if(!flag) {
			alert(errMsg);
			return false;
		}
		return true;
	}
	
	function valMul(id1, id2, errMsg) {
		if(!$("#"+id1).val() && !$("#"+id2).val()) {
			alert(errMsg);
			return false;
		}
		return true;
	}
	
	function valForm() {
		if (!valChkbox("careers", err["careers_can_not_be_empty"])) return false;
		if (!valChkbox("languages", err["languages_can_not_be_empty"])) return false;
		if (!valChkbox("industries", err["industries_can_not_be_empty"])) return false;
		if (!$("#caseName").val()) {
			alert(err["casename_can_not_be_empty"]);
			return false;
		}
		if (!$("#jpl").val()) {
			alert(err["jpl_can_not_be_empty"]);
			return false;
		}
		if ($("#caseRange1")[0].selectedIndex > $("#caseRange2")[0].selectedIndex) {
			alert(ERROR_CREATE_CASE_RANGE);
			return false;
		}
		if (!!$("#startDateFlag").attr("checked") && !$("#startDate").val()) {
			alert(err["startdate_can_not_be_empty"]);
			return false;
		}
		if (!valMul("endRange", "endDate", err["enddate_can_not_be_empty"])) return false;
		if (!!$("#endDate").val()) {
			var st = $("#startDate").val() || new Date();
			var et = $("#endDate").val()
			var betweenTime = daysBetween(st, et);
			if (!!betweenTime && betweenTime < 0) {
				alert(err["enddate_less_than_startdate"]);
				return false;
			}
		}
		if (!$("#workingPlace").val() || !$("#workingPlaceDetail").val()) {
			alert(err["workingplace_can_not_be_empty"]);
			return false;
		}
		var timeliness = $("#timeliness").val();
		if (!timeliness) {
			alert(err["timeliness_can_not_be_empty"]);
			return false;
		}
		var alivetime = daysBetween(new Date(), timeliness);
		if (!alivetime || alivetime > 60) {
			alert(err["timeliness_too_longer"]);
			return false;
		}
		return true;
	}

	function adjustUnitPrice(value) {
		if (value == 'UNIT_PRICE_EXP') {
			$("#unitPriceDetail").hide();
			$("#unitPriceDetail1").val('')
			$("#unitPriceDetail2").val('')
		} else if (value == 'UNIT_PRICE_MAX') {
			$("#unitPriceDetail").show();
			$("#unitPriceDetail1").hide();
			$("#unitPriceDetail1").val('')
			$("#unitPriceDetail2").show();
		} else if (value == 'UNIT_PRICE_MIN') {
			$("#unitPriceDetail").show();
			$("#unitPriceDetail1").show();
			$("#unitPriceDetail2").hide();
			$("#unitPriceDetail2").val('')
		} else if (value == 'UNIT_PRICE_RANGE') {
			$("#unitPriceDetail").show();
			$("#unitPriceDetail1").show();
			$("#unitPriceDetail2").show();
		}
	}
	
	function adjustAge(value) {
		if (value == 'AGE_REQ_NONE') {
			$("#ageReqDetail").hide();
			$("#ageReqDetail1").val('')
			$("#ageReqDetail2").val('')
		} else if (value == 'AGE_REQ_MAX') {
			$("#ageReqDetail").show();
			$("#ageReqDetail1").hide();
			$("#ageReqDetail1").val('')
			$("#ageReqDetail2").show();
		} else if (value == 'AGE_REQ_MIN') {
			$("#ageReqDetail").show();
			$("#ageReqDetail1").show();
			$("#ageReqDetail2").hide();
			$("#ageReqDetail2").val('')
		} else if (value == 'AGE_REQ_RANGE') {
			$("#ageReqDetail").show();
			$("#ageReqDetail1").show();
			$("#ageReqDetail2").show();
		}
	}

	function adjustOvertimePay(value) {
		if (value == 'OVERTIME_PAY_Y') {
			$("#overtimePayLabel").show();
		} else {
			$("#overtimePayDetail").val('');
			$("#overtimePayLabel").hide();
		}
	}
	
	function init() {
		$('#startDate').datepicker({
			changeYear: true,
			changeMonth: true,
			dateFormat: 'yy-mm-dd',
			minDate: '1970-01-01',
			yearRange: "1970:2222"
		});
		
		$('#endDate').datepicker({
			changeYear: true,
			changeMonth: true,
			dateFormat: 'yy-mm-dd',
			minDate: new Date(),
			yearRange: "1970:2222"
		});
		
		$('#timeliness').datepicker({
			changeYear: true,
			changeMonth: true,
			dateFormat: 'yy-mm-dd',
			minDate: new Date(),
			yearRange: "1970:2222"
		});
		
		$("input:checkbox[name='industries[]']").click(function() {
			$("input:checkbox[name='industries[]']").attr("checked", false);
			$(this).attr("checked", true);
		})

		$("#caseName").blur(function() {
			if(!$(this).val()) {
				$(this).showError(err["casename_can_not_be_empty"]);
			} else {
				$(this).hideError();
			}
		});
		
		$('#startDate').attr("disabled", true);
		
		$("input[type='radio'][name='startDateCurrent']").first().click(function() {
			$('#startDate').val("");
			$('#startDate').attr("disabled", true);
		});
		$("input[type='radio'][name='startDateCurrent']").last().click(function() {
			$('#startDate').attr("disabled", false);
		});
		
		$('#endDate').attr("disabled", true);
		
		$("input[type='radio'][name='endDateFlag']").first().click(function() {
			$('#endDate').val("");
			$('#endRange').attr("disabled", false);
			$('#endDate').attr("disabled", true);
		});
		$("input[type='radio'][name='endDateFlag']").last().click(function() {
			$('#endRange').val("");
			$('#endRange').attr("disabled", true);
			$('#endDate').attr("disabled", false);
		});
		
		$("#ageReq").change(function() {
			var value = $(this).val();
			adjustAge(value);
		});
		
		$("#unitPrice").change(function() {
			var value = $(this).val();
			adjustUnitPrice(value);
		})
		
		$("#overtimePay").change(function() {
			var value = $(this).val();
			adjustOvertimePay(value);
		})
		
		$("#btn_save").click(function() {
			if (valForm()) {
				$.ajax({
					url: '/lp_case/savecaseindraft',
					type: 'post',
					async: false,
					dataType: 'json', //xml, json, script or html
					data: $('#form_case').serializeArray(),
					success: function(data, textStatus, jqXHR) {
						if (data['err'] == 0) { //用户注册信息通过验证
							if (!!data["id"]) {
								$("#type").val(data["type"]);
								$("#caseId").val(data["id"]);
							}
							alert(data['msg']);
						} else { //用户注册信息不合法
							if (!!data["errField"] && $("#"+data["errField"]).length > 0) {
								$("#"+data["errField"]).focus();
								$("#"+data["errField"]).showError(data['msg']);
							} else 
								alert(data['msg']);
						}
					}
				});
			}
		});
		$('#btn_confirm').click(function() {
			if (valForm()) {
				$('#form_case').attr("action", "/lp_case/createcaseconfirm");
				$('#form_case').submit();
			}
		})
		
		/**
		 * 设定员工推荐度竞价排名
		 */
		$('#akbSettingBtn').click(function(){
			
			$('#recmdEmpList').ligerGrid({
				checkbox: false,
				rownumbers: true,
				columns: [{display: CASE_ID, name: 'code', width: 80},
						  {display: CASE_NAME, name: 'name'},
						  {display: case_business_req, name: 'businessReqValue'},
						  {display: case_technical_req, name: 'technicalReqValue'},
						  {display: case_end_range, name: 'period', width: 85},
						  {display: WORKPLACE, name: 'workingPlaceValue', width: 85},
						  {display: PRICE_UNIT_MONTHLY, name: 'unitPriceValue', width: 85},
						  {display: case_timeliness, name: 'timeliness', width: 80},
						  {display: COL_A_SORT_PRICE_YEN, name: 'akb', width: 55} ], 
				pageSize: 10,
				width: "100%",
				columnWidth: 89,
				url: '/front_case/searchreleasecase/casetype/cases_recommend'
			});
			
			//弹出设定竞价点数对话框
			$('#dlg_recmdEmp').dialog({
				autoOpen: true,
				modal: true,
				width: 800,
				buttons: {
					SURE: function(){
						var balance = Number($('#balance').val());
						var point = Number($('#point').val());
						if (point > balance) {
							$('#tip_recmdEmp').html(ALERT_TO_MORE_INPUT);
							return;
						}
						$('#tip_recmdEmp').html('');
						$('#akb').val(point);
						$(this).dialog('close');
					},
					CANCEL: function(){
						$(this).dialog('close');
					}
				}
			});
		});	
	}

	function initSelect() {
		$("#jpl").val(orgCase["jpl"]);
		//var caseRange = !orgCase["case_range"] ? [] : orgCase["case_range"].split("~");
		$("#caseRange1").val(orgCase["case_range_start"]);
		$("#caseRange2").val(orgCase["case_range_end"]);
		$("#endRange").val(orgCase["end_range"]);
		$("#delay").val(orgCase["delay"]);
		$("#workingPlace").val(orgCase["working_place"]);
		$("#unitPrice").val(orgCase["unit_price"]);
		$("#overtimePay").val(orgCase["overtime_pay"]);
		$("#interviewer").val(orgCase["interviewer"]);
		$("#ageReq").val(orgCase["age_req"]);
		$("#countryReq").val(orgCase["country_req"]);
		$("#visibility").val(orgCase["visibility"]);
		$("#unitPriceUnit").val(orgCase["unit_price_unit"]);
	}
	
	function initChkbox() {
		if (!!orgCase["careers"]) {
			$.each(orgCase["careers"].split(";"), function(i, v) {
				$("input[type='checkbox'][name='careers[]'][value='"+v+"']").attr("checked", true);
			});
		}
		if (!!orgCase["languages"]) {
			$.each(orgCase["languages"].split(";"), function(i, v) {
				$("input[type='checkbox'][name='languages[]'][value='"+v+"']").attr("checked", true);
			});
		}
		if (!!orgCase["industries"]) {
			$.each(orgCase["industries"].split(";"), function(i, v) {
				$("input[type='checkbox'][name='industries[]'][value='"+v+"']").attr("checked", true);
			});
		}
		if (!orgCase["end_date"]) {
			$("input:radio[name='endDateFlag']").first().attr("checked", true);
			$("#endDate").val("");
			$("#endDate").attr("disabled", true);
		} else {
			$("input:radio[name='endDateFlag']").last().attr("checked", true);
			$("#endDate").attr("disabled", false);
			$("#endRange").attr("disabled", true);
			$("#endDate").val(orgCase["end_date"]);
		}
		if (!orgCase["start_date"]) {
			$("input:radio[name='startDateCurrent']").first().attr("checked", true);
			$("#startDate").val("");
			$("#startDate").attr("disabled", true);
		} else {
			$("input:radio[name='startDateCurrent']").last().attr("checked", true);
			$("#startDate").attr("disabled", false);
			$("#startDate").val(orgCase["start_date"]);
		}
	}
	function initText() {
		$("#caseId").val(orgCase['id']);
		$("#type").val(orgCase['type']);
		$("#caseName").val(orgCase['name']);
		$("#businessReq").val(orgCase['business_req']);
		$("#technicalReq").val(orgCase['technical_req']);
		$("#technicalReq").val(orgCase['technical_req']);
		$("#workingPlaceDetail").val(orgCase['working_place_detail']);
		$("#unitPriceDetail1").val(orgCase['unit_price_low']);
		$("#unitPriceDetail2").val(orgCase['unit_price_high']);
		$("#overtimePayDetail").val(orgCase['overtime_pay_detail']);
		$("#ageReqDetail1").val(orgCase['age_req_begin']);
		$("#ageReqDetail2").val(orgCase['age_req_end']);
		$("#timeliness").val(orgCase['timeliness']);
		$("#remark").val(orgCase['remark']);
		$("#akb").val(orgCase['akb']);
	}

	function initValue() {
		initSelect();
		initChkbox();
		initText();
	}
	init();
	if (!!orgCase && !$.isEmptyObject(orgCase)) {
		initValue()
	}
	
	adjustUnitPrice($("#unitPrice").val());
	adjustAge($("#ageReq").val());
	adjustOvertimePay($("#overtimePay").val())
});