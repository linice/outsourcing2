var cancelAttentionImg = "/img/default/front/<?=$auth->locale?>/btn_dis_attention.jpg";
var attentionImg = "/img/default/front/<?=$auth->locale?>/btn_select_on.jpg";
var manager;
$(function() {
	var url = "/front_case/searchreleasecase";
	
	var grid = $("#caselist").ligerGrid({
		checkbox: true,
		columns: [{display: CASE_ID, name: 'code', width:62, render: CaseCodeFunction},
				  {display: CASE_NAME, name: 'name', render: CaseNameFunction},
				  {display: case_business_req, name: 'businessReqValue'},
				  {display: case_technical_req, name: 'technicalReqValue'},
				  {display: case_end_range, name: 'period', width:120},
				  {display: WORKPLACE, name: 'workingPlaceValue', width:103},
				  {display: PRICE_UNIT_MONTHLY, render: UnitPriceFuntion, width:100},
				  {display: case_timeliness, name: 'timeliness', width:72}/*,
				  //{display: '相关操作', render: opFunction, align: "center", width:140}*/],
		url: url,
		width: "100%",
		columnWidth: 145,
		isScroll: false, frozen:false,
		delayLoad: true,
		detail: {onShowDetail: showDetail, height: "auto"},
		pageSize: 20,
		//onLoaded : function(){}//,
//		onAfterShowData: function (grid) { 
//			if ($.fn.ligerCheckBox) 
//				$(".l-grid-body input:checkbox,.l-grid-hd-cell input:checkbox", grid).ligerCheckBox({ css: { marginTop: 3} }) 
//		} 
	});
	
	function CaseCodeFunction(rowData, rowIndex) {
		return CaseCodeFormatFunction(rowData, 'code');
	}
	function CaseNameFunction(rowData, rowIndex) {
		return CaseNameFormatFunction(rowData, 'name', 'code');
	}
	
	manager = $("#caselist").ligerGetGridManager();
	var searchFields = $("#searchFields").val();
	searchFields = eval('['+searchFields+']');
	function loadGrid() {
		if (!!searchFields) {
			manager.setOptions({parms: searchFields[0]})
		}
		manager.loadData();
	}
	loadGrid();
	
	function opFunction(rowData, rowIndex) {
		var h = "";
		if (!!currUserCode && currUserCode != rowData["lp_code"]) {
			if (!rowData["apply_id"] || currUserCode.indexOf('Lp') != -1) {
				h = h + '<a href="javascript:void(0)" onclick="applyCase(null,\''+rowData.code+'\',\''+rowData.name+'\')">应聘</a>';
				if (!!h) h += '|' ;
				if (!!rowData["attention_usr_code"]) {
					h = h + '<a href="javascript:void(0)" onclick="cancelCase(null,\''+rowData.code+'\',\''+rowData.name+'\')">取消关注</a>';
				} else {
					h = h + '<a href="javascript:void(0)" onclick="attentionCase(null,\''+rowData.code+'\',\''+rowData.name+'\')">关注</a>';
				}
			}  
			else {
				h = h + '<a href="javascript:void(0)" style="color:gray">应聘中</a>'
				if (!!h) h += '|' ;
				h = h + '<a href="javascript:void(0)" style="color:gray">关注</a>';
			}
		} else {
			h = h + '<a href="javascript:void(0)" style="color:gray">应聘</a>'
			if (!!h) h += '|' ;
			h = h + '<a href="javascript:void(0)" style="color:gray">关注</a>';
		}
		if (!!h) h += '|'
		return h + '<a href="javascript:void(0)" onclick="editCase('+rowData.id+', '+!!rowData["attention_usr_code"]+')">案件详情</a>';
	}
	
	function showDetail(rowData, detailPanel) {
		adjustCaseDetail(rowData)
		//如果未登陆且未关注该案件，则显示关注，否则显示取消关注，未登陆则不显示
		if (!!currUserCode && rowData["lp_code"] == currUserCode) {
			$("#detail").find("#attentionBtn").hide();
			$("#detail").find("#cancelAttentionBtn").hide();
			$("#detail").find("#applyBtn").hide();
		} else {
			$("#detail").find("#applyBtn").show();
			if(!rowData["attention_usr_code"]) {
				$("#detail").find("#attentionBtn").show();
				$("#detail").find("#cancelAttentionBtn").hide();
			} else {
				$("#detail").find("#cancelAttentionBtn").show();
				$("#detail").find("#attentionBtn").hide();
			}
		}
		$(detailPanel).append($("#detail").html());
		$(detailPanel).find("#viewDetail").click(function() {
			var btn = ["apply", !!rowData["attention_usr_code"]?"cancelAttention":"attention"]
			viewCase(rowData.code, btn)
		});
		$(detailPanel).find("#cancelAttentionBtn").click(function() {
			cancelAttentionCase([rowData], manager)
		});
		$(detailPanel).find("#attentionBtn").click(function() {
			attentionCase([rowData], manager);
		});
		$(detailPanel).find("#applyBtn").click(function() {
			if (rowData["lp_code"] != currUserCode) {
				applyCase(rowData);
			}
		});
	}

	//普通检索
	$('#btn_payAttention').click(function(){
		var rows = manager.getSelecteds();
		var f = true;
		$.each(rows, function(i, v) {
			var rowData = rows[i];
			if(!currUserCode || !rowData["attention_user_code"] || rowData["attention_user_code"]==currUserCode) {
				
			}
		})
		attentionCase(rows, manager);
		return false;
	});
	
	$("#backBtn").click(function() {
		$("#dynForm").attr("action", "/front_case/case");
		$("#dynForm").attr("target", "_self");
		$("#dynForm").submit();
	})
	
	$("#cases_expend").toggle(function() {
		$(this).removeClass("case_lie_list").addClass("case_lie");
		expendDetailAll(manager, manager.getData().length);
	}, function() {
		$(this).removeClass("case_lie").addClass("case_lie_list");
		collapseDetailAll(manager, manager.getData().length);
	})
});