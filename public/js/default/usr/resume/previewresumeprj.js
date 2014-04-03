$(document).ready(function(){
	
	$(function(){
		var prjDateBegin = $('#prjDateBegin').html();
		var prjDateEnd = $('#prjDateEnd').html();
		var prjMonthCnt = calcMonthCnt(prjDateBegin, prjDateEnd);
		
		$('#prjMonthCnt').html(prjMonthCnt);
	});
	
}); //End: $(document).ready