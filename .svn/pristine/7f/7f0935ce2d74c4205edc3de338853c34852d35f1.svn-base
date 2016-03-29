#!/usr/local/bin/python
'''
Replace iqiyi video ads
'''
import json
import re

withfi_track_url = 'http://54.65.245.210/track/track.php'
def read_track_params():
    with open('/etc/withfi/miwifi_toolbar_info.js', 'r') as f:
        p = re.compile('{.+}')
        s = f.read()
        meta = json.loads( p.search(s).group(0))
        params = '&'.join(["%s=%s" %(k, v) for k, v in meta.items()])
        return params
params = read_track_params()
withfi_track_url_iqiyi_video = '&'.join((withfi_track_url + '?proj=iqiyi_video', params))
withfi_track_url_tencent_video = '&'.join((withfi_track_url + '?proj=tencent_video', params))
