/******************js工具函数**********************/
/**
 * 删除字符串前后的空格
 */
function trim(str){
	var regExp = /^\s*(.*?)\s*$/;
	return str.replace(regExp, "$1");
}


/**
 * 验证是否是手机号码
 * @param mobile string
 * @returns bool
 */
function isMobile(mobile)
{
	var pattern = /[\d\-\+]+/;
//	var mobile = "18676660185";
	return pattern.test(mobile);
}


/**
 * 验证是否是座机号码
 * @param tel string
 * @returns bool
 */
function isTel(tel)
{
//	var pattern = /^(\+?d{2})?[-_－—\s]?0?\d{2,4}[-_－—\s]?\d{7,8}([-_－—\s]\d{3,})?$/;
	var pattern = /[\d\-\+]+/;
//	str = "746 4831012 123";
	return pattern.test(tel);
}


/**
 * 验证是否是电子邮件地址
 * @param email string
 * @returns bool
 */
function isEmail(email)
{
	//摘自php和MySQL开发
	var pattern = /^[a-zA-Z0-9_\-\.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$/;
	return pattern.test(email);
}


/**
 * 计算满岁
 * @param birthday string, eg: 1970-01-01
 * @returns int
 */
function calcFullAge(birthday) {
	var birthday = new Date(birthday);
	var today = new Date();
	var fullAge = today.getFullYear() - birthday.getFullYear();
	
	birthday.setYear(2000);
	today.setYear(2000);
	
	if (birthday > today) {
		fullAge--;
	}
	return fullAge;
}


/**
 * 计算满月
 * @param dateBegin string, eg: 1970-01-01
 * @param dateEnd string, eg: 1970-03-01
 * @returns int
 */
function calcMonthCnt(dateBegin, dateEnd) {
	var dateBegin = new Date(dateBegin);
	var dateEnd = new Date(dateEnd);
	var monthCnt = 0;
	
	monthCnt = (dateEnd.getFullYear() - dateBegin.getFullYear()) * 12;
	monthCnt += dateEnd.getMonth() - dateBegin.getMonth();
	if (dateEnd.getDate() < dateBegin.getDate()) {
		monthCnt--;
	}
	return monthCnt;
}


/**
 * 日期字符串转换为日期，如果字符串格式不对则返回空
 * @param dateStr
 * @returns {Date}
 */
function parseDate(dateStr) {
	if(dateStr.match(/\d{4}-\d{2}-\d{2}/) == null) 
		return null;
	var a = dateStr.split("-");
	m = parseInt(a[1])-1;
	var nd = new Date(a[0],m,a[2]);
	return nd;
}


/**
 * 计算两个日期之间的相关天数
 * @param dateStr1
 * @param dateStr2
 * @returns
 */
function daysBetween(dateStr1, dateStr2, abs) {
	if (typeof dateStr1 == 'string') {
		dateStr1 = dateStr1.replace(/-/g, "/");
	} else if (!!dateStr1.getTime) {
		dateStr1 = dateStr1.getFullYear()+"/"+(dateStr1.getMonth()+1)+"/"+dateStr1.getDate();
	} else 
		return false;
	if (typeof dateStr2 == 'string') {
		dateStr2 = dateStr2.replace(/-/g, "/");
	} else if (!!dateStr2.getTime) {
		dateStr2 = dateStr2.getFullYear()+"/"+(dateStr2.getMonth()+1)+"/"+dateStr2.getDate();
	} else 
		return false;
	var l1 = Date.parse(dateStr1);
	var l2 = Date.parse(dateStr2);
	var l = l2-l1;
	return !!abs ? Math.abs(l/(1000*60*60*24)) : l/(1000*60*60*24);
}


/**
 * @param a number 
 * @param b number 
 * @returns {Number}
 */
function sortNumberAsc(a, b)
{
	return a - b;
}


/**
 * @param a number
 * @param b number
 * @returns {Number}
 */
function sortNumberDesc(a, b)
{
	return b - a;
}

function isMember() {
	return currUserRole == 'MEMBER';
}

function isLp() {
	return currUserRole == 'LP';
}

function isAdmin() {
	return currUserRole == 'ADMIN';
}


/**
 * 打印页面的指定部分，开始和结束标识分别为：<!--startprint-->, <!--endprint-->
 */
function doPrint() {
	bdHtml = window.document.body.innerHTML;
	printBegin = '<!--printBegin-->';
	printEnd = '<!--printEnd-->';
	printHtml = bdHtml.substr(bdHtml.indexOf(printBegin)+17);
	printHtml = printHtml.substring(0, printHtml.indexOf(printEnd));
	window.document.body.innerHTML = printHtml;
	window.print();
}

function adjustCaseDetail(rowData) {
	$("#detail").find("span[id]").each(function() {
		var spanid = $(this).attr("id");
		if ($(this).attr("format") == 'date' && !!rowData[spanid]) {
			$(this).text(rowData[$(this).attr("id")].substr(0,10));
		} else if (spanid == 'lp_code' || spanid == 'lp_name') {
			if (!!currUserCode && (currUserCode == rowData['lp_code'] || isAdmin())) {
				$(this).parent().show();
				$(this).text(rowData[spanid]);
			} else {
				$(this).parent().hide();
			}
		} else if (spanid == 'unitPriceValueView' || spanid == 'unitPriceValue') {
			$(this).addClass("fred").addClass("f_b");
			if (!!currUserCode && (currUserCode == rowData['lp_code'] || isAdmin())) {
				$(this).text(rowData["unitPriceValue"]);
			} else {
				$(this).text(rowData['unitPriceValueView']);
			}
		} else if (spanid == 'business_req' || spanid == 'technical_req') {
			//return $(this).html(rowData[spanid.replace("_r", "R")+"Value"])
			var v = rowData[spanid];
			$(this).html(escapeHtml(v));
		} else if (spanid == 'name' && !!rowData['akb'] && rowData['akb'] > 0) {
			$(this).html(rowData[spanid]+"<span class='fred f_b'>("+RECOMMEND+")</span>");
		} else {
			$(this).text(rowData[spanid]);
		}
	});
}

function UnitPriceFuntion(rowData, rowIndex) {
	if (!!currUserCode && (currUserCode == rowData['lp_code'] || isAdmin())) {
		return rowData["unitPriceValue"];
	} else {
		return rowData["unitPriceValueView"];
	}
}


function DateFormatFunction(rowData, col) {
	return !!rowData[col] ? rowData[col].substr(0, 10) : "";
}


function CaseCodeFormatFunction(rowData, col) {
	return "<a href=\"javascript:void(0)\" onclick=\"viewCase('"+rowData[col]+"')\">"+rowData[col]+"</a>";
}


function CaseNameFormatFunction(rowData, col, caseId) {
	return "<a href=\"javascript:void(0)\" onclick=\"viewCase('"+rowData[caseId]+"')\">"+rowData[col]+"</a>";
}


function UsrCodeFormatFunction(rowData, col) {
	if (isLp() || isAdmin()) {
		return "<a href=\"javascript:void(0)\" onclick=\"window.open('/usr_resume/previewresume/resumeCode/"+rowData[col]+"')\">"+rowData[col]+"</a>";
	} else {
		return rowData[col];
	}
}


function UsrNameFormatFunction(rowData, col, usrId) {
	if (isLp() || isAdmin()) {
		return "<a href=\"javascript:void(0)\" onclick=\"window.open('/usr_resume/previewresume/resumeCode/"+rowData[usrId]+"')\">"+rowData[col]+"</a>";
	} else {
		return rowData[col];
	}
}

/**
 * 打开列表的所有明细
 * @param gridManager	列表的ligerui manager
 * @param limit			列表的行数
 */
function expendDetailAll(gridManager, limit) {
	limit = limit || 20;
	for (var idx = 0; idx < limit; idx++) {
		gridManager.extendDetail(idx);
	}
}

/**
 * 收缩列表的所有明细
 * @param gridManager	列表的ligerui manager
 * @param limit			列表的行数
 */
function collapseDetailAll(gridManager, limit) {
	limit = limit || 20;
	for (var idx = 0; idx < limit; idx++) {
		gridManager.collapseDetail(idx);
	}
}

function escapeHtml(value) {
	var v = value.replace(/\r\n/g, "<br/>");
	v = v.replace(/\r/g, "<br/>");
	v = v.replace(/\n/g, "<br/>");
	return v;
}
