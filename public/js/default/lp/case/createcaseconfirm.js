$(function() {
	$('#btn_back').click(function(){
		$("#form_confirm").attr("action", "/lp_case/createcase");
		$("#form_confirm").submit();
	});
	
	$('#btn_publish').click(function(){
		$("#form_confirm").attr("action", "/lp_case/publishcase");
		$("#form_confirm").submit();
	})
	
});
