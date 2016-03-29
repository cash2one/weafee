'''
WIthfi AutoSync Code via Rsync
'''

import os, sys, stat
import commands
import time

def exec_cmd(cmd, output_mode = 0, wait_time = 0):
    print(cmd)
    
    ret, output = commands.getstatusoutput(cmd)
#    if ret != 0:
    print(output)

    if wait_time:
        time.sleep(wait_time)

    if output_mode == 1:
        return output
    elif output_mode == 2:
        return ret, output
    else:
        return ret

def validate_config_dirs(dirs):
    for dir in dirs:
        if not os.path.isdir(dir):
                print 'Create dir %s' %dir
                os.makedirs(dir, 0755)
            
WithfiConfig = {
    'device_id_file':'/var/device_id',

    'basic_dirs':
    {
        'withfi_base_dir' : '/var/spool/withfi',
        'withfi_backup_dir' : '/tmp/backup',
        'withfi_log' : '/var/log/withfi',
        'rsync_local_dir' : '/var/rsync/withfi',
        'withfi_conf': '/etc/withfi'
    },
    
    'rsync':
    {
        'rsync_server':'r400@52.8.100.62',
        'rsync_cmd':'rsync -avz --delete --no-o --no-g',
        'rsync_base_path' : '/home/r400/release',
        'retry':3,
        'wait_time':300,
    },

    'components': {
        'firmware':
        {
            'rsync_path': 'firmware',
            'withfi_path': 'firmware',
            'version_file':'firmware_version',
            'need_upgrade':True,
            'restart_service':True,
            'extra_scripts': ['/bin/sh /var/spool/withfi/firmware/clearos/deploy.sh', ]
        },
        'data':
        {
            'rsync_path': 'routers/%(device_id)s/data',
            'withfi_path': 'data',
            'version_file':'data_version',
            'need_upgrade':True,
            'restart_service':False,
            'extra_scripts':['/usr/local/bin/python "/var/spool/withfi/firmware/BlackListUpdate.py" > "/var/log/withfi/blacklist.log"']
        }
    }
}


