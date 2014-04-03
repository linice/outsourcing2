/**
 * be included in: searchusr.phtml
 */
$(document).ready(function(){
	var today = new Date();
	var yearBegin = today.getFullYear();
	var yearEnd = yearBegin + 1;
	
	
	/**
	 * 初始化检索条件，来源：检索结果的“更改检索条件”
	 */
	$(function(){
		if (advancedSearchParms) {
			//人员种类
			$('input[name="talentType"][value="' + advancedSearchParms['talentType'] + '"]').attr('checked', true);
			
			//OS
			if (advancedSearchParms['oss']) {
				$.each(advancedSearchParms['oss'], function(k, v) {
					$('input[name="oss[]"][value="' + v + '"]').attr('checked', true);
				});
			}
			
			//经验语言/FrameWork
			$.each(fws, function(k, v) {
				if (advancedSearchParms[v + '_fw_age']) {
					$('#' + v + '_fw_age').val(advancedSearchParms[v + '_fw_age']);
				}
			});
			
			//业务领域
			$.each(bizs, function(k, v) {
				if (advancedSearchParms[v + '_biz_age']) {
					$('#' + v + '_biz_age').val(advancedSearchParms[v + '_biz_age']);
				}
			});
			
			//勤务地
			if (advancedSearchParms['expWps_jp']) {
				$.each(advancedSearchParms['expWps_jp'], function(k, v) {
					$('input[name="expWps_jp[]"][value="' + v + '"]').attr('checked', true);
				});
			}
			if (advancedSearchParms['expWps_os']) {
				$.each(advancedSearchParms['expWps_os'], function(k, v) {
					$('input[name="expWps_os[]"][value="' + v + '"]').attr('checked', true);
				});
			}
			
			//其它条件
			//性别
			$('input[name="sex"][value="' + advancedSearchParms['sex'] + '"]').attr('checked', true);
			//年龄
			$('#fullageMin').val(advancedSearchParms['fullageMin']);
			$('#fullageMax').val(advancedSearchParms['fullageMax']);
			//日本语
			$('#jaAbility').val(advancedSearchParms['jaAbility']);
			
			//稼动可能日
			$('input[name="ableWorkDateChoice"][value="' + advancedSearchParms['ableWorkDateChoice'] + '"]').attr('checked', true);
			$('#ableWorkDateBegin').val(advancedSearchParms['ableWorkDateBegin']);
			$('#ableWorkDateEnd').val(advancedSearchParms['ableWorkDateEnd']);
			
			//单价
			$('#salaryMin').val(advancedSearchParms['salaryMin']);
			
			//简历合格
			if (advancedSearchParms['rsmQfd']) {
				$.each(advancedSearchParms['rsmQfd'], function(k, v) {
					$('input[name="rsmQfd[]"][value="' + v + '"]').attr('checked', true);
				});
			}
		}
	});
	
	
	/**
	 * 可工作日期（开始）
	 */
	$('#ableWorkDateBegin').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
		minDate: today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate(),
		yearRange: yearBegin + ':' + yearEnd
	});
	
	
	/**
	 * 可工作日期（结束）
	 */
	$('#ableWorkDateEnd').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: 'yy-mm-dd',
		minDate: today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate(),
		yearRange: yearBegin + ':' + yearEnd
	});
	
	
	/**
	 * 勤务地——日本，全选或全不选
	 */
	$('#expWps_jp_all').click(function(){
		if ($(this).attr('checked') == 'checked') {
			$('input[name="expWps_jp[]"]').attr('checked', true);
		} else {
			$('input[name="expWps_jp[]"]').attr('checked', false);
		}
	});
	
	
	/**
	 * 勤务地——海外，全选或全不选
	 */
	$('#expWps_os_all').click(function(){
		if ($(this).attr('checked') == 'checked') {
			$('input[name="expWps_os[]"]').attr('checked', true);
		} else {
			$('input[name="expWps_os[]"]').attr('checked', false);
		}
	});
	
	
	/**
	 * 检索人才：简单
	 */
	$('#btn_simple_search').click(function() {
		simpleSearch();
	});
	
	
	/**
	 * 检索人才：高级
	 */
	$('#btn_advancedSearch').click(function() {
		$('#form_advancedSearch').submit();
	});
}); //End: $(document).ready


/**
 * 检索人才：简单
 */
function simpleSearch() {
	var searchKey = $('#searchKey').val();
	var searchVal = $('#searchVal').val();
	
	window.open('/admin/admin_usr/searchusrresult/searchType/SIMPLE/searchKey/' + searchKey + '/searchVal/' + searchVal, '_blank');
}






