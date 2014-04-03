$(document).ready(function(){
	var today = new Date();
	var yearEnd = today.getFullYear();
	var yearBegin = yearEnd - 100;
	
	
	/**
	 * 出身年月
	 */
	$('#birthday').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
//		minDate: '1970-01-01',
		yearRange: yearBegin + ':' + yearEnd
	});
	
	
	/**
	 * 初始化注册信息，用于注册确认返回
	 */
	$(function() {
		//如果是注册确认页面返回
		if (registerInfo) {
			//取oss, bizs, fws中，最多项目的数量
			var ossLength = (typeof registerInfo['oss'] == 'undefined')? 0 : registerInfo['oss'].length;
			var bizsLength = (typeof registerInfo['bizs'] == 'undefined')? 0 : registerInfo['bizs'].length;
			var fwsLength = (typeof registerInfo['fws'] == 'undefined')? 0 : registerInfo['fws'].length;
			var maxExperiencesCnt = Math.max(ossLength, bizsLength, bizsLength);
			if (maxExperiencesCnt) {
				for(var i = 0; i < maxExperiencesCnt; i++) {
					addExperience();
				}
			} else {
				for(var i = 0; i < 3; i++) {
					addExperience();
				}
			}
			$.each(registerInfo, function(k, v) {
				if (k == 'sex') {
					$(':radio[name="sex"][value="' + v + '"]').attr('checked', true);
				} else if (k == 'isReceiveNews') {
					$(':checkbox[name="isReceiveNews"][value="' + v + '"]').attr('checked', true);
				} else if (k == 'oss' || k == 'bizs' || k == 'fws') {
					var j = 0;
					$.each(registerInfo[k], function(k2, v2) {
						$('select[name="' + k + '[]"]:eq(' + j + ')').val(v2);
						j++;
					});
					j = 0;
					$.each(registerInfo[k + 'Years'], function(k3, v3) {
						$('select[name="' + k + 'Years[]"]:eq(' + j + ')').val(v3);
						j++;
					});
				} else {
					$('#' + k).val(v);
				}
			});
		} else { //否则，刚进入注册页面
			for (var i = 0; i < 3; i++) {
				addExperience();
			}
		}
		
		//如果推荐人信息为空，则隐藏推荐人一行
		var refereeEmail = $.trim($('#refereeEmail').val());
		if (!refereeEmail) {
			$('#tr_referee').hide();
		}
	});
	
	
	/**
	 * 根据现住国家的变化，列出省或县
	 */
	$('#country').change(function(){
		var html = '';
		if ($(this).val() == 'China') {
			$.each(provinces, function(k, v) {
				html += '<option value="' + k + '">' + v + '</option>';
			});
		} else {
			$.each(counties, function(k, v) {
				html += '<option value="' + k + '">' + v + '</option>';
			});
		}
		$('#province').html(html);
	});
	
	
	/**
	 * 增加技能项
	 */
	$('#btn_addExperience').click(function(){
		addExperience();
	});
	
	
	/**
	 * 用户注册
	 */
	$('#btn_register').click(function(){
		$.ajax({
			url: '/front_register/registerverify',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: $('#form_register').serializeArray(),
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) { //用户注册信息通过验证
					$('#registerInfo').val(data['jsRegInfo']);
					$('#c_form_register').submit();
				} else { //用户注册信息不合法
					alert(data['msg']);
				}
			}
		});
	}); //End: $('#btn_register').click
	
	
	
	
	
}); //End: $(document).ready



/**
 * 
 */
function addExperience() {
	$('#experiences').append($('#tr_experience').html());
}