class WithfiUpgrade(object):
    """withfi config"""
    def __init__(self, config):    
        self.dirs = config['basic_dirs']
        self.components = config['components']
        self.rsync = config['rsync']

        self.rsync_client = RsyncClient(self.rsync)  
        validate_config_dirs(self.dirs.itervalues())

        self.device_id = self.get_id_from_file(config['device_id_file'])
        if self.device_id < 0:
            print 'Not find device id'
            raise Exception('Init WithfiUpgrade failed')

        self.components['data']['rsync_path'] = self.components['data']['rsync_path']%(dict(device_id = self.device_id))

    def get_id_from_file(self, file):
        id = ''
        if os.path.exists(file):
            id = open(file, 'r').readline()
           
        if id.strip().isdigit():
            return int(id)
        else:
            return -1

    def rsync_component_version(self, component_name):
        component = self.components[component_name]

        print ('Rsync %s version file'%component_name)
        validate_config_dirs([os.path.join(self.dirs['rsync_local_dir'], component['rsync_path'])])
        src_path = os.path.join(self.rsync['rsync_base_path'], component['rsync_path'], component['version_file']) 
        dest_path = os.path.join(self.dirs['rsync_local_dir'], component['rsync_path'])

        self.rsync_client.rsync_files(':'.join([self.rsync['rsync_server'], src_path]), dest_path)

    def get_component_version(self, component_name):
        component = self.components[component_name]

        old_verion_file = os.path.join(self.dirs['withfi_base_dir'], component['withfi_path'], component['version_file'])
        old_version = self.get_id_from_file(old_verion_file)

        new_version_file = os.path.join(self.dirs['rsync_local_dir'], component['rsync_path'], component['version_file'])
        new_version = self.get_id_from_file(new_version_file)

        return old_version, new_version

    def withfi_check_upgrade(self):
        do_upgrade = False

        for name, component in self.components.iteritems():
            try:
                self.rsync_component_version(name)
                old, new = self.get_component_version(name)

                print('Upgrade [%s] from version <%s> to version <%s>!' %(name, old, new))
                if old == -1 or new > old:
                    self.components[name]['need_upgrade'] = True
                    do_upgrade = True
                else:
                    self.components[name]['need_upgrade'] = False
            except Exception as e:
                print e
                self.components[name]['need_upgrade'] = False

        return do_upgrade

    def backup_files(self, src, dest):
        cmds = [
            'mkdir -p %s'%(dest),
            'rm -rf %s'%(dest+'/*'),
            '\cp -Rf %s %s'%(src, dest),
        ]
        for cmd in cmds:            
            ret = exec_cmd(cmd)
            if ret != 0: return -1
        return 0

    def restore_files(self):
        cmds = [
            'mkdir -p %s'%(self.dirs['withfi_base_dir']),
            'rm -rf %s'%(self.dirs['withfi_base_dir']+'/*'),
            '\cp -Rf %s %s'%(self.dirs['withfi_backup_dir'] +'/*', self.dirs['withfi_base_dir']),
        ]
        for cmd in cmds:            
            ret = exec_cmd(cmd)
            if ret != 0: return -1
        return 0

    def upgrade_apps(self, component):
        src = os.path.join(self.dirs['rsync_local_dir'], component['rsync_path'])
        dest = os.path.join(self.dirs['withfi_base_dir'], component['withfi_path'])
        cmds = [
            'rm -rf %s'%(dest +'/*'),
            'mkdir -p %s'%(dest),
            '\cp -Rf %s %s'%(src +'/*', dest + '/'),
        ]

        for cmd in cmds:            
            ret = exec_cmd(cmd)
            if ret != 0: return -1

        if component.has_key('extra_scripts'):
            for script in component['extra_scripts']:
                #script_file = os.path.join(dest, script)
                #os.chmod(script_file, stat.S_IRWXU)
                ret = exec_cmd(script)
                if ret !=0 : return -1

        return 0



    def stop_hj(self):
        if self.hj_status:
            exec_cmd("iptables -F -t nat && echo '' > /etc/clearos/firewall.d/local", wait_time = 5)

        cmds = [
                '/sbin/stop applebrowser',
                '/sbin/stop nginx-watchdog',
                '/sbin/service nginx stop',
                ]

        for cmd in cmds:            
            exec_cmd(cmd)


    def start_hj(self):
        cmds = [                
                '/sbin/service nginx restart',
                '/sbin/start applebrowser',
                '/sbin/start nginx-watchdog'
                ]

        for cmd in cmds:        
            exec_cmd(cmd)

        if self.hj_status:
            exec_cmd("/bin/sh /var/spool/withfi/firmware/clearos/firewall/local &> /dev/null && cat /var/spool/withfi/firmware/clearos/firewall/local > /etc/clearos/firewall.d/local")
        

    def heath_check(self):
        pass

    def withfi_pre_upgrade(self):
        ret = self.backup_files(self.dirs['withfi_base_dir'], self.dirs['withfi_backup_dir']) 

        if ret != 0: 
            print('Backup fail!')

        #check hj status
        output = exec_cmd("iptables -L -t nat -vn | grep -c 'tcp dpt:80 redir ports'", output_mode=1)

        if int(output) == 0:
            print 'hj status is off'
            self.hj_status = 0
        else:
            print 'hj status is on'
            self.hj_status = 1

        for name, component in self.components.iteritems():
            if component['need_upgrade']:
                validate_config_dirs([os.path.join(self.dirs['rsync_local_dir'], component['rsync_path'])])
                src_path = os.path.join(self.rsync['rsync_base_path'], component['rsync_path']) 
                dest_path = os.path.join(self.dirs['rsync_local_dir'], os.path.split(component['rsync_path'])[0])

                self.rsync_client.rsync_files(':'.join([self.rsync['rsync_server'], src_path]), dest_path)


    def withfi_start_upgrade(self):
        restart_service = False

        for name, component in self.components.iteritems():
            if component['need_upgrade']:
                print 'start upgrade [%s] component'%(name)

                if component['restart_service']:
                    print 'stop withfi services'
                    restart_service = True
                    self.stop_hj()    

                ret = self.upgrade_apps(component)
                if ret != 0:
                    raise Exception('Upgrade failed')

        if restart_service == True: self.start_hj()
        

    def withfi_post_upgrade(self):
        #self.heath_check()
        return 0



class RsyncClient(object):
    def __init__(self, config):
        self.server = config['rsync_server']
        self.rsync_cmd = config['rsync_cmd']
        self.retry = config['retry']
        self.wait_time = config['wait_time']

    def rsync_files(self, src, dest):
        cmd = ' '.join([self.rsync_cmd, src, dest])
        retry = self.retry
        #if rsync fail, retry max 3 times in 15 min
        while retry > 0:
            if exec_cmd(cmd, self.wait_time) == 0: break
            retry -= 1

        if retry == 0: raise Exception('Rsync file failed')





#need add force update option
#need add code push channel
#not update router code
def withfi_upgrade():
    print('*********************Start withfi upgrade check**************************')
    print time.strftime('%Y-%m-%d %H:%M:%S')

    client = WithfiUpgrade(WithfiConfig)
    do_upgrade = client.withfi_check_upgrade()

    if do_upgrade:
        print('Do upgrade now!')

        try: 
            print('---start pre upgrade---')
            client.withfi_pre_upgrade()

            print('---start file upgrade---')
            client.withfi_start_upgrade()

            print('---start post upgrade---')
            client.withfi_post_upgrade()

            print('Withfi upgrade success!')

        except:
            print('Withfi upgrade failed!')
    else:
        print('No need upgrade now!')

def test():
    client = WithfiUpgrade(WithfiConfig)
    #client.rsync_component_version('firmware')
    #print client.get_component_version('firmware')
    do_upgrade = client.withfi_check_upgrade()
    # for component in client.components.itervalues():
    #     print component
    if do_upgrade:
        for name, component in client.components.iteritems():
            print name, component

def main():
    withfi_upgrade()
if __name__ == '__main__':
    main()
