(function($) {
	$.fn.pagingDisplay = function(options) {
		if (typeof options == 'string') {
			return $.fn.pagingDisplay.method[options].apply(this, Array.prototype.slice.call(arguments,1))
		} else if (typeof options == 'number') {
			var p = $(this).data("option");
			p.page = options;
			$(this).pagingDisplay.method['reload'].call(this, p);
		} else {
			var p = $.extend({}, $.fn.pagingDisplay.default, options);
			var g = $(this);
			
			var param = $.extend({pagesize : p.pagesize, page : p.page}, p.param);
			
			g.data("option", p);
			if (p.url && p.reload) {
				$.ajax({
					url: p.url,
					type: 'post',
					async: false,
					dataType: 'json', //xml, json, script or html
					data: param,
					success: function(data, textStatus, jqXHR) {
						p.data = data['Rows'];
						p.total = data['Total'];
						g.pagingDisplay.method['initData'].call(this, data['Rows'], p, g);
					}
				});
			}
			return g;
		}
	}
	$.fn.pagingDisplay.method = {
		reload : function(opt) {
			var p = $.extend({}, $(this).data("option"), opt);
			var g = $(this);
			var _t = this;
			var param = $.extend({pagesize : p.pagesize, page : p.page}, p.param);
			if (p.url) {
				$.ajax({
					url: p.url,
					type: 'post',
					async: false,
					dataType: 'json', //xml, json, script or html
					data: param,
					success: function(data, textStatus, jqXHR) {
						p.data = data['Rows'];
						p.total = data['Total'];
						$.fn.pagingDisplay.method['initData'](data['Rows'], p, g);
					}
				});
			}
		}, 
		initData : function(data, p, g) {
			g.empty();
			$.each(data, function(i) {
				var li = $(p.item);
				var dli = p.render.call(g, data[i], p);
				li.append(dli);
				li.appendTo(g);
			});
			if (p.reloadPaging) {
				$.fn.pagingDisplay.method['initPaging'](p, g);
			}				
		},
		initPaging : function(p, g) {
			var paging = g.next('#Pagination');
			var page = p.page;
			var total = p.total;
			var pagesize = p.pagesize;
			var totalpage = Math.ceil(total/pagesize);
			var callbackFun = function(nextPage) {
				nextPage = nextPage+1;
				g.pagingDisplay(nextPage);
			}
			p.reloadPaging = false;
			paging.empty();
			paging.pagination(total, { 
				callback: callbackFun, 
				items_per_page: pagesize, //每页显示的条目数
				num_display_entries: 6, //连续分页主体部分显示的分页条目数
				current_page: page-1, //当前选中的页面
				num_edge_entries: 2, //两侧显示的首尾分页的条目数
				ellipse_text: "..."  //省略的页数用什么文字表示
			}); 
		}
	}
	$.fn.pagingDisplay.default = {
		url : "",
		param : {},
		pagesize : 20,
		page : 1,
		total : 0,
		item : "<li>",
		reload : true,
		reloadPaging : true,
		data : {}
	}
})(jQuery);