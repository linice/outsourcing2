var cancelAttentionImg = "/img/default/front/<?=$auth->locale?>/btn_dis_attention.jpg";
var attentionImg = "/img/default/front/<?=$auth->locale?>/btn_select_on.jpg";
var inmanager, endmanager;
$(function() {
	var inurl = "/front_case/searcheffectivecase";
	var endurl = "/front_case/searchhistorycase";
	
	$("#tb1").click(function() {
		if ($(this).parent().attr("class") == 'now') return false;
		$(this).parent().addClass("now");
		$(this).parent().next().removeClass("now");
		$("#tbl1").show();
		$("#tbl2").hide();
		if (!$("#caseinlist").text()) {
			loadTab1();
		}
	})
	$("#tb2").click(function() {
		if ($(this).parent().attr("class") == 'now') return false;
		$(this).parent().addClass("now");
		$(this).parent().prev().removeClass("now");
		$("#tbl2").show();
		$("#tbl1").hide();
		if (!$("#caseendlist").text()) {
			loadTab2();
			if (!!searchFields) {
				endmanager.setOptions({parms: searchFields[0]})
			}
			endmanager.loadData();
		}
	})
	
	function loadTab1() {
		$("#caseinlist").ligerGrid({
			checkbox: true,
			columns: [{display: CASE_ID, name: 'code', width:75, render: CaseCodeFunction},
					  {display: CASE_NAME, name: 'name', render: CaseNameFunction},
					  {display: case_business_req, name: 'businessReqValue'},
					  {display: case_technical_req, name: 'technicalReqValue'},
					  {display: case_end_range, name: 'period', width:100},
					  {display: WORKPLACE, name: 'workingPlaceValue', width:100},
					  {display: PRICE_UNIT_MONTHLY, render: UnitPriceFuntion, width:96},
					  {display: case_timeliness, name: 'timeliness', width:74}/*,
					  {display: '相关操作', render: opFunction, align: "center", width:190}*/],
			url: inurl,
			width: "100%",
			columnWidth: 149,
			isScroll: false, frozen:false,
			delayLoad: true, 
			detail: {onShowDetail: showDetail, height: "auto"},
			pageSize: 20
		});
		inmanager = $("#caseinlist").ligerGetGridManager();
	}
	
	function loadTab2() {
	    $("#caseendlist").ligerGrid({
			checkbox: true,
	        columns: [{display: CASE_ID, name: 'code', width:75, render: CaseCodeFunction},
					  {display: CASE_NAME, name: 'name', render: CaseNameFunction},
					  {display: case_business_req, name: 'businessReqValue'},
					  {display: case_technical_req, name: 'technicalReqValue'},
					  {display: case_end_range, name: 'period', width:100},
					  {display: WORKPLACE, name: 'workingPlaceValue', width:100},
					  {display: PRICE_UNIT_MONTHLY, render: UnitPriceFuntion, width:96},
					  {display: case_timeliness, name: 'timeliness', width:74}/*,
	                  {display: '相关操作', render: opFunction2, align: "center", width: 76}*/],
	        url: endurl,
	        width: "100%",
	        columnWidth: 149,
	        isScroll: false, frozen:false,
	        delayLoad: true, 
	        detail: {onShowDetail: showDetail, height: "auto"},
	        pageSize: 20
	    });
	    endmanager = $("#caseendlist").ligerGetGridManager();
	}
	
	function CaseNameFunction(rowData, rowIndex) {
		return CaseNameFormatFunction(rowData, 'name', 'code');
	}
	function CaseCodeFunction(rowData, rowIndex) {
		return CaseCodeFormatFunction(rowData, 'code');
	}
	
	var searchFields = $("#searchFields").val();
	searchFields = eval('['+searchFields+']');
	function loadGrid() {
		loadTab1();
		if (!!searchFields) {
			inmanager.setOptions({parms: searchFields[0]})
		}
		inmanager.loadData();
		inmanager = $("#caseinlist").ligerGetGridManager();
	}
	loadGrid();
	
	function opFunction(rowData, rowIndex) {
		var h = '<a href="javascript:void(0)" onclick="applyCase(null,\''+rowData.code+'\',\''+rowData.name+'\')">应聘</a>|'
		if (rowData["lp_code"] == currUserCode) {
			h = h + '<a href="javascript:void(0)" style="color:gray">关注</a>|';
		} else if (!!rowData["attention_usr_code"]) {
			h = h + '<a href="javascript:void(0)" onclick="cancelCase(null,\''+rowData.code+'\',\''+rowData.name+'\')">取消关注</a>|';
		} else {
			h = h + '<a href="javascript:void(0)" onclick="attentionCase(null,\''+rowData.code+'\',\''+rowData.name+'\')">关注</a>|';
		}
		h = h + '<a href="javascript:void(0)" onclick="editCase('+rowData.id+')">案件维护</a>|';
		return h + '<a href="javascript:void(0)" onclick="applyMgt('+rowData.id+')">应聘管理</a>';
	}
	
	function opFunction2(rowData, rowIndex) {
		if (rowData["lp_code"] == currUserCode) {
			h = h + '<a href="javascript:void(0)" style="color:gray">关注</a>|';
		} else if (!!rowData["attention_usr_code"]) {
			return '<a href="javascript:void(0)" onclick="cancelCase(null,\''+rowData.code+'\',\''+rowData.name+'\')">取消关注</a>';
		} else {
			return '<a href="javascript:void(0)" onclick="attentionCase(null,\''+rowData.code+'\',\''+rowData.name+'\')">关注</a>';
		}
	}
	
	function showDetail(rowData, detailPanel) {
		adjustCaseDetail(rowData);
		//如果已登陆且未关注该案件，则显示关注，否则显示取消关注，未登陆则不显示
		if(!rowData["attention_usr_code"]) {
			$("#detail").find("#attentionBtn").show();
			$("#detail").find("#cancelAttentionBtn").hide();
		} else {
			$("#detail").find("#cancelAttentionBtn").show();
			$("#detail").find("#attentionBtn").hide();
		}
		//如果已登陆且未应聘该案件，则可以应聘
		if (!isTab1()) {
			$("#detail").find("#applyManagerBtn").hide();
			$("#detail").find("#applyBtn").hide();
			$("#detail").find("#cancelAttentionBtn").hide();
			$("#detail").find("#attentionBtn").hide();
		} else {
			$("#detail").find("#applyManagerBtn").show();
			$("#detail").find("#applyBtn").show();
		}
		$(detailPanel).append($("#detail").html());
		$(detailPanel).find("#viewDetail").click(function() {
			var btn = [isTab1() ? "apply" : "", !!rowData["attention_usr_code"]?"cancelAttention":"attention"];
			viewCase(rowData.code, btn);
		});
		$(detailPanel).find("#applyManagerBtn").click(function() {
			applyManager(rowData.code);
		});
		$(detailPanel).find("#cancelAttentionBtn").click(function() {
			var manager = isTab1() ? inmanager : endmanager;
			cancelAttentionCase([rowData], manager)
		});
		$(detailPanel).find("#attentionBtn").click(function() {
			var manager = isTab1() ? inmanager : endmanager;
			attentionCase([rowData], manager)
		});
		$(detailPanel).find("#applyBtn").click(function() {
			applyCase(rowData);
		});
	}
	
	$('#attentionBtn1').click(function(){
		var rows = inmanager.getSelecteds();
		attentionCase(rows, inmanager);
		return false;
	});

	$('#attentionBtn2').click(function(){
		var rows = endmanager.getSelecteds();
		attentionCase(rows, endmanager);
		return false;
	});
	
	$("#backBtn").click(function() {
		$("#dynForm").attr("action", "/admin/admin_casesearch/casesearch");
		$("#dynForm").submit();
	})
	
	$("#cases_expend").toggle(function() {
		var manager = isTab1() ? inmanager : endmanager;
		$(this).removeClass("case_lie_list").addClass("case_lie");
		expendDetailAll(manager, manager.getData().length);
	}, function() {
		var manager = isTab1() ? inmanager : endmanager;
		$(this).removeClass("case_lie").addClass("case_lie_list");
		collapseDetailAll(manager, manager.getData().length);
	})
	
	function isTab1() {
		return $("#tbl2:hidden").length > 0 ? true : false;
	}
});
