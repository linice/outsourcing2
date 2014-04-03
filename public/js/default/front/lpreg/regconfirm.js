$(document).ready(function() {
	//初始化
	$(function(){
		//将input text的边框去掉
		$('input[type="text"]').attr('readonly', true).css('border', '0');
	});
	
	
	//法人注册确认
	$('#btn_confirm').click(function(){
		$('#form_confirm').attr("action", "/front_lpreg/regsucc");
		$('#form_confirm').submit();
	});
	
	
	//法人注册返回
	$('#btn_back').click(function(){
		$('#form_confirm').attr("action", "/front_lpreg/lpreg");
		$('#form_confirm').submit();
		return false;
	});
	
	
	
});