/* Code for AJAX online check */

/* common function begin */
function product_bg(ob){
    ob.className="onowiterm";
}
function product_bg_out(ob){
	ob.className="";
} 
//输入框状态
function inputAutoClear(ipt)
{
	ipt.onfocus=function()
	{if(this.value==this.defaultValue){this.value='';}};
	 ipt.onblur=function()
	{if(this.value==''){this.value=this.defaultValue;}};
	 ipt.onfocus();
} 
function ChangIterm(n) {
    for(var i=1;i<=2;i++){
        var curC=document.getElementById("tab_"+i);
        var curB=document.getElementById("tab_t"+i);
        if(n==i){
            curC.style.display="block";
            curB.className="thisiterm"
        }else{
            curC.style.display="none";
            curB.className=""
        }
    } 
}

//只能输入数字
function clearNoNum(obj){
//先把非数字的都替换掉，除了数字
obj.value = obj.value.replace(/[^\d]/g,"");
if(parseInt(obj.value)>parseInt(30)) obj.value = 4;
}

//只能输入数字
function clearNoNum2(obj){
//先把非数字的都替换掉，除了数字
obj.value = obj.value.replace(/[^\d]/g,"");
if(parseInt(obj.value)>parseInt(1024)) obj.value = 32;
}

function clearNoNum3(obj){
//先把非数字的都替换掉，除了数字
obj.value = obj.value.replace(/[^\d]/g,"");
if(parseInt(obj.value)>parseInt(15)) obj.value = 5;
}

function gotoImg(){
    window.open("http://pagespeed.webkaka.com/youhua/image/");
}
function gotoCss(){
    window.open("http://pagespeed.webkaka.com/youhua/css/");
}
function gotoJs(){
    window.open("http://pagespeed.webkaka.com/youhua/js/");
}

//* common function end */

var ChartXmlPath = ""; 
//var strCheckHost = "SHENZHEN;LESHAN;JINAN;SHANGHAI;BEIJING;TAIZHOU;HAERBIN;CHANGSHA;SHENYANG;KUNMING;XIAN;XIAMEN";     
var strCheckHost = "shijiazhuangdx;dongguandx;bjdianxin;luoyangdx;shanghaidx;chongqingdx;dgdx2;ahdxebadu;";
strCheckHost = strCheckHost + "hengyangdx;hangzhoudx;scmydx;beijingdx;jiangxidx;quanzhoudx;zhenjiang2dx;lishuidx;baojidx;foshandx;guangxidx;guizhoussf;zhenjiangdx;shenzhendx;qingdaodx;sccddx;guizhou360jq;jxnc360jq;jinhuasx;";
strCheckHost = strCheckHost + "qhdlt;beijinglt;hrbwt;weifangwt;hebeilt;shanxilt;bjtopservers;baojilt;tjlt;beijingsx;xiamendx;fujiansx;fujianyd;hnzhengzhougd;zzbgp;changshabgp;bjdxt;tjbgp;bjdx1000;hbyd";  

var strUrlCheckHost = "ushe;hk1000;japan;hanguo;tw;xianggang;usec;tai1wan;uk;pccw;hkcnkuai;hk360jq;usraksmart;hkraksmart;hk3389;korea1949;uswx19;uswx18;hk6w;hkwinman;usssf";

var strCheckCount = 20; //检测次数60次 ,每次1.5秒  

//var cur_val = 0;
function showActivity(cur_val) {
     if(cur_val >= 0) {
          cur_val += 6;
          update_progress("progress", 50, 100, cur_val);
          processbar_evt = setTimeout("showActivity("+cur_val+");", 150);
     }
}
            
function update_progress(id, w_bar, w_total, cur) {
	var cur1 = cur % ( 2 * w_total+2*w_bar );
	var cur1 = cur % (w_total+w_bar );

	if( cur1 > w_total + w_bar) 
		cur1 = (2*w_total + 2*w_bar) - cur1;

	var cur_w = Math.max(0, cur1 - w_bar);
	var cur_p = Math.min(w_total - cur_w, cur1 - cur_w);

	getitem(document,id).style.width = '' + cur_p + '%';
	getitem(document,id+"_white").style.width = '' + cur_w + '%';

}

function getItem(name) {
	return document.getElementById ? 
	document.getElementById(name) : 
	(document.all ? top.document.all[name] : null); 
}
function getitem(doc,name) {
      return doc.getElementById ? 
        doc.getElementById(name) : 
        (document.all ? top.document.all[name] : null); 
}
function ajaxInit(Code){
    if(Code=="0"){
         document.getElementById("tblbar").style.display ='none';
    }
    else{
       btn_OnClick();
    }
}

var stopChecking = false; //强行终止
function ajaxStart(Code,UID){
    var iCount;
    iCount = Code + 1;
    if(stopChecking){
        iCount = strCheckCount;
    }
    document.getElementById('countdown').innerHTML = "<font color =blue><b>("+ (strCheckCount - iCount - (-1)) +")<b></font>";
    
    var url = document.getElementById('url').value;
    var useragent = document.getElementById('UserAgent').value;
    WebCheck.GetWebResponse(strCheckCount,iCount,url,UID,useragent,GetWebResponse_CallBack);
    if(iCount < strCheckCount || iCount == strCheckCount )
         ajax_evt = setTimeout("ajaxStart("+iCount+",'"+UID+"')",3000);
    
}

function GetWebResponse_CallBack(response){
    if (response.error != null){
        //alert(response.error);
        return;
    }
    var str = response.value;

    var arrStr = str.split("$$");
    if(str.indexOf("Error:") > 0 && str.indexOf("Error:") < 3)
    {
        if(document.getElementById('url').disabled){
            alert(str);
            document.getElementById("tblbar").style.display ='none';
            ajaxStop();
            document.getElementById('btnChk').disabled=false;
            document.getElementById('url').disabled=false;
            document.getElementById('UserAgent').disabled=false;
            return;
        }
    }
    if(str.indexOf("URLOK") > 0)
    {
        //var arrStr = str.split("||");
        //document.getElementById("URLCHECKED").innerHTML=arrStr[0];
        //document.getElementById("URLCHECKED").title=arrStr[4];
        //document.getElementById("URLIPADDRESS").innerHTML=arrStr[2];
        //document.getElementById("URLIPADDRESS").title=arrStr[6];
    }
    if(str.indexOf("completed")>0){
        if(document.getElementById('btnChk').disabled==true){

            document.getElementById("tblbar").style.display ='none';
            ajaxStop();
            document.getElementById('btnChk').disabled=false;
            document.getElementById('url').disabled=false;
            document.getElementById('UserAgent').disabled=false;
        
            var arrStr = str.split("$$");
            document.getElementById('rdChart01').checked = true;
            document.getElementById('rdChart01').onclick = function(){showChartResult(arrStr[1])};  
            document.getElementById('rdChart02').onclick = function(){showChartTime(arrStr[1])};  
            document.getElementById('rdChart03').onclick = function(){showChartSpeed(arrStr[1])}; 
            document.getElementById('rdChart04').onclick = function(){showChartLine(arrStr[1])}; 
            //document.getElementById('rdChart01').checked = true;
            document.getElementById('rdChart01').onclick();
            
            getData(arrStr[0]);

            //updateFlashParams("flashMap",arrStr[2]);
            document.getElementById("downloadTime").innerHTML=arrStr[3];
            document.getElementById("SCORE").innerHTML=arrStr[4];
            document.getElementById("Gzip").innerHTML=arrStr[5];
        }
    }
    else{
        if(arrStr.length==5){
            //alert(str);
            getData(arrStr[0]);
            //updateFlashParams("flashMap",arrStr[1]);
        }
    }
    /*
    //document.getElementById("lblResult").innerHTML=str;
    var arrStr = str.split("$$");
    alert(str);
    if(arrStr.length==4){
       //alert(str);
       getData(arrStr[0]);
       //updateFlashParams("flashMap",arrStr[1]);
    }
    else if(arrStr.length==4){
       //alert(str);
       getData(arrStr[0]);
       //updateFlashParams("flashMap",arrStr[2]);
    }
    */
}


