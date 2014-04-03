$(document).ready(function() {
	$("a[name='ackLp']").click(function() {
		var code = $(this).attr('id');
		var tr = $(this).parent().parent();
		$.ajax({
			url: '/admin/admin_lp/checklp',
			type: 'post',
			async: false,
			dataType: 'json', //xml, json, script or html
			data: {code: code},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					alert(data['msg']);
					tr.remove();
				} else {
					alert(data['msg']);
				}
			}
		});
	});
}); //End: $(document).ready