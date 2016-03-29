#!/usr/bin/env python
''' Install: 1. copy this file to /etc/rc.d/
             2. chmod +x /etc/rc.d/rc.local
             3. append below text to /etc/rc.d/rc.local
                 if [ -f /etc/rc.d/register.py ]; then
                   python /etc/rc.d/register.py > /etc/rc.d/register.log
                 fi
'''


from urllib2 import Request, urlopen, URLError, HTTPError 
import json
import sys
import os
import uuid
import time

def get_nproc():
    with os.popen("nproc") as output:
        nproc = output.read().strip('\n')
    return int(nproc)
def get_neth():
    with os.popen("lspci | grep -c Ethernet") as output:
        neth = output.read().strip('\n')
    return int(neth)
def get_mac_address0():
    node = uuid.getnode()
    mac_hex = uuid.UUID(int = node).hex[-12:]
    return ":".join([mac_hex[i: i + 2] for i in range(0, 11, 2)]).upper()
def get_mac_address(interface):
    #interface = 'eth0'
    mac = '00:11:22:33:44:55'
    with os.popen('ifconfig %s | grep -oP "(?<=HWaddr )[0-9A-Fa-f:]*"' % (interface,)) as output:
        mac = output.read().strip('\n').upper()
    return mac

def register_router():
    patch_mode = False
    if len(sys.argv) == 2 and int(sys.argv[1]) != 0: # patch mode
        patch_mode = True
    addr = 'http://admin.withfi.com/device/register'
    mac = get_mac_address('eth0')
    release_id = 1
    url = ('%s?MAC=%s&release_id=%d') % (addr, mac, release_id)
    req = Request(url)
    retry = 0
    while retry < 100:
        try: 
            response = urlopen(req) 
        except HTTPError, e: 
            print "The server couldn't handle the request."
            print 'Error code: ', e.code 
            time.sleep( 5 ) #wait for network ready
            retry += 1
            continue
        except URLError, e: 
            print 'We failed to reach a server.' 
            print 'Reason: ', e.reason
            time.sleep( 5 ) #wait for network ready
            retry += 1
            continue
        result = response.read()
        response.close()
        break
    if retry == 100:
        sys.exit(-1)
    print 'Response from server: %s after retrying %d times' % (result, retry)
    obj = json.loads(result)
    registered = obj['registered']
    if registered == 1:
        device_id = obj['device_id']
        print('Got device_id %d' % int(device_id))
        if patch_mode:
            device_id = int(sys.argv[1])
        os.system('echo %d > /var/device_id' % device_id)
        nproc = get_nproc()
        neth = get_neth()
        hardware = 'Unknown'
        if nproc == 4 and neth == 4:
            hardware = 'R200'
        elif nproc == 2 and neth == 6:
            hardware = 'R400'
        else:
            pass
        miwifi_toolbar_info = {'toolbar_id': 1, 'device_id': device_id, 'rom': '1.0.0', 'hardware': hardware, 'channel': 'stable' };
        obj_str = ' = '.join(('var miwifi_toolbar_info', json.dumps(miwifi_toolbar_info))) 
        with open('/etc/withfi/miwifi_toolbar_info.js', 'w') as f:
            f.write(obj_str)
        if not patch_mode:
            os.remove(sys.argv[0]) #delete this file itself
            os.system('shutdown -h now')
        sys.exit(0)
    else:
        print "Register failed! %s" % obj['error']
        sys.exit(-3)
def main():
    register_router()
if __name__ == "__main__":
    main()