function getData(str)
{
    var arrStr = str.split("$");
    for(var i=1;i < arrStr.length;i++)
    {
        //var strId = "SHENZHEN;SHANGHAI;LESHAN;JINAN;BEIJING;HANGZHOU;HAERBIN;CHANGSHA;SHENYANG;WUHAN;KUNMING;XIAN;XIAMEN";
        var strId = strCheckHost;
        var arrId = strId.split(";");
        for(var j = 0;j < arrId.length; j++)
        {
            if(arrStr[i].indexOf(arrId[j]) == 0)
            {
                var arrData = arrStr[i].split("|");
                
                document.getElementById(arrId[j]+'_STATUS').innerHTML = arrData[1];
                document.getElementById(arrId[j]+'_TIME').innerHTML = arrData[2];
                document.getElementById(arrId[j]+'_SPEED').innerHTML = arrData[3];
                document.getElementById(arrId[j]+'_IP').innerHTML = arrData[5];
                document.getElementById(arrId[j]+'_NSTIME').innerHTML = arrData[6];
                document.getElementById(arrId[j]+'_CONNECTTIME').innerHTML = arrData[7];
                document.getElementById(arrId[j]+'_MORE_HEADER').innerHTML = arrData[10];
                document.getElementById(arrId[j]+'_MORE_IP').innerHTML = arrData[9];
                document.getElementById(arrId[j]+'_MORE_STATUS').innerHTML = arrData[1];
                
                document.getElementById(arrId[j]+'_MORE_NSTIME').innerHTML = arrData[6];
                document.getElementById(arrId[j]+'_MORE_CONNECTTIME').innerHTML = arrData[7];
                document.getElementById(arrId[j]+'_MORE_TIME').innerHTML = arrData[2];
                
                var t1 = arrData[6];
                var t2 = arrData[7];
                var t3 = arrData[2];
 
                var ttlTime = t1 - (-t2) - (-t3);
                var ttlWidth = 250;
                
                if(t1 != "N/A" && t2 != "N/A" && t3 != "N/A" && ttlTime.toString() != "0"){
                    var bWidth = ttlWidth*t1/ttlTime - (-1);
                    //document.getElementById(arrId[j]+'_MORE_NSTIME').innerHTML = "<span style='float:left;width:"+bWidth+"px;background-image:url(img/nav_line.gif)'>" + t1;
                    document.getElementById(arrId[j]+'_MORE_NSTIME').innerHTML = "<table style='BORDER:0px;'><tr><td style='BORDER:0px;'><span style='float:left;height:12px;width:"+bWidth+"px;background-color:#009966;background-image:url(img/nav_line.gif)'></td><td style='BORDER:0px;padding-top:2px'>" + t1 + "</td></tr></table>";
 
                    var cWidth = ttlWidth*t2/ttlTime - (-1);
                    //document.getElementById(arrId[j]+'_MORE_CONNECTTIME').innerHTML = "<span style='float:left;width:"+cWidth+"px;background-image:url(img/nav_line.gif)'>" + t2;
                    document.getElementById(arrId[j]+'_MORE_CONNECTTIME').innerHTML = "<table style='BORDER:0px;'><tr><td style='BORDER:0px;'><span style='float:left;height:12px;width:"+cWidth+"px;background-color:#009966;background-image:url(img/nav_line.gif)'></td><td style='BORDER:0px;padding-top:2px'>" + t2 + "</td></tr></table>";
 
                    var dWidth = ttlWidth*t3/ttlTime - (-1);
                    //document.getElementById(arrId[j]+'_MORE_TIME').innerHTML = "<span style='float:left;width:"+dWidth+"px;background-image:url(img/nav_line.gif)'>" + t3;
                    document.getElementById(arrId[j]+'_MORE_TIME').innerHTML = "<table style='BORDER:0px;'><tr><td style='BORDER:0px;'><span style='float:left;height:12px;width:"+dWidth+"px;background-color:#009966;background-image:url(img/nav_line.gif)'></td><td style='BORDER:0px;padding-top:2px'>" + t3 + "</td></tr></table>";
                }
                
                document.getElementById(arrId[j]+'_MORE_SIZE').innerHTML = arrData[8];
                document.getElementById(arrId[j]+'_MORE_SPEED').innerHTML = arrData[3];
                if(arrData[1] != "200" && arrData[1] != "N/A" && arrData[1] != "301" && arrData[1] != "302")
                {
                    document.getElementById(arrId[j]+'_R').className='ResultTableTr';
                    document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia3.png";
                }
                else if(arrData[1] == "N/A")
                {
                    document.getElementById(arrId[j]+'_R').className='CheckTableTr';
                    document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia4.png";
                }
                else if(arrData[1] == "200" || arrData[1] == "301" || arrData[1] == "302" || arrData[1] == "403")
                {
                    if(j%2==0)
                    {
                        document.getElementById(arrId[j]+'_R').className='';
                        document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia.png";
                        }
                    else
                    {
                        document.getElementById(arrId[j]+'_R').className='hoverTableTrOdd';
                        document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia2.png";
                    }
                    document.getElementById("URLPAGESIZE").innerHTML=arrData[4];
                }
                else
                {
                    document.getElementById("URLPAGESIZE").innerHTML=arrData[4];
                }
            }
        }
    }
}
function resetData()
{
    
        //var strId = "SHENZHEN;SHANGHAI;LESHAN;JINAN;BEIJING;HANGZHOU;HAERBIN;CHANGSHA;SHENYANG;WUHAN;KUNMING;XIAN;XIAMEN";
        var strId = strCheckHost;
        var arrId = strId.split(";");
        for(var j = 0;j < arrId.length; j++)
        {
            
                document.getElementById(arrId[j]+'_STATUS').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_TIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_SPEED').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_IP').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_NSTIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_CONNECTTIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_HEADER').innerHTML = "null";
                document.getElementById(arrId[j]+'_MORE_IP').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_STATUS').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_NSTIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_CONNECTTIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_TIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_SIZE').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_SPEED').innerHTML = "N/A";
                
                //document.getElementById(arrId[j]+'_R').style.backgroundColor='#F0DAF2';
                document.getElementById(arrId[j]+'_MORE').style.display='none';
                if(j%2==0)
                {
                    document.getElementById(arrId[j]+'_R').className='';
                    document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia.png";
                }
                else
                {
                    document.getElementById(arrId[j]+'_R').className='hoverTableTrOdd';
                    document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia2.png";
                }
                
        }
    
}
            
function ajaxStop(){
    if (ajax_evt) clearTimeout(ajax_evt);
    if (processbar_evt) clearTimeout(processbar_evt);
}
            
function S4() {   
    return (((1+Math.random())*0x10000)|0).toString(16).substring(1);   
}   
 
function NewGuid() {   
    return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());   
} 
          
