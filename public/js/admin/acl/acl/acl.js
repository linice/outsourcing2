var gResourcesClassName = false;


$(document).ready(function(){
	//页面打开时，初始化资源列表
	showResources(0, 0, 0);

	
	$('#resourceName').hover(function(){
		if ('请输入资源名称' == trim($(this).val())) {
			$(this).val('')
					.css({'color': 'black'});
		}
		$(this).focus();
	});
	
	
	$('#resourceName').blur(function(){
		if ('' == trim($(this).val())) {
			$(this).val('请输入资源名称')
					.css({'color': 'gray'});
		}
	});
	
	
	//选择模块
	$('#module').change(function(){
		var module = $(this).val();
		if (module == 'all') {
			//控制过滤条件的显示
			$('#controller option').css({'display': 'none'});
			$('#controllerAll').css({'display': 'block'});
			
			//控制资源列表的显示
			gResourcesClassName = false;
			showResources(0, 0, 0);
		} else {
			//控制过滤条件的显示
			$('#controller option').css({'display': 'none'});
			$('#controllerAll').css({'display': 'block'});
			$('#controller .'+module).css({'display': 'block'});
			
			//控制资源列表的显示
			gResourcesClassName = module;
			showResources(0, 0, module);
		}
	});
	
	
	//选择控制器
	$('#controller').change(function(){
		var module = $('#module').val();
		var controller = $(this).val();
		if (controller == 'all') {
			//控制过滤条件的显示
			$('#action option').css({'display': 'none'});
			$('#actionAll').css({'display': 'block'});
			
			//控制资源列表的显示
			gResourcesClassName = module;
			showResources(0, 0, module);
		} else {
			//控制过滤条件的显示
			$('#action option').css({'display': 'none'});
			$('#actionAll').css({'display': 'block'});
			$('#action .'+module+'.'+controller).css({'display': 'block'});
			
			//控制资源列表的显示
			gResourcesClassName = module+'.'+controller;
			showResources(0, 0, module+'.'+controller);
		}
	});
	
	
//	//选择方法（Action）
//	$('#action').change(function(){
//		var module = $('#module').val();
//		var controller = $('#controller').val();
//		var action = $(this).val();
//		if (action == 'all') {
//			//控制资源列表的显示
//			$('#div_resources ul').css({'display': 'none'});
//			$('#div_resources .'+module+'.'+controller).css({'display': 'block'});
//		} else {
//			//控制资源列表的显示
//			$('#div_resources ul').css({'display': 'none'});
//			$('#div_resources .'+module+'.'+controller+'.'+action).css({'display': 'block'});
//		}
//	});
	
	
	$('#searchResource').click(function(){
		var resourceName = $('#resourceName').val();
	});

}); //End: $(document).ready


function showResources(currentPage, pageSize, className)
{
	var maxEntries = 0;
	
	if (currentPage == false || currentPage < 0) {
		currentPage = 1;
	}
	
	if (pageSize == false || pageSize < 0) {
		pageSize = 2;
	}
	
	//先隐藏所有资源项，然后根据所过滤的条件，有选择的显示部分资源项
	$('#div_resources ul').css({'display': 'none'});
	if (className == false) {
		maxEntries = $('#div_resources ul').size();
		$('#div_resources ul').each(function(index){
			if (index >= (currentPage-1)*pageSize && index <  currentPage*pageSize)
				$(this).show();
		});
	} else {
		maxEntries = $('#div_resources .'+className).size();
		$('#div_resources .'+className).each(function(index){
			if (index >= (currentPage-1)*pageSize && index <  currentPage*pageSize)
				$(this).show();
		});
	}
	
	$("#div_pagination").pagination(maxEntries, {
		current_page: currentPage - 1,
		items_per_page: pageSize,
        callback: function(currentPage, jq){
        	showResources(currentPage + 1, pageSize, className);
    	}
    });
}


function changePageSize(obj)
{
	var pageSize = $(obj).val();
	if(isNaN(pageSize))
		return false;
	
	showResources(0, pageSize, gResourcesClassName);
}




