$(document).ready(function(){
	//初始化联系信息
	$(function(){
		//如果联系信息不为空，则初始化
		if (contactInfo) {
			contactInfo = JSON.parse(contactInfo);
			$('#isAgree').attr('checked', true);
			$('#fullname').val(contactInfo['fullname']);
			$('#tel').val(contactInfo['tel']);
			$('#email').val(contactInfo['email']);
			$('#content2').val(contactInfo['content']);
			
		}
	});
	
	
	//联系信息提交
	$('#btn_submit').click(function(){
		$('#form_contact').ajaxSubmit({
			url: '/front_contact/contactverify',
			dataType: 'json',
			success: function(data, status, xhr, $form) {
				if (data['err'] == 0) {
					$('#tip_contact').html('');
					$('#contactInfo').val(data['jsContactInfo']);
					$('#form_contact_verify').submit();
				} else {
					$('#tip_contact').html(data['msg']);
				}
			}
		});
		return false;
	});
	
}); //End: $(document).ready