function btn_OnClick(){
    //alert(unescape('%u7EF4%u62A4%u4E2D%uFF0C%u591A%u8C22%u5149%u4E34%uFF01'));
    //return false;
    document.getElementById('btnChk').disabled=true;
    document.getElementById('url').disabled=true;
    document.getElementById('UserAgent').disabled=true;
    showChartResult("Charts/");
    resetData(); //把表数据清空
    document.getElementById('rdChart01').onclick = function(){showChartResult('Charts/')};  
    document.getElementById('rdChart02').onclick = function(){showChartTime('Charts/')};  
    document.getElementById('rdChart03').onclick = function(){showChartSpeed('Charts/')}; 
    document.getElementById('rdChart04').onclick = function(){showChartSpeed('Charts/')}; 
    document.getElementById('rdChart01').checked = true;
    document.getElementById('rdChart01').onclick();
    stopChecking=false; //强行终止设为false
    ajaxStart(0,NewGuid());
    showActivity(0);
    document.getElementById("tblbar").style.display ='';

    document.getElementById("URLPAGESIZE").innerHTML=unescape('%u7F51%u9875%u5927%u5C0F%28Byte%29%uFF1AN/A');
    document.getElementById("downloadTime").innerHTML=unescape('%u5E73%u5747%u6253%u5F00%u65F6%u95F4%28s%29%uFF1AN/A');
    document.getElementById("SCORE").innerHTML=unescape('%3Cdiv%20style%3D%22margin-left%3A5px%3Bwidth%3A91%25%20%21important%3Bwidth%3A100%25%3Bheight%3A100%25%3Bbackground-color%3A%23FFFFFF%3Bbackground-image%3Aurl%28images/star_5.jpg%29%3Bbackground-repeat%3A%20no-repeat%3Bbackground-position%3A%20center%2017px%3Bcolor%3A%23000000%3Bpadding-top%3A0px%3B%22%20%3E%3Ca%20href%3D%22http%3A//www.webkaka.com/blog/archives/website-speed-and-analysis.html%22%20target%3D%22_blank%22%20%3E%3Cimg%20src%3D%22images/icon_wenhao.jpg%22%20onmouseover%3D%22document.getElementById%28%27divtipScore%27%29.style.display%3D%27%27%22%20onmouseout%3D%22document.getElementById%28%27divtipScore%27%29.style.display%3D%27none%27%22%20style%3D%22float%3Aright%3Bmargin-right%3A5px%3B%22/%3E%3C/a%3E%u6EE1%u610F%u5EA6%u5F97%u5206%uFF1AN/A%3C/div%3E%0D%0A%3Cdiv%20id%3D%22divtipScore%22%20style%3D%27margin-left%3A0px%20%21important%3Bmargin-left%3A-80px%3Bpadding%3A3px%203px%203px%203px%3Bborder%3A2px%20%23a1a1a1%20solid%3Bbackground-color%3A%23cccccc%3Bdisplay%3Ablock%3Bwidth%3A180px%3Bheight%3A20px%3Bposition%3Aabsolute%3Bmargin-top%3A-63px%3Bfont-weight%3Anormal%3Bcolor%3A%23333333%3Bz-index%3A888%3Bdisplay%3Anone%27%20%3E%0D%0A%20%20%20%20%20%u70B9%u51FB%u67E5%u770B%u6EE1%u610F%u5EA6%u5F97%u5206%u8BA1%u7B97%u516C%u5F0F%0D%0A%3C/div%3E');
    document.getElementById("Gzip").innerHTML=unescape('GZip%u538B%u7F29%uFF1Anull');   
    
    var szUrl = document.getElementById('url').value;
    setSiteCookie(szUrl);
    
    //var strId = "SHENZHEN;SHANGHAI;LESHAN;JINAN;BEIJING;HANGZHOU;HAERBIN;CHANGSHA;SHENYANG;WUHAN;KUNMING;XIAN;XIAMEN";
    var strId = strCheckHost;
    var arrId = strId.split(";");
    for(var j = 0;j < arrId.length; j++)
    {
       document.getElementById(arrId[j]+'_R').style.backgroundColor='#FFFFFF';
    }
} 
    
function updateFlashParams (flashID, params)
{
	var flashObj = getFlashObject(flashID);
	try
	{
		var chunks = params.split("$");
		for (var i=0; i<chunks.length; i++)
		{
			values = chunks[i].split("=");
			if (values.length == 2)
			{
				values[1] = unescape(values[1]).replace("+"," ");
				flashObj.SetVariable(values[0].toLowerCase(),"\("+values[1]+"\)");
			}
		}
	}
	catch (e)
	{
	}
}
	  
function getFlashObject (flashID)
{
	if (window.document[flashID])
		return window.document[flashID];
	else if (document.embeds && document.embeds[flashID])
		return document.embeds[flashID];
	else
		return document.getElementById(flashID);
}

function UrlCheck()
{
   var szUrl = document.getElementById('url').value;
   if(szUrl=="")
   {
       window.location = "http://www.webkaka.com/UrlCheck.aspx?url=http://www.baidu.com";
   }
   else
   {
       window.location = "http://www.webkaka.com/UrlCheck.aspx?url="+szUrl;
   }
}

function showChartResult(ChartPath){
	var chart = new FusionCharts("speed/flashViewer.swf?flag=3", "ChartId", "650", "250", "0", "0");
	chart.setDataURL(ChartPath+"Column2D_Result.xml?id="+NewGuid());		   
	chart.render("div_chart");
} 
function showChartTime(ChartPath){
	var chart = new FusionCharts("speed/flashViewer.swf?flag=1", "ChartId", "650", "250", "0", "0");
	chart.setDataURL(ChartPath+"ScrollCombi2DDY_TllLines.xml?id="+NewGuid());		   
	chart.render("div_chart");
} 
function showChartSpeed(ChartPath){
	var chart = new FusionCharts("speed/flashViewer.swf?flag=1", "ChartId", "650", "250", "0", "0");
	chart.setDataURL(ChartPath+"ScrollCombi2DDY_TllSpeed.xml?id="+NewGuid());		   
	chart.render("div_chart");
} 
function showChartLine(ChartPath){
	var chart = new FusionCharts("speed/flashViewer.swf?flag=3", "ChartId", "650", "250", "0", "0");
	chart.setDataURL(ChartPath+"Column2D_Line.xml?id="+NewGuid());		   
	chart.render("div_chart");
}     
function doPing(){
    var szUrl = document.getElementById('url').value;
    if(szUrl=="")
    {
        window.location = "Ping.aspx?url=http://www.baidu.com";
    }
    else
    {
        window.location = "Ping.aspx?url="+szUrl;
    }
}   
function doTrace(){
    var szUrl = document.getElementById('url').value;
    if(szUrl=="")
    {
        window.location = "Tracert.aspx?url=http://www.baidu.com";
    }
    else
    {
        window.location = "Tracert.aspx?url="+szUrl;
    }
}   
function GetQueryString(url)      
{      
     var reg = new RegExp("(^|&)"+     url     +"=([^&]*)(&|$)");      
     var r = window.location.search.substr(1).match(reg);      
     if (r!=null) return unescape(r[2]); 
     return null;
}
function chkPing(szUrl,szLoc)
{ 
    if(szUrl==null) return false;
    document.getElementById('url').value = szUrl;
    if(szLoc==null) szLoc="zj";
    var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
    for(var i=0; i<inputs.length; i++){
       if (inputs[i].type == "checkbox"){
           inputs[i].checked = false;
       }
    }
    document.getElementById('chkbox_'+szLoc).checked = true;

    document.getElementById('url').select();
    
    //btn_PingOnClick(); //允许带参数访问
}
function chkTrace(szUrl,szLoc)
{
    if(szUrl==null) return false;
    document.getElementById('url').value = szUrl;
    if(szLoc==null) szLoc="hb";
    var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
    for(var i=0; i<inputs.length; i++){
       if (inputs[i].type == "checkbox"){
           inputs[i].checked = false;
       }
    }
    document.getElementById('chkbox_'+szLoc).checked = true;

    document.getElementById('url').select();
    
    //btn_TracertOnClick(); //允许带参数访问
}
function chkWebcheck(szUrl)
{
    if(szUrl==null){ 
    document.getElementById("tblbar").style.display ='none';
    return false;
    }
    document.getElementById('url').value = szUrl;
    document.getElementById('url').select();
    document.getElementById("tblbar").style.display ='none';

    //btn_OnClick(); //允许带参数访问
}
function chkUrlcheck(szUrl)
{
    if(szUrl==null){
       document.getElementById("tblbar").style.display ='none';
       return false;
    }
    document.getElementById('url').value = szUrl;
    document.getElementById('url').select();
    document.getElementById("tblbar").style.display ='none';

    //btn_Click(); //允许带参数访问
}

//------------------------------------------------------------ js for WebCheck.aspx end

//------------------------------------------------------------ js for UrlCheck.aspx begin

function ajaxInitial(Code){
    if(Code=="0"){
         document.getElementById("tblbar").style.display ='none';
         //UrlCheck.GetUrlResponse(Code,"http://www.baidu.com","737d2733-3967-4b13-8b91-8b9bf7ac48a3",GetUrlResponse_CallBack);
    }
    else{
        document.getElementById('btnChk').disabled=true;
        document.getElementById('url').disabled=true;
        ajaxStarts(0,NewGuid());
        showActivity(0);
        document.getElementById('tblbar').style.display ='';
    }
}


