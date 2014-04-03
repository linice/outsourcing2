$(function() {
	$('#btn_back').click(function(){
		$("#c_form_case").attr("action", "/lp_case/editcase");
		$("#c_form_case").submit();
	});
	
	$('#btn_publish').click(function(){
		$("#c_form_case").attr("action", "/lp_case/savemodifycase");
		$("#publish").val("Y");
		$("#c_form_case").submit();
	});
	
	$('#btn_save').click(function(){
		$("#c_form_case").attr("action", "/lp_case/savemodifycase");
		$("#publish").val("N")
		$("#c_form_case").submit();
	});
});
