$(function() {
	$('#btn_back').click(function(){
		$("#form_confirm").attr("action", "/admin/admin_case/createcase");
		$("#form_confirm").submit();
	});
	
	$('#btn_publish').click(function(){
		$("#form_confirm").attr("action", "/admin/admin_case/publishcase");
		$("#form_confirm").submit();
	})
	
});
