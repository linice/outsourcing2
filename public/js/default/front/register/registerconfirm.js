$(document).ready(function(){
	$(function() {
		var referee = $.trim($('#referee').html());
		if (!referee) {
			$('#tr_referee').hide();
		}
	});
	
	
	/**
	 * 处理用户注册
	 */
	$('#btn_register').click(function(){
		$('#form_register').attr('action', '/front_register/handleregister');
		$('#form_register').submit();
	});
	
	
	/**
	 * 返回用户注册
	 */
	$('#btn_backReg').click(function(){
		$('#form_register').attr('action', '/front_register/register');
		$('#form_register').submit();
	});
	
}); //End: $(document).ready