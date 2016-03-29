#!/usr/local/bin/python
'''
Replace iqiyi video ads
'''
import json
from pprint import pprint
from random import randint
import re
import logging
import logging.handlers

from libmproxy import filt
from libmproxy.protocol.http import HTTPResponse
from libmproxy.script import concurrent
import time
from urllib import urlencode, unquote

from sys import path
path.append(r"/root/videos")
from withfi import withfi_track_url_iqiyi_video
import huixuan_bid
import local_bid


LOG_PATH = '/root/videos/iqiyi.log'
logger = logging.getLogger('log')
loghdlr1 = logging.handlers.RotatingFileHandler(LOG_PATH, "a", 0, 1)
fmt1 = logging.Formatter("%(asctime)s %(process)d %(thread)d %(levelname)-6s %(message)s", "%Y-%m-%d %H:%M:%S")
loghdlr1.setFormatter(fmt1)
logger.addHandler(loghdlr1)
logger.setLevel(logging.INFO)


FAKE_VIDEO_TEMPLATE = 'http://dev.10086.cn/videos/other/20151215/54/64/%s.f4v?v=864201235'
TARGET_URL = '~u api.cupid.iqiyi.com/mixer'
#bidders
BIDDER_HUIXUAN = 1
BIDDER_LOCAL = 2
BIDDER_NONE = 0
BIDDER = BIDDER_HUIXUAN

MIDTERM_RESP_PATH = '/dev/shm/videos/other/20151215/54/64/'

