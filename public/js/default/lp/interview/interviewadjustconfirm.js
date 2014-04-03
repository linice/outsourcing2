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
		History().back();
		//window.open('/lp_interview/interview');
		return false;
	});
	
	$("#btn_confirm").click(function() {
		$("#dynForm").attr("action", "/lp_interview/saveinterviewadjust")
		$("#dynForm").submit();
	})
});