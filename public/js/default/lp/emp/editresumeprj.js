$(document).ready(function(){
	//保存简历，进入other
	$('#btn_save').click(function(){
		window.open('/lp_emp/editresumeother');
		return false;
	});
	
	
	//预览简历
	$('#btn_preview').click(function(){
		window.open('/lp_emp/resumepreview');
		return false;
	});
	
	//返回，进入lang
	$('#btn_back').click(function(){
		window.open('/lp_emp/editresumelang');
		return false;
	});
}); //End: $(document).ready