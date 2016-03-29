'''
add 4 tencent vedio ads
'''

from lxml import etree as ET
import re
import json
import random

from libmproxy import filt

with open('/etc/withfi/miwifi_toolbar_info.js', 'r') as f:
    p = re.compile('{.+}')
    s = f.read()
    meta = json.loads( p.search(s).group(0));
    params = '&'.join(["%s=%s" %(k,v) for k, v in meta.items()])

filter_url = 'lives.l.qq.com/livemsg'
fake_video_url = "http://www.10086.cn/videos/test/txbb.mp4"
fake_click_url = "http://www.baidu.com?"
withfi_track_url = '&'.join(('http://54.65.245.210/track/track.php?proj=tencent_video', params))
tencent_track_item = '''
<reportitem><url><![CDATA[%s]]></url><reporttime>0</reporttime></reportitem>
'''%withfi_track_url

random_alpha = 'abcdefghijklmnopqrstuvwxyz1234567890'
replay_num = 4
ad_item_template = '''
<item> 
  <order_id>2500895</order_id>  
  <type>WL</type>  
  <display_code/>  
  <cover_id> <![CDATA[ ]]> </cover_id>  
  <url> <![CDATA[ ]]> </url>  
  <urlsmall> <![CDATA[ ]]> </urlsmall>  
  <params> <![CDATA[ ]]> </params>  
  <image> 
    <index>0</index>  
    <url> <![CDATA[ ]]> </url>  
    <md5>d9a54f6b2616fbf8fb32e613ee916d09</md5>  
    <cs>797265</cs>  
    <ds>15</ds>  
    <width>640</width>  
    <height>360</height>  
    <vid>j0200d0261w</vid> 
  </image>  
  <image> 
    <index>1</index>  
    <url> <![CDATA[ ]]> </url>  
    <md5>d9a54f6b2616fbf8fb32e613ee916d09</md5>  
    <cs>797265</cs>  
    <ds>15</ds>  
    <width>640</width>  
    <height>360</height>  
    <vid>j0200d0261w</vid> 
  </image>  
  <duration>15000</duration>  
  <ReportTime>0</ReportTime>  
  <no_click>N</no_click>  
  <inner_jump>N</inner_jump>  
  <link> <![CDATA[ ]]> </link>  
  <reportUrl> <![CDATA[ ]]> </reportUrl>  
  <reportUrlOther /> 
</item>
'''

def response(context, flow):
    if flow.match(filt.parse(filter_url)):
        # print 'match filter'
        # print flow.response.content
        parser = ET.XMLParser(strip_cdata=False)
        root = ET.fromstring(flow.response.content, parser)

        node_adlist = root.find('adList')

        if node_adlist is not None:
            for item_node in node_adlist.findall('item'):
                if int(item_node.findtext('order_id')) != 1:
                    node_adlist.remove(item_node)

            for i in range(replay_num):
                item = ET.XML(ad_item_template, parser)
                for video_link in item.findall('./image/url'):
                    video_link.text = ET.CDATA(fake_video_url)

                item.find('order_id').text = str(random.randint(1000000, 9999999))
                for vid in item.findall('./image/vid'):
                    vid.text = "".join(random.sample(random_alpha, 11))

                item.find('link').text = ET.CDATA(fake_click_url)
                    
                item.find('reportUrlOther').append(ET.XML(tencent_track_item, parser))

                node_adlist.append(item)

        flow.response.content = ET.tostring(root)