$(document).ready(function(){
	/**
	 * 初始化简历模板
	 */
	$(function() {
		$('input[name="lp_propose_temp"][value="' + lp_propose_temp + '"]').attr('checked', true);
	});
	
	/**
	 * 模板1，2，附加信息预览
	 */
	$('a[name="preview"]').click(function() {
		var p = $(this).next();
		if ($(this).attr("id") == 'emailOption') {
			$.ajax({
				url: "/lp_lp/findemailoption",
				type: 'post',
				async: false,
				dataType: 'json', //xml, json, script or html
				success: function(data, textStatus, jqXHR) {
					if (!!data) {
						var tpl = lp_propose_temp;
						p.find("input:radio[name='layout'][value='"+tpl+"']").attr("checked", true);
						p.find("textarea[name='layoutHeader']").val(!!data[tpl] ? data[tpl]["header"]:'');
						p.find("textarea[name='layoutFooter']").val(!!data[tpl] ? data[tpl]["footer"]:'');
						if (!!data["temp1"]) {
							$('#temp1Header').val(data['temp1']['header']);
							$('#temp1Footer').val(data['temp1']['footer']);
						}
						if (!!data['temp2']) {
							$('#temp2Header').val(data['temp2']['header']);
							$('#temp2Footer').val(data['temp2']['footer']);
						}
						p.show();
					}
				}
			});
		} else {
			p.show();
		}
	});

	
	/**
	 * 关闭预览弹出框
	 */
	$("input[name='closeBtn'][type='image']").click(function() {
		$(this).parents(".floatdiv").hide();
	});

	
	/**
	 * 修改模板
	 */
	$("#updTempBtn").click(function() {
		$.ajax({
			url: '/lp_lp/modifyemailtemp',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: {temp: $('input[name="lp_propose_temp"]:checked').val()},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					alert(data['msg']);
				} else {
					alert(data['msg']);
				}
			}
		});
	});

	
	/**
	 * 保存附加信息
	 */
	$("#layoutSubmitBtn").click(function() {
		var thisDiv = $(this).parents(".floatdiv");
		$.ajax({
			url: "/lp_lp/modifyemailoption",
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: thisDiv.find("form").serializeArray(),
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					alert(data['msg'], null, null, function() {thisDiv.hide();});
				} else {
					alert(data['msg']);
				}
			}
		});
	});

	
	/**
	 * 关闭附加信息弹出框
	 */
	$("#layoutCancelBtn").click(function() {
		$(this).parents('.floatdiv').hide();
	});
	
	
	$("input:radio[name='layout']").click(function() {
		var value = $(this).val();
		var thisDiv = $(this).parents(".floatdiv");
		thisDiv.find("textarea[name='layoutHeader']").val($("#"+value+"Header").val());
		thisDiv.find("textarea[name='layoutFooter']").val($("#"+value+"Footer").val());
	});

	
	$('#lpsettingmodifyBtn').click(function() {
		$.ajax({
			url: "/lp_lp/modifylpsetting",
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: $("#lpsettingform").serializeArray(),
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					alert(data['msg']);
				} else {
					alert(data['msg']);
				}
			}
		});
	});
	
	
	/**
	 * 退会 
	 */
	$('#btn_unsubscribe').click(function(){
		$.ligerDialog.confirm(CONFIRM_UNSUBSCRIBE, function (ret) {
		    if (ret == true) {
		    	$.ajax({
		    		url: '/lp_lp/unsubscribe',
		    		type: 'get',
		    		async: true,
		    		dataType: 'json', //xml, json, script or html
		    		data: null,
		    		success: function(data, textStatus, jqXHR) {
		    			if (data['err'] == 0) {
		    				location.href = '/?pwd=10';
		    			} else {
		    				$('#tip_unsubscribe').html(data['msg']);
		    			}
		    		}
		    	});
		    } //End: if (ret == true)
		});
	});
	
	
	
}); //End: $(document).ready


