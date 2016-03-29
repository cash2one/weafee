import json
import os
import commands
import tempfile
from routerapp.models import *
from django.utils import timezone

log_file = 'cmd_log.txt'
def log_cmd(text):
    output = open(log_file, 'a')
    output.write(text)
    output.write('\n')
    output.close()

def local_exec(cmds, enable_log_cmd = True, break_if_err = True, sudo = True) :
    tempfile_name = tempfile.mktemp()
    if enable_log_cmd: log_cmd(tempfile_name)
    file_fd = open(tempfile_name, 'w')
    file_fd.write('#!/usr/bin/sh')
    file_fd.write('\n')
    for cmd in cmds:
        if enable_log_cmd: log_cmd(cmd)
        file_fd.write(cmd)
        file_fd.write('\n')
    file_fd.close()
    exec_cmd = 'sh %s' % (tempfile_name,)
    if sudo:
        exec_cmd = 'sudo ' + exec_cmd
    if enable_log_cmd:
        log_cmd(exec_cmd)
    #ret = os.system(exec_cmd)
    ret, output = commands.getstatusoutput(exec_cmd)
    log_cmd(str(ret) + ' ' + output)
    return ret
def take_effect_setting(category):
    ret, msg = -1, 'stub'
    setting = Setting.objects.get(category = category)
    if setting is None:
        return (1, "undefined category")
    if category == 'wan':
        ret, msg = take_effect_wan(setting.value)
    elif category == 'lan':
        ret, msg = take_effect_lan(setting.value)
    else:
        log_cmd('no such category in backend' % (category,))
        return (1, 'no such category in backend')
    setting.last_execute_time = timezone.now()
    setting.last_result = ret 
    setting.save()
    return ret, msg

def get_dns_setting_cmd(setting, filename):
    cmds = []
    dns = setting.get('dns1')
    if dns is not None and len(dns.strip()) > 0:
        str1 = 'DNS1=%s' % dns.strip()
        cmd = "sed -i '/DNS1=/d' %s" % (filename, )
        cmds.append(cmd)
        cmd = "sed -i '$ a %s' %s" % (str1, filename)
        cmds.append(cmd)
    dns = setting.get('dns2')
    if dns is not None and len(dns.strip()) > 0:
        str1 = 'DNS2=%s' % dns.strip()
        cmd = "sed -i '/DNS2=/d' %s" % (filename, )
        cmds.append(cmd)
        cmd = "sed -i '$ a %s' %s" % (str1, filename)
        cmds.append(cmd)
    return cmds
def get_mtu_setting_cmd(setting, cmds, filename):
    mtu = setting.get('mtu')
    if mtu is not None:
        cmd = 'echo %d > /sys/class/net/%s/mtu' % (mtu, setting['phy_interface'])
        cmds.append(cmd)

def set_key_value(key, value, filename):
    cmds = []
    cmd = "sed -i '/%s/d' %s" % (key, filename)
    cmds.append(cmd)
    cmd = "sed -i '$ a %s=%s' %s" % (key, value, filename)
    cmds.append(cmd)
    return cmds
def take_effect_wan(setting_str):
    settings = json.loads(setting_str)
    cmds = []
    for setting in settings:
        filename = '/etc/sysconfig/network-scripts/ifcfg-%s' % setting['logic_interface']
        cmds.append('ifdown ppp0')
        cmds.append('ifdown eth0')
        cmds = cmds + get_dns_setting_cmd(setting, filename)
        #get_mtu_setting_cmd(setting, cmds, filename)
        cmds = cmds + set_key_value('BOOTPROTO', setting['wan_type'], filename)
        if setting.get('mtu') is not None and len(str(setting['mtu']).strip()) > 0:
            cmds = cmds + set_key_value('MTU', str(setting['mtu']).strip(), filename)

        if setting['wan_type'] == 'dhcp':
            cmds = cmds + set_key_value('ONBOOT', 'yes', filename)
            cmds.append('for f in /etc/sysconfig/network-scripts/ifcfg-ppp*; do sed -i "s/ONBOOT/ONBOOT=no/" $f; done')
            cmds.append('ifup eth0')
            pass # no any more additional setting
        elif setting['wan_type'] == 'static':
            cmds = cmds + set_key_value('IPADDR', setting['static_ip'], filename)
            cmds = cmds + set_key_value('NETMASK', setting['static_netmask'], filename)
            cmds = cmds + set_key_value('GATEWAY', setting['static_gateway'], filename)
            cmds = cmds + set_key_value('ONBOOT', 'yes', filename)
            cmds.append('for f in /etc/sysconfig/network-scripts/ifcfg-ppp*; do sed -i "s/ONBOOT/ONBOOT=no/" $f; done')
            cmds.append('ifup eth0')
        elif setting['wan_type'] == 'pppoe':
            cmds = cmds + set_key_value('ETH', setting['phy_interface'], filename)
            cmds = cmds + set_key_value('USER', setting['username'], filename)
            filename2 = '/etc/sysconfig/network-scripts/ifcfg-%s' % setting['phy_interface']
            cmds = cmds + set_key_value('ONBOOT', 'no', filename2)
            cmds.append('for f in /etc/sysconfig/network-scripts/ifcfg-ppp0; do sed -i "s/ONBOOT/ONBOOT=no/" $f; done')
            cmds.append('ifup ppp0')
            #FIXME how to change these 2 files?
            #/etc/ppp/chap-secrets
            #/etc/ppp/pap-secrets
        else:
            pass
    #cmds.append('service network restart')
    cmds.append('service dhcpd restart')
    cmds.append('service dnsmasq restart')
    ret = local_exec(cmds)
    if ret == 0:
        return (0, 'success')
    else:
        return (ret, 'execute wan setting failed')
def take_effect_lan(setting_str):
    return (1, "not implemented. ignored")
