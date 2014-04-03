$("link[href='/js/jquery/ligerUI/skins/Aqua/css/ligerui-all.css']").attr("disabled", true);
function setTab(m,n){
	var menu=document.getElementById("tab"+m).getElementsByTagName("li");  
	var div=document.getElementById("tablist"+m).getElementsByTagName("div");
	
	if (n == 0) {
		$("link[href='/js/jquery/ligerUI/skins/Aqua/css/ligerui-all.css']").attr("disabled", true);
	} else {
		$("link[href='/js/jquery/ligerUI/skins/Aqua/css/ligerui-all.css']").attr("disabled", false);
	}
	var showdiv=[];
	for (i=0; j=div[i]; i++){
	  if ((" "+div[i].className+" ").indexOf(" tablist ")!=-1){
	   showdiv.push(div[i]);
	  }
	}
	for(i=0;i<menu.length;i++)
	{
		menu[i].className=i==n?"now":"";
		showdiv[i].style.display=i==n?"block":"none";  
	}
}