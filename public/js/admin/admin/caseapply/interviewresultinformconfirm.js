$(function(){
	function addRows(rows) {
		var t = $("#tab_list").find("tbody");
		$.each(rows, function(i, v) {
			var tr = ['<tr><td align="left">'+v.baseinfo+'</td>'];
			tr.push('<td style="text-align:left" class="fgrey">'+v.result+'</td>');
			tr.push('<td style="text-align:left" class="fgrey">'+v.reasonValue+'</td>');
			tr.push('<td style="text-align:center" class="fgrey">'+v.admission_date+'</td>');
			tr.push('<td style="text-align:center" class="fgrey">'+v.admission_place+'</td>');
			tr.push('<td style="text-align:center" class="fgrey">'+v.admission_linkman+'</td>');
			tr.push('<td style="text-align:center" class="fgrey">'+v.admission_telephone+'</td>');
			tr.push('</tr>');
			t.append(tr.join(""));
		})
	}
	
	addRows(usrList);
	
	$("#btn_confirm").click(function() {
		$("#dynForm").attr("action", "/admin/admin_interview/saveinterviewresult")
		$("#dynForm").submit();
	})
	
	$('#btn_back').click(function(){
		$("#dynForm").attr("action", "/admin/admin_interview/interviewresultinform")
		$("#dynForm").submit();
		return false;
	});
});