$(document).ready(function(){
	/**
	 * 初始化用户注册信息
	 */
	$(function(){
		$('input[name="sex"][value="' + usr['sex'] + '"]').attr('checked', true);
	});
}); //End: $(document).ready


/**
 * 确认修改注册信息
 */
function saveMemberRegInfo() {
	$.ajax({
		url: '/usr_usr/modifyreginfo',
		type: 'get',
		async: false,
		dataType: 'json', //xml, json, script or html
		data: $('#form_memberRegInfo').serializeArray(),
		success: function(data, textStatus, jqXHR) {
			if (data['err'] == 0) {
				$('#tip_memberRegInfo').html(data['msg']);
				setTimeout("$('#tip_memberRegInfo').html('')", 3000);
			} else if (data['err'] == 1) {
				$('#tip_memberRegInfo').html(data['msg']);
			} else {
				var html = '';
				var msgs = eval(data['msg']);
				$.each(msgs, function(k, v) {
					html += v + '<br />';
				});
				$('#tip_memberRegInfo').html(html);
				
				var labels = eval(data['labels']);
				$.each(labels, function(k, v) {
					$('#' + v).css('color', 'red');
				});
			}
		}
	});
}


