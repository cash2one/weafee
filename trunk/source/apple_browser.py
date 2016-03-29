"""
   Withfi Apple Device WISPr
"""
import time

from libmproxy.protocol.http import HTTPResponse, HTTPFlow
from netlib.odict import ODictCaseless

DeviceState = {}
#AdLink="http://static.diditaxi.com.cn/site/pages/thank-2014/index.html?from=timeline&isappinstalled=0"
#AdLink="http://chuye.cloud7.com.cn/5035450"
#AdLink="http://prev.rabbitpre.com/apps/552ceb4183f6b3d26a82133c"
#AdLink="http://prev.rabbitpre.com/apps/552c8ad4465fe5d06aacdd16"
#AdLink="http://file.vxplo.cn/?nid=19014&key=fbbe3278a8&title=57u05a.GSDXkuqTkupLop4bpopHnp4A-&html5=true&width=640&v=32&slink=/idea/WfCwZ2l"
#AdLink="http://prev.rabbitpre.com/appDesktop/55c229d124624acedc223738"


def do_request(context, flow):
    """
        Called when a client request has been received.
    """
#    context.log("[nick] request")
    
    global DeviceState
    resp = None

#    print DeviceState
#    print flow.request.headers
    
    if (flow.request.pretty_host(hostheader=True).endswith("captive.apple.com") or flow.request.path in r'/library/test/success.html') and flow.request.headers['X-Real-IP']:
        for key, value in DeviceState.items():
            if int(time.time()) - value[1] > 300:
                del DeviceState[key]

        RealIP = flow.request.headers['X-Real-IP'][0]
        ServerIP = flow.request.headers['ServerAddress'][0]
#        print ServerIP

        AdLink = "http://%s/withficode/ios/index.htm"%ServerIP

        SuccessResp=HTTPResponse([1, 1], 200, "OK", ODictCaseless([["Content-Type", "text/html"]]), "<HTML><HEAD><TITLE>Success</TITLE></HEAD><BODY>Success</BODY></HTML>")
        PendingResp=HTTPResponse([1, 1], 302, "OK",ODictCaseless([["Content-Type", "text/html"], ["location", AdLink]]),"")

        if RealIP in DeviceState.keys():
            if DeviceState[RealIP][0] == 'accept':
                resp = SuccessResp
            elif DeviceState[RealIP][0] == 'pending':
                resp = PendingResp
                DeviceState[RealIP][0] = 'accept'
            else:
                resp = PendingResp
                DeviceState[RealIP] = ['pending', int(time.time())]
    flow.reply(resp)

def request(context, flow):
    do_request(context, flow)
