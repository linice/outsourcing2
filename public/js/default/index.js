$(document).ready(function(){
	//获取页面公共参数
	var url = $(location).attr('href'); //页面url
	var title = $(document).attr('title'); //页面title
	var jiaThis_id = '1344388610943156';
	
	
	//隐藏页头用户登陆
	$('#div_login').hide();
	
	/**
	 * 分享到新浪微博 
	 */
	$('#shareTo_tsina').click(function(){
		window.open('http://www.jiathis.com/send/?webid=tsina&url=' + url + '&title=' + title + '&uid=' + jiaThis_id);
	});
	
	
	/**
	 * 分享到腾讯微博 
	 */
	$('#shareTo_tqq').click(function(){
		window.open('http://www.jiathis.com/send/?webid=tqq&url=' + url + '&title=' + title + '&uid=' + jiaThis_id);
	});
	
	
	/**
	 * 分享到twitter 
	 */
	$('#shareTo_twitter').click(function(){
		window.open('http://www.jiathis.com/send/?webid=twitter&url=' + url + '&title=' + title + '&uid=' + jiaThis_id);
	});
	
	
	/**
	 * 分享到facebook 
	 */
	$('#shareTo_fb').click(function(){
		window.open('http://www.jiathis.com/send/?webid=fb&url=' + url + '&title=' + title + '&uid=' + jiaThis_id);
	});
	
	
	/**
	 * 被推荐的人的email，失去焦点 
	 */
	$('#email_consignee').blur(function(){
		var val = trim($(this).val());
		if (val == '') {
			$(this).val(please_input_email);
		}
	});
	
	
	/**
	 * 被推荐的人的email，获得焦点 
	 */
	$('#email_consignee').focus(function(){
		var val = trim($(this).val());
		if (val == please_input_email) {
			$(this).val('');
		}
	});
	
	
	/**
	 * 发送邮件 
	 */
	$('#btn_sendEmail').click(function(){
		$.ajax({
			url: '/index/sendemail',
			type: 'get',
			async: true,
			dataType: 'json', //xml, json, script or html
			data: {'email': $('#email_consignee').val()},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) { //发送推荐信成功
					alert(data['msg']);
				} else { //发送推荐信失败
					alert(data['msg']);
				}
			}
		});
	});
	
	
	/**
	 * 用户登陆：点击登陆按钮
	 */
	$('#btn_login_homepage').click(function() {
		//置登陆按钮为不可用，登陆完成后，再启用
		var enabled = $(this).attr('class');
		if (enabled == 'ENABLED_Y') {
			$(this).attr('class', 'ENABLED_N');
			login_homepage();
			$(this).attr('class', 'ENABLED_Y');
		}
	});
	
	
	/**
	 * 用户登陆：密码框获得焦点时，按Enter键
	 */
	$('#login_passwd_homepage').keydown(function(event){
		if (event.which == 13) {
			login_homepage();
		}
	});
	
	
	
	
	/****************************function***********************/
	function login_homepage() {
		//请求登陆
		$.ajax({
			url: '/login/login',
			type: 'post',
			data: $('#form_login_homepage').serializeArray(),
			dataType: 'json', //xml, json, script or html
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					location.reload();
				} else {
					alert(data['msg'], PROMPT, 'error');
				}
			}
		});
	}
	
	$("input:checkbox[name='careersAll']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.careerslist").attr("checked", true);
		} else {
			$("input:checkbox.careerslist").attr("checked", false);
		}
	});
	
	$("input:checkbox[name='languagesAll']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.languageslist").attr("checked", true);
		} else {
			$("input:checkbox.languageslist").attr("checked", false);
		}
	});
	
	$("input:checkbox[name='industriesAll']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.industrieslist").attr("checked", true);
		} else {
			$("input:checkbox.industrieslist").attr("checked", false);
		}
	});
	
	$("input:checkbox[name='overseas']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.overlist").attr("checked", true);
		} else {
			$("input:checkbox.overlist").attr("checked", false);
		}
	});
	
	$("input:checkbox[name='japans']").click(function() {
		if ($(this).attr("checked")) {
			$("input:checkbox.japanlist").attr("checked", true);
		} else {
			$("input:checkbox.japanlist").attr("checked", false);
		}
	});

	$("input:checkbox.careerslist").click(function() {
		var checked = true;
		$.each($("input:checkbox.careerslist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='careersAll']").attr("checked", checked);
	})
	
	$("input:checkbox.languageslist").click(function() {
		var checked = true;
		$.each($("input:checkbox.languageslist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='languagesAll']").attr("checked", checked);
	})
	
	$("input:checkbox.industrieslist").click(function() {
		var checked = true;
		$.each($("input:checkbox.industrieslist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='industriesAll']").attr("checked", checked);
	})
	
	$("input:checkbox.overlist").click(function() {
		var checked = true;
		$.each($("input:checkbox.overlist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='overseas']").attr("checked", checked);
	});
	
	$("input:checkbox.japanlist").click(function() {
		var checked = true;
		$.each($("input:checkbox.japanlist"), function() {
			if (!$(this).attr("checked")) {
				checked = false;
				return false;
			}
		});
		$("input:checkbox[name='japans']").attr("checked", checked);
	});
	
	//高级检索
	$('#btn_search').click(function(){
		$("#dynForm").attr("action", "/front_case/casesearchresult");
		$("#dynForm").submit();
		return false;
	});
	
	
}); //End: $(document).ready