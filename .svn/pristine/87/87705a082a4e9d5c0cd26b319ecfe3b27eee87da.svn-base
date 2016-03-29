
function DisplayAllSitesBox() {
	if ($('#allSiteDiv')[0].style.display == 'none') {
		$('#allSiteDiv')[0].style.display = '';
	}
}
function SetInputValue(id, str){
	$('#'+id).val(str);
	$('#allSiteDiv')[0].style.display='none';
}
function FillAllSites() { 
	var allSites = $.cookie('allSites');
	var boxhtml = '';
	if (allSites) {
		var allSitesA = allSites.split('|');
		var curSite;
		$("#url").val(allSitesA[0]);
		
		for (i = 0; i < allSitesA.length; i++) {
		    curSite = allSitesA[i];
		    if (allSitesA[i].length > 25){
		        curSite = allSitesA[i].substr(0,25);
		    }
		    boxhtml += '<li class=divUrlList onmouseover="JavaScript:{if(this.style.backgroundColor!=\'#ff0000\')this.style.backgroundColor=\'#eeeeee\'}" onmouseout="JavaScript:{if(this.style.backgroundColor!=\'#ff0000\')this.style.backgroundColor=\'#FFFF99\'}" ><input type="button" onclick="DelAllSitesItem(\''+allSitesA[i]+'\')" value="删 除" /><a href="javascript:void(0);" target="_self" onclick="SetInputValue(\'url\', \''+allSitesA[i]+'\')">'+curSite+'</a></li>';
		}
	}
	else{
		boxhtml += '<li class=divUrlList onmouseover="JavaScript:{if(this.style.backgroundColor!=\'#ff0000\')this.style.backgroundColor=\'#eeeeee\'}" onmouseout="JavaScript:{if(this.style.backgroundColor!=\'#ff0000\')this.style.backgroundColor=\'#FFFF99\'}" >没有记录</li>';
	}
	$("#allSiteDiv").html(boxhtml);
	
}
function DelAllSitesItem(value) {
	var newSites = [];
	var allSites = $.cookie('allSites');
	var allSitesA = allSites.split('|');
	for (i = 0; i < allSitesA.length; i++) {
		if (value != allSitesA[i]){
			newSites.push(allSitesA[i]);
		}
	}
	if (newSites.length > 0) {
		allSites = newSites.join('|');
	} else {
		allSites = null;
	}
	$.cookie('allSites', allSites, {expires: 365, path: '/'});
	FillAllSites();
}
function setSiteCookie(url) {
    var ipArray,ip,j; 
	ip = url; 
	if(/[\.0-9A-Za-z_-]/.test(ip)){ 
		if (ip.indexOf(" ")>=0){ 
			ip = ip.replace(/ /g,""); 
		} 

		if (ip.toLowerCase().indexOf("http://")==0){ 
			ip = ip.slice(7); 
		} 

		if(!/^([\w-]+\.)+((com)|(net)|(org)|(gov\.cn)|(info)|(cc)|(com\.cn)|(net\.cn)|(org\.cn)|(name)|(biz)|(tv)|(cn)|(mobi)|(name)|(sh)|(ac)|(io)|(tw)|(com\.tw)|(hk)|(com\.hk)|(ws)|(travel)|(us)|(tm)|(la)|(me\.uk)|(org\.uk)|(ltd\.uk)|(plc\.uk)|(in)|(eu)|(it)|(jp)|(ai)|(so))$/.test(ip)){ 
			//return false; 
		}
		
        var allSites = $.cookie('allSites');
		if (!allSites) {
			$.cookie('allSites', escape(ip), {expires: 365, path: '/'}); 
		} else {
			var newSites = [];
			var allSitesA = allSites.split('|');
			for (i = 0, j = 0; i < allSitesA.length; i++) {
				if (ip != allSitesA[i]){
					newSites.push(allSitesA[i]);
					if (j >= 3){
						break;
					}
					j++;
				}
			}
			if (newSites.length > 0) {
				allSites = escape(ip)+'|'+newSites.join('|');
			} else {
				allSites = escape(ip);
			}
			$.cookie('allSites', allSites, {expires: 365, path: '/'}); 
		}
	}
}
//document.getElementById("allSitesBox").onmouseout = function() {
//document.getElementById("allSitesBox").style.display='none';
//}
$(document).mousedown(
	function(event){
		if (event.target.id != 'url' && event.target.parentNode.className != 'divUrlList' && $('#allSiteDiv')[0].style.display != 'none') {
			$('#allSiteDiv')[0].style.display = 'none';
		}
	}
);
