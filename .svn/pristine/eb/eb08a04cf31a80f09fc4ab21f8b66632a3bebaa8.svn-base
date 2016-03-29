#!/usr/bin/python
import os
import time

#preinstall_cmds = ["if rpm -q postfix &>/dev/null ; then rpm -e postfix --nodeps; fi"]
preinstall_cmds = []
#preinstall_cmds = ["service iptables stop",
#                   "chkconfig --del iptables"]
                  #"chmod -x /etc/init.d/iptables"]

class bcolors:
    HEADER = '\033[95m'
    OKBLUE = '\033[94m'
    OKGREEN = '\033[92m'
    WARNING = '\033[93m'
    FAIL = '\033[91m'
    ENDC = '\033[0m'

    def disable(self):
        self.HEADER = ''
        self.OKBLUE = ''
        self.OKGREEN = ''
        self.WARNING = ''
        self.FAIL = ''
        self.ENDC = ''

def log(msg, enable_color=True, indent_level = 0, indent_width = 4, tee_file= './run.log'):
    now_str = time.strftime('%Y-%m-%d %H:%M:%S',time.localtime(time.time()))
    log = ""
    indents = " " * indent_level * indent_width
    plain_log = "[" + now_str + "] " + indents + msg
    if enable_color:
        log = "[" + now_str + "] " + indents + bcolors.HEADER + msg + bcolors.ENDC
    else:
        log = plain_log
    print(log)
    log_fd = open(tee_file, 'a')
    log_fd.write(plain_log + "\n")
    log_fd.close()
def header(msg):
    log(msg, enable_color = True, indent_level = 1)
def show_info(msg):
    os.system("echo \"%s\"" % (bcolors.OKBLUE + msg + bcolors.ENDC))
def show_err(msg):
    os.system("echo \'%s\'" % (bcolors.FAIL + msg + bcolors.ENDC))
def ssh_exec(host, cmd, print_cmd = True):
    #if print_cmd: header("running %s on %s" % (cmd, host))
    cmd = "/usr/bin/ssh -o 'StrictHostKeyChecking=no'  root@%s \"%s\""%(host, cmd)
    if print_cmd: header(cmd)
    return os.system(cmd)
def local_exec(cmds, print_cmd = True, break_if_err = True) :
    for cmd in cmds:
        if print_cmd: show_info(cmd)
        ret = os.system(cmd)
        if ret != 0 and break_if_err:
            break
    return ret