function btn_Click(){
   
    document.getElementById('btnChk').disabled=true;
    document.getElementById('url').disabled=true;
    showCheckResult('Charts/'); //清空Chart图
    resetDatas(); //把表数据清空
    stopChecking=false; //强行终止设为false
    ajaxStarts(0,NewGuid());
    showActivity(0);
    document.getElementById("tblbar").style.display ='';
    
    document.getElementById("URLPAGESIZE").innerHTML=unescape('%u7F51%u9875%u5927%u5C0F%28Byte%29%uFF1AN/A');
    document.getElementById("downloadTime").innerHTML=unescape('%u5E73%u5747%u4E0B%u8F7D%u65F6%u95F4%28s%29%uFF1AN/A');
    document.getElementById("SCORE").innerHTML=unescape('%3Cdiv%20style%3D%22margin-left%3A10px%3Bwidth%3A91%25%20%21important%3Bwidth%3A100%25%3Bheight%3A100%25%3Bbackground-color%3A%23FFFFFF%3Bbackground-image%3Aurl%28images/star_0.jpg%29%3Bbackground-repeat%3A%20no-repeat%3Bbackground-position%3A%20center%2017px%3Bcolor%3A%23000000%3Bpadding-top%3A0px%3B%22%20%3E%3Ca%20href%3D%22http%3A//www.webkaka.com/blog/archives/website-speed-and-analysis.html%22%20target%3D%22_blank%22%20%3E%3Cimg%20src%3D%22images/icon_wenhao.jpg%22%20style%3D%22float%3Aright%3Bmargin-right%3A5px%3B%22/%3E%3C/a%3E%u6EE1%u610F%u5EA6%u5F97%u5206%uFF1AN/A%3C/div%3E');
    
    var szUrl = document.getElementById('url').value;
    setSiteCookie(szUrl);
      
    var strId = strUrlCheckHost;
    var arrId = strId.split(";");
    for(var j = 0;j < arrId.length; j++)
    {
       document.getElementById(arrId[j]+'_R').style.backgroundColor='#FFFFFF';
    }
    
} 
function showCheckResult(ChartPath){
	var chart = new FusionCharts("speed/flashViewer.swf?flag=3", "ChartId", "630", "224", "0", "0");
	chart.setDataURL(ChartPath+"Column2D_Result.xml?id="+NewGuid());		   
	chart.render("div_chart");
} 
function resetDatas()
{
    
        //var strId = "SHENZHEN;SHANGHAI;LESHAN;JINAN;BEIJING;HANGZHOU;HAERBIN;CHANGSHA;SHENYANG;WUHAN;KUNMING;XIAN;XIAMEN";
        var strId = strUrlCheckHost;
        var arrId = strId.split(";");
        for(var j = 0;j < arrId.length; j++)
        {
            
                document.getElementById(arrId[j]+'_STATUS').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_TIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_SPEED').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_IP').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_NSTIME').innerHTML = "N/A";
                //document.getElementById(arrId[j]+'_CONNECTTIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_HEADER').innerHTML = "null";
                document.getElementById(arrId[j]+'_MORE_IP').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_STATUS').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_NSTIME').innerHTML = "N/A";
                //document.getElementById(arrId[j]+'_MORE_CONNECTTIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_TIME').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_SIZE').innerHTML = "N/A";
                document.getElementById(arrId[j]+'_MORE_SPEED').innerHTML = "N/A";
                
                //document.getElementById(arrId[j]+'_R').style.backgroundColor='#F0DAF2';
                document.getElementById(arrId[j]+'_MORE').style.display='none';
                if(j%2==0)
                {
                    document.getElementById(arrId[j]+'_R').className='';
                    document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia.png";
                }
                else
                {
                    document.getElementById(arrId[j]+'_R').className='hoverTableTrOdd';
                    document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia2.png";
                }
                
        }
    
} 
function ajaxStarts(Code,UID){
    var iCount;
    iCount = Code + 1;
    if(stopChecking){
        iCount = strCheckCount;
    }
    document.getElementById('countdown').innerHTML = "<font color =blue><b>("+ (strCheckCount - iCount - (-1)) +")<b></font>";

    var url = document.getElementById('url').value;
    UrlCheck.GetUrlResponse(strCheckCount,iCount,url,UID,GetUrlResponse_CallBack);
    if(iCount < strCheckCount || iCount == strCheckCount )
         ajax_evt = setTimeout("ajaxStarts("+iCount+",'"+UID+"')",2500);
    
}

function GetUrlResponse_CallBack(response){
    if (response.error != null){
        //alert(response.error);
        return;
    }
    var str = response.value;
    //alert(str);
    var arrStr = str.split("$$");
    //alert(arrStr.length);
    if(str.indexOf("Error:") > 0 && str.indexOf("Error:") < 3)
    {
        if(document.getElementById('url').disabled){
            alert(str);
            document.getElementById("tblbar").style.display ='none';
            ajaxStop();
            document.getElementById('btnChk').disabled=false;
            document.getElementById('url').disabled=false;
            return;
        }
    }
    if(str.indexOf("URLOK") > 0)
    {
        //var arrStr = str.split("||");
        //document.getElementById("URLCHECKED").innerHTML=arrStr[0];
        //document.getElementById("URLCHECKED").title=arrStr[4];
        //document.getElementById("URLIPADDRESS").innerHTML=arrStr[2];
        //document.getElementById("URLIPADDRESS").title=arrStr[6];
    }
    
    if(str.indexOf("completed")>0){
        if(document.getElementById('btnChk').disabled==true){

            document.getElementById("tblbar").style.display ='none';
            ajaxStop();
            document.getElementById('btnChk').disabled=false;
            document.getElementById('url').disabled=false;
        
        
            var arrStr = str.split("$$");
            showCheckResult(arrStr[1]);

            getDatas(arrStr[0]);
            //updateFlashParams("flashMap",arrStr[2]);
            document.getElementById("downloadTime").innerHTML=arrStr[2];
            document.getElementById("SCORE").innerHTML=arrStr[3];
        }
    }
    else{
        if(arrStr.length==3){
            //alert(str);
            getDatas(arrStr[0]);
            //updateFlashParams("flashMap",arrStr[1]);
            
        }
    }
    /*
    //document.getElementById("lblResult").innerHTML=str;
    var arrStr = str.split("$$");
    //alert(str);
    if(arrStr.length==2){
       //alert(str);
       getData(arrStr[0]);
       updateFlashParams("flashMap",arrStr[1]);
    }
    else if(arrStr.length==3){
       //alert(str);
       getData(arrStr[0]);
       updateFlashParams("flashMap",arrStr[2]);
    }
    */
}

