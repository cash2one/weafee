#!/usr/bin/env python
from netfilterqueue import NetfilterQueue
from scapy.all import *
from scapy.layers import http
from scapy.sendrecv import __gen_send
import sys
import time

def print_and_drop(pkt):
    global n
    global sock
    global http_rsp
    n = n + 1
    if n % 100 == 0:
        print "Received %d packages.\n" % (n,)
    if pkt.get_payload_len() > 0:
        ip_layer = ''.join(map(lambda x:hex(ord(x))[2:].zfill(2), pkt.get_payload()))
        time0 = time.time()
        x = IP(ip_layer.decode('hex'))
        time0 = time.time() - time0
#        x.show()
        time2 = time.time()
        if hasattr(x, 'Method') and x.Method == 'GET' \
           and x.Path.find('_t=t') == -1 \
           and hasattr(x, 'Accept') and x.Accept is not None \
           and x.Accept.lower().find('html')!= -1:
            #print x
            #print "size= %d" % (pkt.get_payload_len())
            time1 = time.time()
	    layerip = x.getlayer('IP')
            pkt2 = Ether()/IP(src=layerip.dst, dst=layerip.src,ttl=127, flags=2)\
                /TCP(dport=x.sport, sport=x.dport, seq = x.ack, ack=x.seq+x.len-x.ihl*4-x.dataofs*4, flags=0x011)\
                /http_rsp
	    time4 = time.time() - time1
            #sendp(Ether()/IP(src=layerip.dst, dst=layerip.src,ttl=127, flags=2)\
            #    /TCP(dport=x.sport, sport=x.dport, seq = x.ack, ack=x.seq+x.len-x.ihl*4-x.dataofs*4, flags=0x011)\
            #    /http_rsp, verbose=False, iface='eth1')
            sendp(pkt2, verbose=False, iface='br0')
	    #__gen_send(sock, pkt2, inter=0, loop=0, count=None, verbose=None, realtime=None)
            timex = time.time()
            print("Costs: parse %f, dicision %f, compose %f, send %f." % (time0, time1 - time2, time4, timex - time1,))
    pkt.drop()

def main():
    queue_id = 1
    if len(sys.argv) == 2 and int(sys.argv[1]) != 0: #queue id was specified
       queue_id = int(sys.argv[1])
    global n
    global http_rsp
    global sock
    resp_file = 'resp.html'
    html = '<html> <body onLoad=u()> <div id=i> <iframe id=m frameborder=0 width=100% height=100%></iframe> </div> <div id=x> <a onClick=c()>X</a > <iframe src=http://withfi.com/unitest/banner.jpg width=400 height=50 scrolling=no frameborder=0></iframe> </div> </body> </html>'
    with open(resp_file) as f:
        html = f.read()
    http_rsp = r'''
HTTP/1.1 200 OK
Content-Type: text/html;charset=utf-8
Connection: close
Expires: 0
Cache-control: no-store,no-cache,must-revalidate,post-check=0,pre-check=0
Content-Length:%d

%s''' % (len(html), html)
    n = 0
    nfqueue = NetfilterQueue()
    sock = conf.L2socket(iface='eth1')
    max_queue_len = 10
    nfqueue.bind(queue_id, print_and_drop, max_queue_len)
    try:
        nfqueue.run()
    except KeyboardInterrupt:
        print

if __name__ == "__main__":
    main()
