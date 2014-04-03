$(document).ready(function(){
	/**
	 * 刷新页面时，根据保存的业务经历信息，初始化业务经历
	 */
	$(function(){
		if (resumeBizs != false) {
			var levels = {'A': '◎', 'B': '○', 'C': '★', 'D': '△'};
			var ages = {1: YEAR_1, 2: YEAR_2, 3: YEAR_3, 4: YEAR_4, 5: YEAR_5, 6: YEAR_6, 7: YEAR_7, 8: YEAR_8, 9: YEAR_9};
			$.each(resumeBizs, function(k, v) {
				//当业务为用户选择，或用户输入的业务名时，初始化业务名
				if (v['biz'].substr(0, 3) == 'os_' || v['biz'].substr(0, 3) == 'db_' 
					|| v['biz'].substr(0, 10) == 'framework_' || v['biz'].substr(0, 4) == 'biz_') {
					$('#' + v['biz']).val(v['biz_name']);
				}
				$('#' + v['biz'] + '_level').html(levels[v['level']]);
				$('#' + v['biz'] + '_age').html(ages[v['age']]);
			});
		}
	});

	
}); //End: $(document).ready