function getDatas(str)
{
    
    var arrStr = str.split("$");
    for(var i=1;i < arrStr.length;i++)
    {
        //var strId = "SHENZHEN;SHANGHAI;LESHAN;JINAN;BEIJING;HANGZHOU;HAERBIN;CHANGSHA;SHENYANG;WUHAN;KUNMING;XIAN;XIAMEN";
        var strId = strUrlCheckHost;
        var arrId = strId.split(";");
        for(var j = 0;j < arrId.length; j++)
        {
            if(arrStr[i].indexOf(arrId[j]) == 0)
            {
                var arrData = arrStr[i].split("|");
                
                document.getElementById(arrId[j]+'_STATUS').innerHTML = arrData[1];
                document.getElementById(arrId[j]+'_TIME').innerHTML = arrData[2];
                document.getElementById(arrId[j]+'_SPEED').innerHTML = arrData[3];
                document.getElementById(arrId[j]+'_IP').innerHTML = arrData[5];
                document.getElementById(arrId[j]+'_NSTIME').innerHTML = arrData[6];
                //document.getElementById(arrId[j]+'_CONNECTTIME').innerHTML = arrData[7];
                document.getElementById(arrId[j]+'_MORE_HEADER').innerHTML = arrData[10];
                document.getElementById(arrId[j]+'_MORE_IP').innerHTML = arrData[9];
                document.getElementById(arrId[j]+'_MORE_STATUS').innerHTML = arrData[1];
                
                document.getElementById(arrId[j]+'_MORE_NSTIME').innerHTML = arrData[6];
                //document.getElementById(arrId[j]+'_MORE_CONNECTTIME').innerHTML = arrData[7];
                document.getElementById(arrId[j]+'_MORE_TIME').innerHTML = arrData[2];
                
                var t1 = arrData[6];
                var t2 = arrData[7];
                var t3 = arrData[2];
 
                var ttlTime = t1 - (-t2) - (-t3);
                var ttlWidth = 250;
                
                if(t1 != "N/A" && t2 != "N/A" && t3 != "N/A" && ttlTime.toString() != "0"){
                    var bWidth = ttlWidth*t1/ttlTime - (-1);
                    document.getElementById(arrId[j]+'_MORE_NSTIME').innerHTML = "<table style='BORDER:0px;'><tr><td style='BORDER:0px;'><span style='float:left;height:12px;width:"+bWidth+"px;background-color:#009966;background-image:url(img/nav_line.gif)'></td><td style='BORDER:0px;padding-top:2px'>" + t1 + "</td></tr></table>";
 
                    //var cWidth = ttlWidth*t2/ttlTime - (-1);
                    //document.getElementById(arrId[j]+'_MORE_CONNECTTIME').innerHTML = "<table style='BORDER:0px;'><tr><td style='BORDER:0px;'><span style='float:left;height:12px;width:"+cWidth+"px;background-color:#009966;background-image:url(img/nav_line.gif)'></td><td style='BORDER:0px;padding-top:2px'>" + t2 + "</td></tr></table>";
 
                    var dWidth = ttlWidth*t3/ttlTime - (-1);
                    document.getElementById(arrId[j]+'_MORE_TIME').innerHTML = "<table style='BORDER:0px;'><tr><td style='BORDER:0px;'><span style='float:left;height:12px;width:"+dWidth+"px;background-color:#009966;background-image:url(img/nav_line.gif)'></td><td style='BORDER:0px;padding-top:2px'>" + t3 + "</td></tr></table>";
                }
                
                document.getElementById(arrId[j]+'_MORE_SIZE').innerHTML = arrData[8];
                document.getElementById(arrId[j]+'_MORE_SPEED').innerHTML = arrData[3];
                if(arrData[1] != "200" && arrData[1] != "N/A" && arrData[1] != "301" && arrData[1] != "302")
                {
                    document.getElementById(arrId[j]+'_R').className='ResultTableTr';
                    document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia3.png";
                }
                else if(arrData[1] == "N/A")
                {
                    document.getElementById(arrId[j]+'_R').className='CheckTableTr';
                    document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia4.png";
                }
                else if(arrData[1] == "200" || arrData[1] == "301" || arrData[1] == "302" || arrData[1] == "403")
                {
                    if(j%2==0)
                    {
                        document.getElementById(arrId[j]+'_R').className='';
                        document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia.png";
                        }
                    else
                    {
                        document.getElementById(arrId[j]+'_R').className='hoverTableTrOdd';
                        document.getElementById(arrId[j]+'_ICON').src = "images/icon_jia2.png";
                    }
                    document.getElementById("URLPAGESIZE").innerHTML=arrData[4];
                }
                else
                {
                    document.getElementById("URLPAGESIZE").innerHTML=arrData[4];
                }
            }
        }
    }
    
}


//------------------------------------------------------------ js for UrlCheck.aspx end


//------------------------------------------------------------  js for tracert.aspx begin
var strCheckPoint = "";
function btn_TracertOnClick(){
    //alert(unescape('%u7EF4%u62A4%u4E2D%uFF0C%u591A%u8C22%u5149%u4E34%uFF01'));
    //return false;
    var szUrl = document.getElementById('url').value;
    if(szUrl=="") szUrl = " ";
    document.getElementById('btnChk').disabled=true;
    document.getElementById('url').disabled=true;
    document.getElementById("div_itil").style.display="none";
    document.getElementById("div_process").style.display="";
    document.getElementById("lblResult").innerHTML="";
    document.getElementById('div_tracertInfo_chart').innerHTML="";
    
    //var strCheckUrl = CheckUrl();
    strCheckPoint = CheckUrl();
    
    //document.getElementById("tblStat").style.display="none";
    setSiteCookie(szUrl);
    ajaxTracertStart(0,szUrl,NewGuid());
} 
function ajaxTracertStart(Code,Url,GUID){
    var iCount;
    
    iCount = Code + 1;
    Tracert.GetTraceResponse(iCount,Url,GUID,strCheckPoint,GetTraceResponse_CallBack);
    if(iCount < 90 )
         ajax_evt = setTimeout("ajaxTracertStart("+iCount+",'"+Url+"','"+GUID+"')",3000);
         
}

function GetTraceResponse_CallBack(response){
   
    if (response.error != null){
        //alert(response.error);
        return;
    }
    var str = response.value;
    //alert(str);
    
    if(str.length>0||str.indexOf("Error")>0){
       document.getElementById("div_process").style.display="none";
    }
       
    if(str.indexOf("Error")>0){
        
        ajaxTracertStop();
        document.getElementById('btnChk').disabled=false;
        document.getElementById('url').disabled=false;
    }
    
    //var arrStr = str.indexOf("all Trace completed");
    if(str.indexOf("all Trace completed") > 0){
       
	   ajaxTracertStop();
       document.getElementById('btnChk').disabled=false;
       document.getElementById('url').disabled=false;
       document.getElementById("lblResult").innerHTML=str;
       
       var statHTML = str.split("||statTable||");
       //alert(statHTML[1]);
       //var chunks = str.split("TimeParas");
       //var timeParas = chunks[1]+"$"+chunks[3]+"$"+chunks[5]+"$"+chunks[7]+"$"+chunks[9]+"$"+chunks[11]+"$"+chunks[13]+"$"+chunks[15]+"$"+chunks[17];
	   //var strChart = "<iframe frameborder='0' scrolling='no' width='100%' height='380px' src='drawing.aspx?timeParas="+timeParas+"' name='webchart'></iframe>";	
       if(document.getElementById('div_tracertInfo_chart').innerHTML==""){
	       //document.getElementById('div_tracertInfo_chart').innerHTML = strChart;
	       document.getElementById('div_tracertInfo_chart').innerHTML =statHTML[1];
	       
	   }
	   
	   
	   

    }
    if(document.getElementById('div_tracertInfo_chart').innerHTML==""){
        document.getElementById("lblResult").innerHTML=str;
    }
    
    
}   

function ajaxTracertStop(){
    if (ajax_evt) clearTimeout(ajax_evt);
}

function onSelectChanged(){
    var flag = 0;
    var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
    for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox"){
            if(inputs[i].checked){
                flag = flag + 1;
            }
        }
    }

    if(flag == 5){
        for(var i=0; i<inputs.length; i++){
            if (inputs[i].type == "checkbox" && !inputs[i].checked){
                 inputs[i].disabled = true;
            }
        }
    }
    
    if(flag < 5){
        for(var i=0; i<inputs.length; i++){
            if (inputs[i].type == "checkbox"){
                 inputs[i].disabled = false;
            }
        }
    }
}

function CheckUrl(){
    var chkUrl = "";
    var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
    for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox"){
            if(inputs[i].checked){
               if(chkUrl == "")
                 chkUrl = inputs[i].value;
               else
                 chkUrl = chkUrl + "|" + inputs[i].value;
            }
        }
    }
    
    return chkUrl;
}
function stopPoint(sPoint){
    var chkUrl = "";
    var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
    for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox"){
            if(inputs[i].checked && inputs[i].value != sPoint){
               if(chkUrl == "")
                 chkUrl = inputs[i].value;
               else
                 chkUrl = chkUrl + "|" + inputs[i].value;
            }
        }
    }
    strCheckPoint = chkUrl;
}
//------------------------------------------------------------  js for tracert.aspx end

