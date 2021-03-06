#!/usr/local/bin/python
from urllib2 import Request, urlopen, URLError, HTTPError 
from urllib import urlencode
import re
import json
from collections import OrderedDict
import threading
params = {'iqiyi': {
                 'slotid': 100124,
                 'aw': 896,
                 'ah': 504,
           },
          'tencent': {
                 'slotid': 100149,
                 'aw': 480,
                 'ah': 272,
           },
       }
def batch_bid(logger, type='iqiyi', timeout=5, p={}, count=3):
    # list of tuple: ret, creative_url, duration, click_url, track_urls, src
    bid_results = []
    mutex = threading.Lock()
    def threadFunc(query_id):
        bid_result = bid(logger, type, timeout, p)
        mutex.acquire()
        bid_results.append(bid_result)
        logger.info("Fulfill bid response %d" % query_id)
        mutex.release()
    threads = []
    for i in xrange(3):
        threads.append(threading.Thread(target=threadFunc, args=(i,)))
    for t in threads:
        t.start()
    for t in threads:
        t.join()
    return bid_results
def bid(logger, type='iqiyi', timeout=5, p={}):
    default_resp = False, '', 15, '', [], 'local default'
    parameters = OrderedDict()
    parameters['slotid'] = params[type]['slotid']
    parameters['aw'] = params[type]['aw']
    parameters['ah'] = params[type]['ah']
    parameters['adtype'] = 1
    parameters['dt'] = 1
    parameters['cr'] = 0
    parameters['os'] = p.get('os')
    parameters['osv'] = p.get('osv')
    parameters['brand'] = p.get('brand')
    parameters['model'] = p.get('model')
    parameters['sw'] = p.get('sw')
    parameters['sh'] = p.get('sh')
    parameters['did'] = p.get('did')
    parameters['uuid'] = p.get('uuid')
    for k, v in parameters.items():
        if v is None:
            parameters[k] = 'xxxx'
    #parameters['ip'] = '192.168.1.254'
    addr = '?'.join(('http://ad.test.rtbs.cn/ad', urlencode(parameters)))
    req = Request(addr)
    #req.add_header('Content-Type', 'application/x-www-form-urlencoded')
    try: 
        response = urlopen(req, timeout=timeout) 
    except HTTPError, e: 
        logger.error("The server couldn't fulfill the request, Error code: %d, %s" % (e.code, e.reason))
        return default_resp
    except URLError, e: 
        logger.error('We failed to reach a server,  Error code: %s' % (e.reason))
        return default_resp
    result = response.read()
    response.close()
    #print 'Response from server: %s' % (result,)
    p = re.compile('{.+}')
    if not p.search(result):
        logger.error('Responce from Huixuan is invalid: %d, %s' % (e.code, e.reason))
        return default_resp
    meta = json.loads(p.search(result).group(0));
    logger.info("Bid request to huixuan << %s" % addr)
    def retrieve_bid_result(x):
        ret, creative_url, duration, click_url, track_urls, src = default_resp
        def bid_ok(x):
            return x.has_key('creative_url') and x.has_key('click_monitor') and x.has_key('monitorurls')
        if bid_ok(x):
            creative_url = meta['creative_url']
            click_url = meta['click_monitor']
            #FIXME return real duration in bidding response
            #duration = meta['rtime']
            track_urls = meta['monitorurls']
            src = 'Huixuan Normal'
            logger.info("Got a normal response from Huixuan")
            ret = True
        else:
            try:
                default = json.loads(x['return_code'])
                creative_url = default['creative_url']
                click_url = default['click_monitor']
                #duration = meta['rtime']
                track_urls = default['monitorurls']
                logger.info("Got a default response from Huixuan")
                src = 'Huixuan Default'
                ret = True
            except Error, e:
                logger.error('Server default from Huixuan is invalid: %s' % (e.message))
        return ret, creative_url, duration, click_url, track_urls, src

    return retrieve_bid_result(meta)
    #http://ad.test.rtbs.cn/ad?slotid=100124&aw=896&ah=504&adtype=1&dt=1&cr=0&nt=0&os=android&osv=4.1.2&brand=xxxx&model=xxxxx&sw=1280&sh=720&did=xxxx&uuid=xxxx&pn=xxxxx&ip=192.168.1.254
    #                          slotid=100149&aw=896&ah=504&adtype=1&dt=1&cr=0&nt=0&os=android&osv=4.1.2&brand=xxxx&model=xxxxx&sw=1280&sh=720&did=xxxx&uuid=xxxx&ip=192.168.1.254

