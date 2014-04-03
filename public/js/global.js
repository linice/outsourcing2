$(document).ready(function(){
	/**
	 * 登陆：页头，通过点击登陆按钮
	 */
	$('#btn_login').click(function(){
		//置登陆按钮为不可用，登陆完成后，再启用
		var enabled = $(this).attr('class');
		if (enabled == 'ENABLED_Y') {
			login();
		}
		$(this).attr('class', 'ENABLED_Y');
	});
	
	
	/**
	 * 登陆：页头，通过在密码输入框按Enter键
	 */
	$('#login_passwd').keydown(function(event){
		if (event.which == 13) {
			login();
		}
	});
	
	
	/**
	 * 登陆：弹出框，通过点击登陆按钮
	 */
	$('#btn_dlg_login').click(function(){
		//置登陆按钮为不可用
		$('#btn_dlg_login').attr({'disabled': true});
		var login_email = $('#dlg_login_email').val();
		var login_passwd = $('#dlg_login_passwd').val();
		var isAutoLogin = '';
		if ($('#dlg_isAutoLogin').attr('checked') == 'checked') {
			isAutoLogin = 'Y';
		}
		var parm = {'login_email': login_email, 'login_passwd': login_passwd, 'isAutoLogin': isAutoLogin};
//		console.log(parm);
		
		//请求登陆
		$.ajax({
			url: '/login/login',
			type: 'post',
			data: parm,
			dataType: 'json', //xml, json, script or html
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					location.reload();
				} else {
					$('#tip_dlg_login').html(data['msg']);
					$('#btn_dlg_login').attr({'disabled': false});
				}
			}
		});
	});
	
	
	/**
	 * 确定无权访问
	 */
	$('#btn_dlg_cfm_no_priv').click(function() {
		$('#dlg_no_priv').dialog('close');
		history.go(-1);
	});
	
	
	/**
	 * 退出登录
	 */
	$('#btn_logout').click(function(){
		logout();
	});
	
	
	/**
	 * 为JQuery类添加成员函数
	 */
	$.fn.extend({
		showError : function(text, focus) {
			if (!!focus) $(this).focus();
			$(this).parent().next().removeClass("input_info_ok").addClass("input_info_error").text(text).show();
		},
		hideError : function(text) {
			$(this).parent().next().removeClass("input_info_error").hide();
		},
		showOk : function() {
			$(this).parent().next().removeClass("input_info_error").addClass("input_info_ok").text("").show();
		},
		showNull : function() {
			$(this).parent().next().removeClass("input_info_error").removeClass("input_info_ok").text("").show();
		}
	});
	

	/**
	 * 管理员退出
	 */
	$('#btn_admin_logout').click(function(){
		$(this).attr({'disabled': true});
		$.ajax({
			url: '/admin/login/logout',
			type: 'get',
			async: true,
			data: null,
			dataType: 'json', //xml, json, script or html
			success: function(data, textStatus, jqXHR) {
//				if (data['err'] == 0) {
					location.href='/admin/login';
//				} else {
//					alert(data['msg']);
//					$('#btn_logout').attr({'disabled': false});
//				}
			}
		});
	});
	
}); //End: $(document).ready

function ajaxVal(ob, url, data, fn) {
	$.ajax({
		url: url,
		type: 'post',
		async: false,
		dataType: 'json', //xml, json, script or html
		data: data,
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) 
				ob.showOk();
			else 
				ob.showError(data['msg']);
		}
	});
}

function valErr() {
	if ($("span.input_info_error:visible").length > 0)
		return false;
	return true;
}


/**
 * 登录
 */
function login() {
	//请求登陆
	$.ajax({
		url: '/login/login',
		type: 'post',
		data: $('#form_login').serializeArray(),
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


/**
 * 退出登录
 */
function logout() {
	$.ajax({
		url: '/login/logout',
		type: 'post',
		data: null,
		dataType: 'json', //xml, json, script or html
		success: function(data, textStatus, jqXHR) {
//			if (data['err'] == 0) {
				location.href='/?pwd=10';
//			} else {
//				alert(data['msg']);
//			}
		}
	});
}


/**
 * 设置区域语言：中文或日文
 * @param locale
 */
function setLang(locale) {
	$.ajax({
		url: '/index/setlang',
		type: 'get',
		data: {'locale': locale},
		dataType: 'json', //xml, json, script or html
		success: function(data, textStatus, jqXHR) {
//			if (data['err'] == 0) {
				location.reload();
//			} else {
//				alert(data['msg']);
//			}
		}
	});
}


/**
 * 取代浏览器默认的提示对话框
 * title: 默认为“提示”
 * type: success, error, warn, question, none 
 * 另外：confirm，warning，prompt，waitting有专门的ligerUI函数，函数名为：$.ligerDialog.type，其中type为前面confirm等值
 */
window.alert = function(content, title, type, callback) {
	if (arguments.length == 1) {
		title = PROMPT;
		type = 'none';
	} else if (arguments.length == 2) {
		type = 'none';
	}
	$.ligerDialog.alert(content, title, type, callback);
};


/**
 * dlg: confirm
 */
window.ligerConfirm = function(title, content, option, succFun, errFun) {
	option = !!option ? option : {};
	var succ = function(button, index, panel) {
		if (succFun) {
			succFun.call(this);
		}
		panel.close();
	};
	var err = function(button, index, panel) {
		if (!!errFun) {
			errFun.call(this);
		}
		panel.close();
	};
	$.ligerMessageBox.show({
		width: option.width,
		type: option.type,
		title: title,
		content: content,
		buttons: [{text: SURE, onclick: succ}, {text: CANCEL, onclick: err}]
	});
};


$("input[format]").live('keyup', function() {
	var formatter = $(this).attr("format");
	var value = $(this).val();
	if (formatter == 'int') {
		if (value.match(/\D/) != null) {
			$(this).val(value.replace(/\D/g, ''));
		}
	}
});