@concurrent
def response(context, flow):
    if flow.match(filt.parse(TARGET_URL)):
        #print flow.request.path
        params = dict()
        try:
            #logger.info("Get request from user agent.")
            logger.info("User request to iQiyi << %s", flow.request.path)
            retrieve_query_params(flow.request.path, params)
            logger.info("Request params << %s", json.dumps(params))
            #logger.info("Request parameters: %s", json.dumps(params))
            bid_mixer = json.loads(flow.response.content)
            logger.info("iQiyi respond")
            logger.debug("iQiyi response << %s", json.dumps(bid_mixer))
            # f.write(json.dumps(bid_mixer, indent=2))
            if bid_mixer.has_key('adSlots'):
                #adSlot['duration'] = max(30, min(15, adSlot['duration'])) # range: 30s ~ 60s
                # NOTE maybe consume many time
                if BIDDER == BIDDER_HUIXUAN:
                    logger.info("Bid to Huixuan")
                    adSlot = bid_mixer['adSlots'][0]
                    ad_count = adSlot['duration'] / 15
                    results = huixuan_bid.batch_bid(logger, type='iqiyi', timeout=3, p=params, count=ad_count)
                    for (ret_ok, creative_url, duration, click_url, track_urls, src) in iter(results):
                        logger.info("Huixuan response %s, %s" % (ret_ok, src))
                        logger.debug("Bidding result << Creative: %s; Click: %s; Monitor: %s" % (creative_url, click_url, track_urls))
                elif BIDDER == BIDDER_LOCAL:
                    logger.info("Bid to local")
                    (ret_ok, creative_url, duration, click_url, track_urls, src) = local_bid.bid(logger, type='iqiyi', p = params)
                    logger.info("Local response %s, %s" % (ret_ok, src))
                    logger.debug("Bidding result << Creative: %s; Click: %s; Monitor: %s" % (creative_url, click_url, track_urls))
                else: #default remove ads
                    (ret_ok, creative_url, click_url, track_urls, src) = (True, '', '', [], 'local')
                    logger.info("Default response %s, %s" % (ret_ok, src))
                    logger.debug("Bidding result << Creative: %s; Click: %s; Monitor: %s" % (creative_url, click_url, track_urls))
                if not ret_ok:
                    #flow.response.content = json.dumps(bid_mixer)
                    return
                if len(bid_mixer['adSlots']) > 0:
                    adSlot = bid_mixer['adSlots'][0]
                    if len(adSlot['ads']) > 0:
                        ad = adSlot['ads'][0]
                        adSlot['ads'] = []
                        adSlot['duration'] = 0
                        for (ret_ok, creative_url, duration, click_url, track_urls, src) in iter(results):
                            ad['duration'] = duration
                            ad['clickThroughUrl'] = click_url
                            videoObject = ad['creativeObject']
                            # if we need app load video every time
                            videoObject['qipuid'] = str(randint(1, 999999999))
                            videoObject['duration'] = str(duration)
                            timestamp = '%.6f' % (time.time(), )
                            midterm_video_url = FAKE_VIDEO_TEMPLATE % (timestamp, )
                            fake_resp = {
                                  "t": "CMNET|JiangSu-183.206.164.107",
                                  "s": "1",
                                  "z": "jiangsu_cmnet",
                                  "h": "0",
                                  "e": "0",
                                }
                            bid_result = {
                                  "creative": creative_url,
                                  "click": click_url,
                                  "track": track_urls,
                                }
                            if not ret_ok:
                                video_filename = '3.f4v' #TODO a default video
                                fake_resp["l"] = "http://dev.10086.cn/videos/test/%s?v=864293472&pv=0.1&client=183.206.164.107&src=iqiyi.com" % (video_filename,)
                            else:
                                fake_resp["l"] = creative_url

                            write_cdn_resp(timestamp + '.f4v', fake_resp)
                            with open(MIDTERM_RESP_PATH + timestamp + '.bid', 'w') as f:
                                f.write(json.dumps(bid_result, indent = 2))
                            videoObject['bUrl'], \
                            videoObject['cUrl'], \
                            videoObject['gUrl'], \
                            videoObject['jUrl'], \
                            videoObject['m200Url'], \
                            videoObject['m400Url'] = [midterm_video_url for i in range(6)]

                            if ad.has_key('impressionTracking') and not ad['impressionTracking'].has_key('thirdPartyTracking'):
                                ad['impressionTracking']['thirdPartyTracking'] = []
                            for url in track_urls:
                                #print url
                                ad['impressionTracking']['thirdPartyTracking'].append(url)
                            ad['impressionTracking']['thirdPartyTracking'].append(withfi_track_url_iqiyi_video)


                            adSlot['ads'].append(ad.copy())
                            adSlot['duration'] = adSlot['duration'] + duration
                flow.response.content = json.dumps(bid_mixer)
        except Exception, e:
            logger.error("Exception: %s" % e.message)


def retrieve_query_params(path, params):
    url = unquote(path)
    i = url.find('?')
    query_str = url[i + 1:]
    kvs = query_str.split('&')
    _params = dict()
    for kv in kvs:
        k, v = kv.split('=')
        _params[k] = v
    if _params.has_key("i") and _params['i'].lower() in ['iphone', 'ipad', 'ipad mini', 'ipod touch']:
        params['brand'] = 'Apple'
        params['os'] = 'ios'
        if _params.has_key('idfa'):
            params['did'] = _params['idfa']
    else:
        params['brand'] = 'Android'
        params['os'] = 'android'
        if _params.has_key('aid'):
            params['did'] = _params['aid']
    if _params.has_key("res") and len(_params['res']) > 3:
        height, width = _params['res'].split(',')
        params['ah'], params['aw'] = params['sh'], params['sw'] = height, width
    else:
        params['ah'], params['aw'] = params['sh'], params['sw'] = 896, 504
    if _params.has_key('os') and len(_params['os']) > 0:
        params['osv'] = _params['os']
    if _params.has_key('imei') and len(_params['imei']) > 0:
        params['uuid'] = _params['imei']
    if _params.has_key('m') and len(_params['m']) > 0:
        params['model'] = _params['m']

def write_cdn_resp(filename, resp):
    with open(MIDTERM_RESP_PATH + filename, 'w') as f:
        f.write(json.dumps(resp))
