$(function(){
	function addRows(rows) {
		var t = $("#tab_list").find("tbody");
		$.each(rows, function(i, v) {
			var tr = ['<tr><td align="left">'+v.baseinfo+'</td>'];
			tr.push('<td style="text-align:center" class="fgrey">'+v.interviewTime+'</td>');
			tr.push('</tr>');
			t.append(tr.join(""));
		})
	}
	
	addRows(usrList);
	
	$('#btn_back').click(function(){
		$("#dynForm").attr("action", "/admin/admin_interview/interviewinfoedit")
		$("#dynForm").submit();
	});
	
	$("#btn_confirm").click(function() {
		$("#dynForm").attr("action", "/admin/admin_interview/saveinterviewinfoedit")
		$("#dynForm").submit();
	})
});