//------------------------------------------------------------  js for ping.aspx begin
function btn_PingOnClick(){
    //alert(unescape('%u7EF4%u62A4%u4E2D%uFF0C%u591A%u8C22%u5149%u4E34%uFF01'));
    //return false;
    var szUrl = document.getElementById('url').value;
    if(szUrl=="") szUrl = " ";
    var szNum = document.getElementById('Num').value;
    if(szNum=="") szNum = 4;
    if(parseInt(szNum)>parseInt(30)) szNum = 4;
    var szBytes = document.getElementById('Bytes').value;
    if(szBytes=="") szBytes = 32;
    if(parseInt(szBytes)>parseInt(1024)) szBytes = 32;
    var szTimeOut = document.getElementById('TimeOut').value;
    if(szTimeOut=="") szTimeOut = 5;
    if(parseInt(szTimeOut)>parseInt(15)) szTimeOut = 15;
    document.getElementById('btnChk').disabled=true;
    document.getElementById('url').disabled=true;
    document.getElementById("div_itil").style.display="none";
    document.getElementById("div_process").style.display="";
    document.getElementById("lblResult").innerHTML="";
    document.getElementById('div_tracertInfo_more').innerHTML="";
    document.getElementById('tblChart').style.display='none';
    var strCheckUrl = CheckUrl(szNum);
    
    //document.getElementById("tblStat").style.display="none";
    setSiteCookie(szUrl);
    ajaxPingStart(0,szUrl,szNum,szBytes,szTimeOut,NewGuid(),strCheckUrl);
} 
function ajaxPingStart(Code,Url,Num,Bytes,TimeOut,GUID,ChkUrl){
    var iCount;
    iCount = Code + 1;
    ping.GetPingResponse(iCount,Url,Num,Bytes,TimeOut,GUID,ChkUrl,GetPingResponse_CallBack);
    
    if(parseInt(iCount) < parseInt(4)){
         ajax_evt = setTimeout("ajaxPingStart("+iCount+",'"+Url+"',"+Num+","+Bytes+","+TimeOut+",'"+GUID+"','"+ChkUrl+"')",1000);
    }
    else if(parseInt(iCount) < parseInt(8)){
         ajax_evt = setTimeout("ajaxPingStart("+iCount+",'"+Url+"',"+Num+","+Bytes+","+TimeOut+",'"+GUID+"','"+ChkUrl+"')",1000);
    }
    else if(parseInt(iCount) < parseInt(14)){
         ajax_evt = setTimeout("ajaxPingStart("+iCount+",'"+Url+"',"+Num+","+Bytes+","+TimeOut+",'"+GUID+"','"+ChkUrl+"')",1000);
    }
    else if(parseInt(iCount) < parseInt(100)){
         ajax_evt = setTimeout("ajaxPingStart("+iCount+",'"+Url+"',"+Num+","+Bytes+","+TimeOut+",'"+GUID+"','"+ChkUrl+"')",1000);
    }
}

function GetPingResponse_CallBack(response){
   
    if (response.error != null){
        //alert(response.error);
        return;
    }
    var str = response.value;
    
    //alert(str);

    if(str.length>0||str.indexOf("Error")>0){
       document.getElementById("div_process").style.display="none";
    }
       
    if(str.indexOf("Error")>0){
        
        ajaxPingStop();
        document.getElementById('btnChk').disabled=false;
        document.getElementById('url').disabled=false;
    }
    
    var statHTML = str.split("||statTable||");
    if(statHTML.length > 1){
        
        document.getElementById("lblResult").innerHTML=statHTML[1];
        document.getElementById('div_tracertInfo_more').innerHTML =str;
	    
    }
    //if(document.getElementById('div_tracertInfo_chart').innerHTML==""){
        //document.getElementById("lblResult").innerHTML=str;
    //}
    //var arrStr = str.indexOf("all Trace completed");
    if(str.indexOf("all Ping completed") > 0){
       
	   ajaxPingStop();
       document.getElementById('btnChk').disabled=false;
       document.getElementById('url').disabled=false;
       
       if(document.getElementById('tblChart').style.display=='none'){
          document.getElementById("hidChartPath").innerHTML = statHTML[statHTML.length-2];
          document.getElementById('tblChart').style.display='';
          document.getElementById('rdChart01').onclick();
          document.getElementById('rdChart01').checked = true;
       }
       //document.getElementById("lblResult").innerHTML=str;
       
       //var statHTML = str.split("||statTable||");
       //alert(statHTML[1]);
       //var chunks = str.split("TimeParas");
       //var timeParas = chunks[1]+"$"+chunks[3]+"$"+chunks[5]+"$"+chunks[7]+"$"+chunks[9]+"$"+chunks[11]+"$"+chunks[13]+"$"+chunks[15]+"$"+chunks[17];
	   //var strChart = "<iframe frameborder='0' scrolling='no' width='100%' height='380px' src='drawing.aspx?timeParas="+timeParas+"' name='webchart'></iframe>";	
       //if(document.getElementById('div_tracertInfo_chart').innerHTML==""){
	       //document.getElementById('div_tracertInfo_chart').innerHTML = strChart;
	       //document.getElementById('div_tracertInfo_chart').innerHTML =statHTML[1];
	       //document.getElementById("lblResult").innerHTML=str;
	   //}
    }
    
    
    
    
}    

function ajaxPingStop(){
    if (ajax_evt) clearTimeout(ajax_evt);
}

function onSelectedChanged(){
    return;
}

function CheckUrl(n){
    var limit = 999;
    if(parseInt(n)>parseInt(10) && parseInt(n)<parseInt(21)) limit = 5;
    if(parseInt(n)>parseInt(20)) limit = 3;
    var chkUrl = "";
    var j = 0;
    var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
    for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox"){
            if(inputs[i].value != "dianxin" && inputs[i].value != "liantong" && inputs[i].value != "qita" && inputs[i].value != "quanxuan"){
              if(inputs[i].checked){
               if(chkUrl == ""){
                 chkUrl = inputs[i].value;
                 j = j + 1;
               }
               else{
                 if(parseInt(j) < parseInt(limit)){
                    chkUrl = chkUrl + "|" + inputs[i].value;
                    j = j + 1;
                 }
               }
              }
            }
        }
    }
    
    return chkUrl;
}
function chkallPing(chk)
{
    if(chk==true)
    {
       var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
       for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox"){
            inputs[i].checked = true
            }
       }
    }
    else
    {
       var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
       for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox"){
            inputs[i].checked = false
            }
       }
    }
}

function chkdxPing(chk)
{
    var strDX = "baojidx,xm,zj,dg1dx,ls,tjdx,shdx,fzdx,qzdx,guangxidx,scmydx,jsnjdx,gzdx,jsszdx,gdszdx,shanghaidx,zhejiangdx,beijingdx,sccddx,huanggangdx,bjdxt,chongqingdx,hbdx,dongguandx,jsyangzhoudx,guizhou360jq,jxnc360jq,hubeiwhdx,qhddx,qingdaodx,foshandx,zhenjiangdx,hangzhoudx,wenzhoudx,dgdx2,zhenjiang2dx,guizhoussf,ahdxebadu";
    if(chk==true)
    {
       var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
       for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox" && strDX.indexOf(inputs[i].value) >= 0){
            inputs[i].checked = true
            }
       }
    }
    else
    {
       var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
       for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox" && strDX.indexOf(inputs[i].value) >= 0){
            inputs[i].checked = false
            }
       }
    }
}

function chkltPing(chk)
{
    var strLT = "hblt,shanxilt,shlt,henanlt,xz,sdlt,fzlt,zzlt,wflt,beijinglt,baojisx,baojilt,luoyangdx,zzbgp,tjlt,tjbgp,qhdlt,bjtopservers,jslyglt";
    if(chk==true)
    {
       var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
       for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox" && strLT.indexOf(inputs[i].value) >= 0){
            inputs[i].checked = true
            }
       }
    }
    else
    {
       var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
       for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox" && strLT.indexOf(inputs[i].value) >= 0){
            inputs[i].checked = false
            }
       }
    }
}

function chkqtPing(chk)
{
    var strQT1 = "yidong,beijingtt,hkwebkaka,singapore,fjyd,tw,xianggang,unndche,uspr,usfdc,usbn,uk,japan,helan,ushe,xinjiapo,germany,jp,enzu,ntt,sim,yuenan,webnx,tai1wan,usec,bjdianxin,fjsx,bjlt,bjbgp,uskt,pccw,hkcnkuai,uscatianrun,letswinh,letswint,france,szbgp,hk360jq,bj3389,bjdx1000,usraksmart,hkraksmart,hk3389,hk1000,fsdianxin,hkzhiduo,uszhiduo,korea1949,uswx19,uswx18,hk6w,changshabgp,hkwinman,usssf,hanguo,hbyd";
    if(chk==true)
    {
       var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
       for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox" && strQT1.indexOf(inputs[i].value) >= 0){
            inputs[i].checked = true
            }
       }
    }
    else
    {
       var inputs = document.getElementById("CheckForm").getElementsByTagName("input");
       for(var i=0; i<inputs.length; i++){
        if (inputs[i].type == "checkbox" && strQT1.indexOf(inputs[i].value) >= 0){
            inputs[i].checked = false
            }
       }
    }
}

