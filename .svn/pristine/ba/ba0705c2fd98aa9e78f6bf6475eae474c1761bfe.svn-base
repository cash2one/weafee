import os, sys, commands
def update_blacklist():
    print 'start update blacklist'
    ip_list = []
    blacklist_file = '/var/spool/withfi/data/black/global_blacklist'
    blacklist_conf = '/etc/withfi/blacklist.conf'
    
    try:
        with open(blacklist_conf, 'r') as file:
            ip_list = file.readlines()
    except:
        print 'Get black list ip from %s failed'%blacklist_conf
    
    os.system('ipset -! create blacklist hash:ip')
    
    CMD = "dig -f %s A +short|grep -P '^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5]).(\d{1,2}|1\d\d|2[0-4]\d|25[0-5]).(\d{1,2}|1\d\d|2[0-4]\d|25[0-5]).(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$'"%blacklist_file
    
    ret, output = commands.getstatusoutput(CMD)
    
    if ret == 0 and output is not None:
        try:
            with open(blacklist_conf, 'w') as file:
                file.write(output)  
    
            ip_list = output.split('\n')
            print 'update %s successfully'%blacklist_conf
        except:
            print 'update black list ip to %s failed'%blacklist_conf
    else:
        print 'Get black list ip from dns failed or black list is null'
    
    os.system('ipset flush blacklist')
    for ip in ip_list:
        print "add %s to ipset!"%(ip.strip())
        os.system('ipset -! add blacklist %s'%(ip.strip()))
def main():
    update_blacklist()
if __name__ == "__main__":
    main()