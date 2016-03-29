#!/usr/bin/env python
''' Install:
    1. save this file to /var/spool/withfi/
    2. add cronjob:
        */15 * * * * /usr/local/bin/python /var/spool/withfi/beacon.py &> /var/spool/withfi/beacon.log
'''
from urllib2 import Request, urlopen, URLError, HTTPError 
from urllib import urlencode
import json
import sys
import os
import uuid

# Sample wget 'http://admin.withfi.com/device/beacon' --post-data='MAC=00:11:22:33:44:55&FreeCPU=0&FreeMemory=0&AdsEnabled=0&PowerOnTime=2015-02-03%2012:36&ConnectionCount=0&device_id=3302' --header='Content-Type: application/x-www-form-urlencoded' -O- -q
def get_nproc():
    with os.popen("nproc") as output:
        nproc = output.read().strip('\n')
    return int(nproc)
def bridge_exists():
    with os.popen("brctl show  | wc -l") as output:
        line_count = output.read().strip('\n')
    return int(line_count) > 1
    
    
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
def get_device_id():
    with open('/var/device_id') as f:
        text = f.read().strip('\n') 
    return int(text)
def get_cpu_load():
    with os.popen("cat /proc/loadavg | cut -d' ' -f1") as output:
        cpu_load = output.read().strip('\n')
    return float(cpu_load)

def get_poweron_time():
    cmd = '''date -d "$(awk -F. '{print $1}' /proc/uptime) second ago" +"%Y-%m-%d %H:%M:%S"'''
    with os.popen(cmd) as output:
        poweron_time = output.read().strip('\n')
    return poweron_time
def free_memory():
    cmd = "free -m | head -3 | tail -1 | awk -F' ' '{print $4}'"
    with os.popen(cmd) as output:
        free_mem = output.read().strip('\n')
    return int(free_mem)
def is_ads_enabled():
    cmd = "iptables -L -t nat -vn | grep -c 'tcp dpt:80 redir ports'" 
    with os.popen(cmd) as output:
        ret = output.read().strip('\n')
    try:
        if int(ret) > 0:
            return 1
    except:
        pass
    return 0
def get_connection_count():
    cmd = '/sbin/arp | grep -cv "incomplete"'
    with os.popen(cmd) as output:
        ret = output.read().strip('\n')
    return int(ret)
def get_hardware_type():
    nproc = get_nproc()
    neth = get_neth()
    hardware_capicity = '200'  
    if nproc == 4 and neth == 4:
        hardware_capicity = '200'
    elif nproc == 2 and neth == 6:
        hardware_capicity = '400'
    else:
        pass
    hardware_serial = 'R'
    if bridge_exists():
        hardware_serial = 'B'
    return hardware_serial + hardware_capicity
def send_beacon():
    addr = 'http://54.65.245.210/device/beacon'
    mac = get_mac_address('eth0')
    device_id = get_device_id()
    parameters = {
       'MAC': mac,
       'device_id': device_id,
       'FreeCPU': 100 - int(100 * get_cpu_load()),
       'FreeMemory': free_memory(),
       'PowerOnTime': get_poweron_time(),
       'AdsEnabled': is_ads_enabled(),
       'ConnectionCount': get_connection_count(),
       'Hardware': get_hardware_type(),
    }
    #print(json.dumps(parameters))
    req = Request(addr)
    req.add_header('Content-Type', 'application/x-www-form-urlencoded')
    try: 
        response = urlopen(req, urlencode(parameters)) 
    except HTTPError, e: 
        print "The server couldn't fulfill the request."
        print 'Error code: ', e.code 
        sys.exit(-1)
    except URLError, e: 
        print 'We failed to reach a server.' 
        print 'Reason: ', e.reason 
        sys.exit(-2)
    result = response.read()
    response.close()
    print 'Response from server: %s' % (result,)
def main():
    send_beacon()
if __name__ == "__main__":
    main()
