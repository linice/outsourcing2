$(function(){
	//编辑案件
	$('#btn_editCase').click(function(){
		$("#form").attr("action", "/lp_case/editcase");
		$("#form").submit();
	});
	
	//案件应聘管理
	$('#btn_caseApplyMgt').click(function(){
		$("#form").attr("action", "/lp_caseapply/caseapplymgt");
		$("#form").submit();
	});
	
	//案件关闭
	$('#btn_closeCase').click(function(){
		$.ligerMessageBox.confirm("法人关闭案件", "确定要关闭当前案件?", function(r) {
			if (!!r) {
				$.ajax({
					url: '/lp_case/closecaseinjson',
					type: 'post',
					async: false,
					dataType: 'json', //xml, json, script or html
					data: {ids: $("#caseId").val()},
					success: function(data, textStatus, jqXHR) {
						if (data['err'] == 0) {
							alert(data['msg']);
							window.open("/lp_casecollect/incollectcaselist");
						} else {
							alert(data['msg']);
						}
					}
				});
			}
		});
	});
});