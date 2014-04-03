$(document).ready(function(){
	$('#btn_genSql').click(function(){
		var tableName = $('#tableName').val();
		var fieldNames = $('#fieldNames').val();
		$.ajax({
			url: '/admin/sys/gentableinssql',
			type: 'get',
			dataType: 'json', //xml, json, script or html
			data: {'tableName': tableName, 'fieldNames': fieldNames},
			success: function(data, textStatus, jqXHR) {
				if (data['err'] == 0) {
					$('#ta_sql').html(data['data']);
					$('#tip_genSql').html('');
				} else {
					$('#tip_genSql').html(data['msg']);
				}
			}
		});
	});
	
	
	
	
}); //End: $(document).ready