function showPingResult(n,ChartPath){
    if(n=="1"){
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=4", "ChartId", "500", "300", "0", "0");
	    chart.setDataURL(ChartPath+"Bar2D_PingResult.xml?id="+NewGuid());
	}
	else if(n=="2"){
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=1", "ChartId", "500", "300", "0", "0");
	    chart.setDataURL(ChartPath+"ScrollCombi2DDY_AllLines.xml?id="+NewGuid());
	}
	else if(n=="3"){
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=1", "ChartId", "500", "300", "0", "0");
	    chart.setDataURL(ChartPath+"ScrollCombi2DDY_DXLines.xml?id="+NewGuid());
	}
	else if(n=="4"){
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=1", "ChartId", "500", "300", "0", "0");
	    chart.setDataURL(ChartPath+"ScrollCombi2DDY_LTLines.xml?id="+NewGuid());
	}
	else if(n=="5"){
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=4", "ChartId", "500", "300", "0", "0");
	    chart.setDataURL(ChartPath+"Bar2D_DXLTLines.xml?id="+NewGuid());
	}
	chart.render("div_tracertInfo_chart");
} 

function showPingChart(m){
   showPingResult(m,document.getElementById("hidChartPath").innerHTML);
}
//------------------------------------------------------------  js for ping.aspx end

//------------------------------------------------------------  js for ping.aspx begin //网站批量测试
function btn_PingBatOnClick(){
    
    var szUrls = CheckUrls(); //测试网址
    if(szUrls=="") szUrls = " ";
    var szNum = document.getElementById('Num2').value;
    if(szNum=="") szNum = 4;
    if(parseInt(szNum)>parseInt(30)) szNum = 4;
    document.getElementById('btnPingBat').disabled=true;
    document.getElementById('urls').disabled=true;
    document.getElementById("div_itil").style.display="none";
    document.getElementById("div_process").style.display="";
    document.getElementById("lblResult").innerHTML="";
    document.getElementById('div_tracertInfo_more').innerHTML="";
    document.getElementById('tblChart').style.display='none';
    
    var strLocation = CheckLocation();//测试地点
    
    ajaxPingBatStart(0,szUrls,szNum,NewGuid(),strLocation);
} 

function ajaxPingBatStart(Code,Urls,Num,GUID,sLocation){
    var iCount;
    iCount = Code + 1;
    ping.GetPingBatResponse(iCount,Urls,Num,GUID,sLocation,GetPingBatResponse_CallBack);
    if(iCount < 20 )
         ajax_evt = setTimeout("ajaxPingBatStart("+iCount+",'"+Urls+"',"+Num+",'"+GUID+"','"+sLocation+"')",1500);
         
}

function GetPingBatResponse_CallBack(response){
   
    if (response.error != null){
        //alert(response.error);
        return;
    }
    var str = response.value;
    
    if(str.length>0||str.indexOf("Error")>0){
       document.getElementById("div_process").style.display="none";
    }
       
    if(str.indexOf("Error")>0){
        
        ajaxPingStop();
        document.getElementById('btnPingBat').disabled=false;
        document.getElementById('urls').disabled=false;
    }
    
    //var arrStr = str.indexOf("all Trace completed");
    if(str.indexOf("all Ping completed") > 0){
       
	   ajaxPingStop();
       document.getElementById('btnPingBat').disabled=false;
       document.getElementById('urls').disabled=false;
       document.getElementById("lblResult").innerHTML=str;
       
       var statHTML = str.split("||statTable||");
       //alert(statHTML[1]);
       //var chunks = str.split("TimeParas");
       //var timeParas = chunks[1]+"$"+chunks[3]+"$"+chunks[5]+"$"+chunks[7]+"$"+chunks[9]+"$"+chunks[11]+"$"+chunks[13]+"$"+chunks[15]+"$"+chunks[17];
	   //var strChart = "<iframe frameborder='0' scrolling='no' width='100%' height='380px' src='drawing.aspx?timeParas="+timeParas+"' name='webchart'></iframe>";	
       if(document.getElementById('div_tracertInfo_chart').innerHTML==""){
	       //document.getElementById('div_tracertInfo_chart').innerHTML = strChart;
	       document.getElementById('div_tracertInfo_chart').innerHTML =statHTML[1];
	       
	   }
	   
	   
	   

    }
    if(document.getElementById('div_tracertInfo_chart').innerHTML==""){
        document.getElementById("lblResult").innerHTML=str;
    }
    
    
}   
function CheckUrls(){
    var Urls = document.getElementById('urls').value;
    var reg=new RegExp("\r\n","g"); 
    Urls= Urls.replace(reg,"<br>"); 
    var reg2=new RegExp("\n","g"); //针对firefox
    Urls= Urls.replace(reg2,"<br>"); 
    return Urls;
    
}
function CheckLocation(){
    var chkLoc = "";
    var inputs = document.CheckForm.radio_location;
    for(var i=0; i<inputs.length; i++){
        if (inputs[i].checked == true){
            chkLoc = inputs[i].value;
        }
    }
    
    return chkLoc;
}
//------------------------------------------------------------  js for ping.aspx end //网站批量测试

//------------------------------------------------------------ js for speedRank begin


function ajaxSpeedRankInit(area,bdt,edt,cid){
    if(area=="guangdong")
        guangdong_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="heilongjiang")
        heilongjiang_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="jilin")
        jilin_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="hainan")
        hainan_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="xinjiang")
        xinjiang_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="xizang")
        xizang_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="gansu")
        gansu_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="guangxi")
        guangxi_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="yunnan")
        yunnan_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="guizhou")
        guizhou_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="chongqing")
        chongqing_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="hubei")
        hubei_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="anhui")
        anhui_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="henan")
        henan_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="ningxia")
        ningxia_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="_shanxi")
        _shanxi_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="hunan")
        hunan_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="aomen")
        aomen_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="xianggang")
        xianggang_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="taiwan")
        taiwan_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="fujian")
        fujian_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="jiangxi")
        jiangxi_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="zhejiang")
        zhejiang_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="shanghai")
        shanghai_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="jiangsu")
        jiangsu_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="shandong")
        shandong_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="hebei")
        hebei_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="tianjin")
        tianjin_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="beijing")
        beijing_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="liaoning")
        liaoning_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="shanxi")
        shanxi_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="qinghai")
        qinghai_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="neimeng")
        neimeng_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="sichuan")
        sichuan_rank.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
    else if(area=="")
        speedTest.GetSpeedRankResponse(area,bdt,edt,cid,GetSpeedRankResponse_CallBack);
}


function GetSpeedRankResponse_CallBack(response){
    if (response.error != null){
        return;
    }
    var str = response.value;
    if(str.indexOf("completed")>0){
        var arrStr = str.split("|");
        ChartXmlPath = arrStr[0];
        showChartRankCity(arrStr[1],arrStr[2]);
    }
}

function showChartRankCity(bdt,edt){
        var arrChartXmlPath = ChartXmlPath.split("$");
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=1&ChartNoDataText=No Data&LoadDataErrorText=No Data", "ChartId", "540", "280", "0", "0");
	    chart.setDataURL(arrChartXmlPath[0]+bdt+"-"+edt+"_City.xml?id="+NewGuid());
	    chart.render("div_chart");
} 

function showChartRankIsp(bdt,edt){
        var arrChartXmlPath = ChartXmlPath.split("$");
        
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=1&ChartNoDataText=No Data&LoadDataErrorText=No Data", "ChartId", "540", "280", "0", "0");
	    chart.setDataURL(arrChartXmlPath[0]+bdt+"-"+edt+"_Prov_ISP.xml?id="+NewGuid());
	    chart.render("div_chart");
}

function showChartRankCityCount(bdt,edt){
        var arrChartXmlPath = ChartXmlPath.split("$");
        
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=2&ChartNoDataText=No Data&LoadDataErrorText=No Data", "ChartId", "540", "280", "0", "0");
	    chart.setDataURL(arrChartXmlPath[1]+bdt+"-"+edt+"_City.xml?id="+NewGuid());
	    chart.render("div_chart");
}

