$(function() {
	function init() {
		$("#companyName").blur(function() {
			if(!$(this).val()) {
				$(this).showError(err["companyName_can_not_be_empty"]);
			} else {
				$(this).hideError();
			}
		});
		
		$("#linkman").blur(function() {
			if(!$(this).val()) {
				$(this).showError(err["linkman_can_not_be_empty"]);
			} else {
				$(this).hideError();
			}
		});
		
		$("#telephone").blur(function() {
			var value = $(this).val();
			if(!value) {
				$(this).showError(err["telephone_can_not_be_empty"]);
			} else if (!isTel(value)) {
				$(this).showError(err["telephone_style_error"]);
			} else {
				$(this).hideError();
			}
		});
		
		//添加邮箱验证
		$("#email").blur(function() {
			if (!$(this).val()) 
				$(this).showError(err["email_can_not_be_empty"]);
		});
		$("#email").change(function() {
			var value = $(this).val();
			if(!!value) {
				if (!isEmail(value))
					$(this).showError(err["email_is_not_real"]);
				else 
					ajaxVal($("#email"), '/front_lpreg/valemail', {email: value});
			}
		});
		
		$("#passwd").blur(function() {
			var passwd = $(this).val();
			if (!passwd) {
				$(this).showError(err["password_can_not_be_empty"]);
			} else if (passwd.length < 6 || passwd.length > 18) {
				$(this).showError(err["password_style_error"]);
			} else {
				$(this).showOk();
				if (passwd != $("#passwd2").val()) 
					$("#passwd2").showError(err["passwords_are_not_equal"]);
			}
		});
		
		$("#passwd2").blur(function() {
			var passwd2 = $(this).val();
			if (!passwd2 || passwd2 != $("#passwd").val()) 
				$("#passwd2").showError(err["passwords_are_not_equal"]);
			else 
				$(this).showOk();
		});
		
		//用户注册
		$('#btn_register').click(function(){
			if (valErr()) {
				$.ajax({
					url: '/front_lpreg/registerverify',
					type: 'post',
					async: false,
					dataType: 'json',
					data: $('#form_register').serializeArray(),
					success: function(data, textStatus, jqXHR) {
						if (data['err'] == 0) {
							$('#registerInfo').val(data['registerInfo']);
							$('#c_form_register').submit();
						} else {
							if (!!data["errField"] && $("#"+data["errField"]).length > 0) {
								$("#"+data["errField"]).focus();
								$("#"+data["errField"]).showError(data['msg']);
							} else {
								alert(data['msg']);
							}
						}
					}
				});
			}
		});
	};
	
	function initConfirmInfo() {
		if (!!regConfirmInfo && !$.isEmptyObject(regConfirmInfo))
		for (var p in regConfirmInfo) {
			if (p != 'passwd' && p != 'passwd2')
				$("#"+p).val(regConfirmInfo[p]);
		}
	}
	init();
	initConfirmInfo();
});