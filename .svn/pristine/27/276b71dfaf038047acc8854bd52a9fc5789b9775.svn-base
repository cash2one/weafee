#!/usr/local/bin/python
from urllib2 import Request, urlopen, URLError, HTTPError 
from urllib import urlencode
import re
import json
from collections import OrderedDict

withfi_track_url = 'http://54.65.245.210/track/track.php?proj=iqiyi_video'
#creative_file = '3.f4v'
creative_file = '01be0fedb30c8cc37de1d110a31b8951.flv'
duration = 30
with open('/etc/withfi/miwifi_toolbar_info.js', 'r') as f:
    p = re.compile('{.+}')
    s = f.read()
    meta = json.loads( p.search(s).group(0))
    params = '&'.join(["%s=%s" %(k,v) for k, v in meta.items()])
    withfi_track_url = '&'.join(('http://54.65.245.210/track/track.php?proj=iqiyi_video', params))

def bid(logger, type='iqiyi', p={}):
    default_resp = (True,
                   'http://dev.10086.cn/videos/creative/%s?v=864293472&pv=0.1&client=183.206.164.107&src=iqiyi.com' % creative_file,
                   duration,
                   'http://m.baidu.com',
                   [withfi_track_url],
                   'local',
                   )
    return default_resp