function showChartRankIspCount(bdt,edt){
        var arrChartXmlPath = ChartXmlPath.split("$");
        
	    var chart = new FusionCharts("speed/flashViewer.swf?flag=2&ChartNoDataText=No Data&LoadDataErrorText=No Data", "ChartId", "540", "280", "0", "0");
	    chart.setDataURL(arrChartXmlPath[1]+bdt+"-"+edt+"_Prov_ISP.xml?id="+NewGuid());
	    chart.render("div_chart");
}

//------------------------------------------------------------ js for speedRank end

//------------------------------------------------------------  js for dns begin

//js去除空格函数
String.prototype.Trim = function() 
{ 
    return this.replace(/(^\s*)|(\s*$)/g, ""); 
} 
function Query_OnClick(){
    if(document.getElementById("Radio1").checked){
       btnDns_OnClick();
    }
    else{
       btnDns2_OnClick();
    }
}
function btnDns_OnClick(){
    var szUrl = document.getElementById('url').value;
    var szIPv = "4";
    var szPort = document.getElementById('txtPort').value;
    var szTime = document.getElementById('txtTimeOut').value;
    if(szTime.Trim().length == 0)
        document.getElementById('txtTimeOut').value = 5;
    if(szPort.Trim().length == 0)
        document.getElementById('txtPort').value = 53;
    if(document.getElementById('radIPv6').checked)
        szIPv = "6";
    if(szUrl.Trim().length > 0){
        setSiteCookie(szUrl);
        document.getElementById('btnDns').disabled=true;
        document.getElementById('url').disabled=true;
        ajaxDnsStart(0,szUrl,szIPv,szTime,szPort,NewGuid());
    }
} 
function ajaxDnsStart(Code,sUrl,sIPv,sTime,sPort,GUID){
    var iCount;
    iCount = Code + 1;
    dns.GetDnsResponse(iCount,sUrl,sIPv,sTime,sPort,GUID,GetDnsResponse_CallBack);
    if(iCount < 60 )
         ajax_evt = setTimeout("ajaxDnsStart("+iCount+",'"+sUrl+"','"+sIPv+"','"+sTime+"','"+sPort+"','"+GUID+"')",1500);
         
}

function GetDnsResponse_CallBack(response){
   
    if (response.error != null){
        //alert(response.error);
        return;
    }
    var str = response.value;
    
    //alert(str);

    if(str.length>0||str.indexOf("Error")>0){
       //document.getElementById("div_process").style.display="none";
    }
       
    if(str.indexOf("Error")>0){
        
        ajaxDnsStop();
        document.getElementById('btnDns').disabled=false;
        document.getElementById('url').disabled=false;
    }
    var statHTML = str.split("||innerHtml||");
    if(statHTML.length > 1){
        document.getElementById("dnsResult").innerHTML=statHTML[0];
    }
    if(str.indexOf("all dns completed") > 0){
       
	   ajaxDnsStop();
       document.getElementById('btnDns').disabled=false;
       document.getElementById('url').disabled=false;
    }
    
    
}   
function showTr(n){
    if(document.getElementById(n).style.display=="")
      document.getElementById(n).style.display="none";
    else
      document.getElementById(n).style.display="";
}
function ajaxDnsStop(){
    if (ajax_evt) clearTimeout(ajax_evt);
}
//--------------------------------------------------------------dig2

function btnDns2_OnClick(){
    var szUrl = document.getElementById('url').value;
    var szSvr = document.getElementById('txtServer').value;
    if(!isIP(szSvr)){
       alert(unescape('DNS%u5730%u5740%u4E0D%u6B63%u786E%uFF0C%u8BF7%u91CD%u65B0%u8F93%u5165'));
       document.getElementById('txtServer').focus();
       return false;
    }
    else{
       var szType = "";
       var szTrace = "";
       var szIPv = "4";
       var szPort = document.getElementById('textPort').value;
       var szTime = document.getElementById('txtTime').value;
       var szTries = document.getElementById('txtTries').value;
       
       var szTCP = "";
       if(document.getElementById('chkA').checked)
           szType = "A";
       if(document.getElementById('chkNS').checked)
           if(szType == "")
            szType = "NS";
           else
            szType = szType + ";NS";
       if(document.getElementById('chkMX').checked)
           if(szType == "")
            szType = "MX";
           else
            szType = szType + ";MX";
       if(document.getElementById('chkANY').checked)
           if(szType == "")
            szType = "ANY";
           else
            szType = szType + ";ANY";
       if(document.getElementById('chkQT').checked)
           if(szType == "")
            szType = document.getElementById('selectQT').value;
           else
            szType = szType + ";" + document.getElementById('selectQT').value;
       if(document.getElementById('chkTrace').checked)
           szTrace = "trace";   
       if(document.getElementById('radioIPv6').checked)
           szIPv = "6";
       if(document.getElementById('radioTCP').checked)
           szTCP = "TCP";
       if(szTime.Trim().length == 0)
           document.getElementById('txtTime').value = 5;
       if(szTries.Trim().length == 0)
           document.getElementById('txtTries').value = 1;
       if(szPort.Trim().length == 0)
           document.getElementById('textPort').value = 53;
       if(szType.Trim().length == 0){
           alert(unescape('%u67E5%u8BE2%u7C7B%u578B%u4E0D%u80FD%u4E3A%u7A7A'));
           return false;
       } 
       if(szUrl.Trim().length > 0){
           setSiteCookie(szUrl);
           document.getElementById('btnDns').disabled=true;
           document.getElementById('url').disabled=true;
           ajaxDnsQueryStart(0,szUrl,szSvr,szType,szTrace,szIPv,szTime,szTries,szTCP,szPort,NewGuid());
       }
    }
} 
function ajaxDnsQueryStart(Code,sUrl,sSvr,sType,sTrace,sIPv,sTime,sTries,sTCP,sPort,GUID){
    var iCount;
    iCount = Code + 1;
    dns.DnsQueryResponse(iCount,sUrl,sSvr,sType,sTrace,sIPv,sTime,sTries,sTCP,sPort,GUID,DnsQueryResponse_CallBack);
    if(iCount < 60 )
         ajax_evt = setTimeout("ajaxDnsQueryStart("+iCount+",'"+sUrl+"','"+sSvr+"','"+sType+"','"+sTrace+"','"+sIPv+"','"+sTime+"','"+sTries+"','"+sTCP+"','"+sPort+"','"+GUID+"')",1500);
         
}

function DnsQueryResponse_CallBack(response){
   
    if (response.error != null){
        //alert(response.error);
        return;
    }
    var str = response.value;
    
    //alert(str);

    if(str.length>0||str.indexOf("Error")>0){
       //document.getElementById("div_process").style.display="none";
    }
       
    if(str.indexOf("Error")>0){
        
        ajaxDnsStop();
        document.getElementById('btnDns').disabled=false;
        document.getElementById('url').disabled=false;
    }
    var statHTML = str.split("||innerHtml||");
    if(statHTML.length > 1){
        
        document.getElementById("dnsResult").innerHTML=statHTML[0];
        
    }
    if(str.indexOf("all dns completed") > 0){
       
	   ajaxDnsStop();
       document.getElementById('btnDns').disabled=false;
       document.getElementById('url').disabled=false;
    }
    
    
}  
function svrOnChange(){
   document.getElementById('txtServer').value = document.getElementById('selectDns').value;
}
function DnsPort(obj){
//先把非数字的都替换掉，除了数字
obj.value = obj.value.replace(/[^\d]/g,"");
}
function DnsTime(obj){
//先把非数字的都替换掉，除了数字
obj.value = obj.value.replace(/[^\d]/g,"");
if(parseInt(obj.value)>parseInt(10)) obj.value = 5;
}
function DnsTries(obj){
//先把非数字的都替换掉，除了数字
obj.value = obj.value.replace(/[^\d]/g,"");
if(parseInt(obj.value)>parseInt(3)) obj.value = 1;
}
function isIP(s){   
  var arr=s.match(/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/);   
  if(arr==null)return false;   
  for(i=1;i<arr.length;i++)if(String(Number(arr[i]))!=arr[i]||Number(arr[i])>255)return false;   
  return true;   
} 

//------------------------------------------------------------  js for dns end