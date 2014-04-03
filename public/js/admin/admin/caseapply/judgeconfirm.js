$(function(){
	function addRows(rows) {
		var t = $("#tab_list").find("tbody");
		$.each(rows, function(i, v) {
			var tr = ['<tr><td align="left">'+v.usrInfo+'</td>'];
			tr.push('<td style="text-align:center">'+v.judgeResultValue+'</td>');
			tr.push('<td style="text-align:center">'+v.lackReasonValue+'</td>');
			tr.push('<td align="left">'+v.remark);
			tr.push('<input type="hidden" name="usrId" value="'+v.usrId+'"/>');
			tr.push('</td></tr>');
			t.append(tr.join(""));
		})
	}
	
	addRows(usrList);
	
	$('#btn_back').click(function(){
		$("#dynForm").attr("action", "/admin/admin_judge/judge");
		$("#dynForm").submit()
		return false;
	});
	
	$("#btn_confirm").click(function() {
		$("#dynForm").attr("action", "/admin/admin_judge/savecasejudge");
		$("#dynForm").submit()
	})
	
});