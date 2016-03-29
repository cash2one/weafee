'''
Replace tencent vedio ads
'''

from lxml import etree as ET
import re
import json
import random
import logging
import logging.handlers

from libmproxy import filt
from libmproxy.protocol.http import HTTPResponse
from libmproxy.script import concurrent
import time
from urllib import urlencode, unquote

from sys import path
path.append(r"/root/videos")
from withfi import withfi_track_url_tencent_video
import huixuan_bid
import local_bid

LOG_PATH = '/root/videos/qq.log'
logger = logging.getLogger('log')
loghdlr1 = logging.handlers.RotatingFileHandler(LOG_PATH, "a", 0, 1)
fmt1 = logging.Formatter("%(asctime)s %(process)d %(thread)d %(levelname)-6s %(message)s", "%Y-%m-%d %H:%M:%S")
loghdlr1.setFormatter(fmt1)
logger.addHandler(loghdlr1)
logger.setLevel(logging.INFO)


TARGET_URL = 'lives.l.qq.com/livemsg'
FACK_CLICK_URL = "http://www.baidu.com?"
FAKE_VIDEO_TEMPLATE = 'http://www.10086.cn/videos/creative/%s'
#bidders
BIDDER_HUIXUAN = 1
BIDDER_LOCAL = 2
BIDDER_NONE = 0
BIDDER = BIDDER_HUIXUAN

fake_video_url = FAKE_VIDEO_TEMPLATE % "txbb.mp4"
tencent_track_item_template = '''
<reportitem><url><![CDATA[%s]]></url><reporttime>0</reporttime></reportitem>
'''

random_alpha = 'abcdefghijklmnopqrstuvwxyz1234567890'

def response(context, flow):
    if flow.match(filt.parse(TARGET_URL)):
        #print flow.request.path
        try:
            #for CDATA handle
            parser = ET.XMLParser(strip_cdata=False)
            root = ET.fromstring(flow.response.content, parser)
            ad_count = 1
            params = {}
            results = huixuan_bid.batch_bid(logger, type='tencent', timeout=10, p=params, count=ad_count)
            result = results[0]
            (ret_ok, creative_url, duration, click_url, track_urls, src) = result
            for node in root.findall('.//adList/item'):
                if int(node.findtext('order_id')) != 1:
                    for video_link in node.findall('./image/url'):
                        video_link.text = ET.CDATA(creative_url)
                    for vid in node.findall('./image/vid'):
                        vid.text = "".join(random.sample(random_alpha, 11))
                     
                    if node.find('params') is not None: 
                        node.remove(node.find('params'))
                    
                    node.find('link').text = ET.CDATA(click_url)
                        
                    node.find('reportUrlOther').append(ET.XML(tencent_track_item_template % track_urls[0], parser))
                    node.find('reportUrlOther').append(ET.XML(tencent_track_item_template % withfi_track_url_tencent_video, parser))

            flow.response.content = ET.tostring(root)
        except Exception, e:
            logger.error("Exception: %s" % e.message)

        # print '-'*50 +'\n'
        # print flow.response.content

