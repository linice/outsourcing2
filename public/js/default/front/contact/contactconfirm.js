$(document).ready(function(){
	//联系信息确认
	$('#btn_confirm').click(function(){
		$('#form_contact_confirm').attr('action', '/front_contact/contacthandle');
		$('#form_contact_confirm').submit();
	});
	
	
	//联系我们返回
	$('#btn_back').click(function(){
		$('#form_contact_confirm').attr('action', '/front_contact/contact');
		$('#form_contact_confirm').submit();
	});
	
	
}); //End: $(document).ready