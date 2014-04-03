$(document).ready(function(){
	/**
	 * 初始化简历——基本信息
	 */
	$(function(){
		var jaAbility = resume['ja_ability'];
		var enAbility = resume['en_ability'];
		var birthday = resume['birthday'];
		var fullAge = calcFullAge(birthday);
		
		$('#jaAbility_' + jaAbility).show();
		$('#enAbility_' + enAbility).show();
		
		if (!isNaN(fullAge)) {
			$('#fullAge').html(fullAge);
		}
	});
	
	
	
	
}); //End: $(document).ready