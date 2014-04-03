$(document).ready(function(){
	/**
	 * 打印简历
	 */
	$('#btn_print_resume').click(function() {
		$(this).hide();
		window.print();
		$(this).show();
	});
	
	
	
	
}); //End: $(document).ready