#!/bin/sh
source /root/.bash_profile
ln='ln -sf'
base_dir='/var/spool/withfi/firmware/clearos'

chmod +x ${base_dir}/rc.local
$ln ${base_dir}/rc.local /etc/rc.d/rc.local

#[nick] fix symlink is not work in upstart
rm -rf /etc/init/applebrowser.conf 
rm -rf /etc/init/nginx-watchdog.conf 
\cp -f ${base_dir}/applebrowser.conf /etc/init/
\cp -f ${base_dir}/nginx-watchdog.conf /etc/init/


chmod +x ${base_dir}/nginx
$ln ${base_dir}/nginx /etc/init.d/
chmod +x ${base_dir}/autosshd
$ln ${base_dir}/autosshd /etc/init.d/autosshd1
/sbin/chkconfig --add autosshd1
$ln ${base_dir}/autosshd /etc/init.d/autosshd2
/sbin/chkconfig --add autosshd2
chmod +x ${base_dir}/sync_log.sh

chmod +x ${base_dir}/bashrc
\cp -f ${base_dir}/bashrc ~/.bashrc
#echo ". ${base_dir}/bashrc" >> ~/.bashrc

$ln ${base_dir}/clearos-release /etc/
$ln ${base_dir}/issue /etc/
$ln ${base_dir}/issue.net /etc/
$ln ${base_dir}/product /etc/
$ln ${base_dir}/grub.conf /boot/grub/
$ln ${base_dir}/nginx.conf /etc/nginx/nginx.conf
$ln ${base_dir}/tranparent_proxy.conf /etc/nginx/conf.d/tranparent_proxy.conf
rm -f /etc/nginx/conf.d/{default.conf,example_ssl.conf}
$ln ${base_dir}/apple_browser.conf /etc/nginx/conf.d/apple_browser.conf

rm -rf /var/spool/cron/root
crontab -u root ${base_dir}/crontab

#[nick] fix dns bug of clearos
chmod +x ${base_dir}/ifup-post
$ln ${base_dir}/ifup-post /etc/sysconfig/network-scripts/ifup-post
chmod +x ${base_dir}/ifdown-post
$ln ${base_dir}/ifdown-post /etc/sysconfig/network-scripts/ifdown-post


#[nick] add blacklist feature
$ln ${base_dir}/blacklist/modprobe.d/clearos.conf /etc/modprobe.d/clearos.conf