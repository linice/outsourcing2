$(document).ready(function(){
	//保存简历，进入下一步
	$('#btn_save').click(function(){
		window.open('/lp_emp/editresumeprj');
		return false;
	});
	
	
	//预览简历
	$('#btn_preview').click(function(){
		window.open('/lp_emp/resumepreview');
		return false;
	});
}); //End: $(document).ready