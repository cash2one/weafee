var iScreenWidth = window.screen.width;

suspendcode12="<DIV id=\"lovexin12\" style='width:100px;height:250px;position:fixed;float:left;left:1px;top:150px;'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0' width='100px' height='250px' ><param name='movie' value='http://www.webkaka.com/click/duilianleft-l(3).swf' /><param name='quality' value='high' /><param name='wmode' value='opaque' /><embed src='http://www.webkaka.com/click/duilianleft-l(3).swf' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='100px' height='250px' swliveconnect='true' name='coupletLeft' wmode=opaque></embed></object></a><br><a href='http://www.webkaka.com/click/a_d_s.asp?id=138' target='_blank' style='display:block;width:110px;height:300px;position:absolute;margin-top:-300px; z-index:889;background:#000;filter:alpha(opacity=0);-moz-opacity:0;opacity: 0' onmouseout='swapImg(1)'></a></div>"
if(iScreenWidth == 1024){
  suspendcode12="<DIV id=\"lovexin12\" style='width:20px;height:305px;position:fixed;float:left;left:1px;top:150px;'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0' width='20px' height='305px' ><param name='movie' value='http://www.webkaka.com/click/duilianleft-s(3).swf' /><param name='quality' value='high' /><param name='wmode' value='opaque' /><embed src='http://www.webkaka.com/click/duilianleft-s(3).swf' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='20px' height='305px' swliveconnect='true' name='coupletLeft' wmode=opaque></embed></object></a><br><a href='http://www.webkaka.com/click/a_d_s.asp?id=138' target='_blank' style='display:block;width:20px;height:305px;position:absolute;margin-top:-305px; z-index:888;background:#000;filter:alpha(opacity=0);-moz-opacity:0;opacity: 0' onmousemove='swapImg(0)'></a></div>"
}
else if(iScreenWidth < 1024){
  suspendcode12="<DIV id=\"lovexin12\" style='left:2px;POSITION:absolute;TOP:100px;display:none'></div>";
}
suspendcode16="<DIV id=\"lovexin16\" style='width:100px;height:250px;position:fixed;float:left;left:1px;top:150px;display:none'><object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0' width='100px' height='250px' ><param name='movie' value='http://www.webkaka.com/click/duilianleft-l(3).swf' /><param name='quality' value='high' /><param name='wmode' value='opaque' /><embed src='http://www.webkaka.com/click/duilianleft-l(3).swf' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='100px' height='250px' swliveconnect='true' name='coupletLeft' wmode=opaque></embed></object></a><br><a href='http://www.webkaka.com/click/a_d_s.asp?id=138' target='_blank' style='display:block;width:110px;height:300px;position:absolute;margin-top:-300px; z-index:889;background:#000;filter:alpha(opacity=0);-moz-opacity:0;opacity: 0' onmouseout='swapImg(1)'></a></div>"

document.write(suspendcode12);
document.write(suspendcode16);

function swapImg(n){
 if(n==0){
   document.getElementById('lovexin16').style.display = "";
   document.getElementById('lovexin12').style.display = "none";
   }
 else if(n==1){
   document.getElementById('lovexin12').style.display = "";
   document.getElementById('lovexin16').style.display = "none";
   }
}