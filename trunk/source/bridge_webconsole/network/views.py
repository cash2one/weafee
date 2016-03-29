from django.contrib.auth.models import User
from django.contrib.auth.decorators import login_required
from django.http import HttpResponse
import json
from django.shortcuts import render
from django.core.exceptions import ObjectDoesNotExist
import sys
import os
import uuid
import re

interface = 'br0'
@login_required
def index(request):
    user = request.user
    return render(request, 'network.html', {})

@login_required
def info(request):
    user = request.user
    obj = {}
    if request.method == 'GET':
        obj = {
             'device_id': get_device_id(),
             'hardware': get_hardware_type(),
             'wan_ip': get_ip_address(interface),
             'mac': get_mac_address(),
             }
    return HttpResponse(json.dumps(obj))
@login_required
def wan_setting(request):
    user = request.user
    obj = {}
    if request.method == 'GET':
        obj = read_wan_setting()
    elif request.method == 'POST':
        if write_wan_setting(request.POST):
            obj = {"ret_code":"success"}
        else:
            obj = {"ret_code":"failed"}
        #obj = set_wan_setting(request.POST)
    else:
        pass
    return HttpResponse(json.dumps(obj))

def read_wan_setting():
    setting = {}
    def set_if_not_empty(key, value, obj):
        if value is not None and len(value.strip()) > 0:
            obj[key] = '%s' % (value.strip('"'),)
    os_setting = read_os_network_setting(interface)
    BOOTPROTO = os_setting.get('BOOTPROTO')
    if BOOTPROTO is not None:
        if BOOTPROTO == '"dhcp"':
            setting['BOOTPROTO'] = 1
            for key in ['DNS1', 'DNS2']:
                set_if_not_empty(key, os_setting.get(key), setting)
        elif BOOTPROTO == '"static"':
            setting['BOOTPROTO'] = 2
            for key in ['DNS1', 'DNS2', 'IPADDR', 'NETMASK', 'GATEWAY']:
                set_if_not_empty(key, os_setting.get(key), setting)
        else:
            pass
    return setting


def write_wan_setting(setting):
    os_setting = read_os_network_setting(interface)
    for key in ['DNS1', 'DNS2', 'IPADDR', 'IPADDR', 'NETMASK', 'GATEWAY']:
        if os_setting.has_key(key):
            del os_setting[key]

    def is_ipv4_addr(ip):
        try:
            pattern = r"^([1-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))\.(([0-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))\.){2}([0-9]|([1-9]\d)|(1\d\d)|(2([0-4]\d|5[0-5])))$"
            return re.match(pattern, ip.strip()) is not None
            #return all(0 <= int(byte) < 256 for byte in ip.rstrip().split('.'))
        except:
            return False

    def set_if_not_empty(key, value, obj):
        if value is not None and len(value.strip()) > 0:
            obj[key] = '"%s"' % (value.strip(),)

    def set_ip_if_valid(key, value, obj):
        if is_ipv4_addr(value):
           set_if_not_empty(key, value, obj)
    not_empty = lambda x: (x is not None and len(x.strip()) > 0)
    is_reserved_ip = lambda x: x is not None and re.match(r'172\.16\.20\..*', x)

    BOOTPROTO = setting.get('BOOTPROTO')

    def perfrom():
        write_os_network_setting(interface, os_setting)
        cmd = 'service network restart'
        ret = ''
        with os.popen(cmd) as output:
            ret = output.read().strip('\n')
    if not_empty(BOOTPROTO):
        if int(setting['BOOTPROTO']) == 1:
            os_setting['BOOTPROTO'] = '"dhcp"'
            for key in ['DNS1', 'DNS2']:
                set_ip_if_valid(key, setting.get(key), os_setting)
            perfrom()
        elif int(setting['BOOTPROTO']) == 2 and not is_reserved_ip(setting.get('IPADDR')):
            os_setting['BOOTPROTO'] = '"static"'
            for key in ['IPADDR', 'NETMASK', 'GATEWAY']:
                if not is_ipv4_addr(setting.get(key)):
                    return False
            for key in ['DNS1', 'DNS2', 'IPADDR', 'NETMASK', 'GATEWAY']:
                set_ip_if_valid(key, setting.get(key), os_setting)
            perfrom()
        else:
            return False
    return True

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
def get_mac_address():
    node = uuid.getnode()
    mac_hex = uuid.UUID(int = node).hex[-12:]
    return ":".join([mac_hex[i: i + 2] for i in range(0, 11, 2)]).upper()
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
    cmd = "grep -cP '^iptables -t nat ' /etc/clearos/firewall.d/local"
    with os.popen(cmd) as output:
        ret = output.read().strip('\n')
    if ret == '2':
        return 1
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
def get_ip_address(interface):
    cmd = "ifconfig %s | grep -oP '(?<=inet addr:)[0-9.]*(?= )'" % (interface)
    ret = ''
    with os.popen(cmd) as output:
        ret = output.read().strip('\n')
    if len(ret) < len('0.0.0.0'):
        ret = '0.0.0.0'
    return ret
def read_os_network_setting(interface):
    setting_file = '/etc/sysconfig/network-scripts/ifcfg-%s' % (interface,)
    setting = dict()
    for line in open(setting_file): 
         k, v = [word.strip() for word in line.strip().split('=')]
         setting[k] = v
    return setting
def write_os_network_setting(interface, setting):
    setting_file = '/etc/sysconfig/network-scripts/ifcfg-%s' % (interface,)
    setting_lines = ['%s=%s\n' % (k, v) for (k, v) in setting.items()]
    with open(setting_file, 'w') as f:
        f.writelines(setting_lines)
