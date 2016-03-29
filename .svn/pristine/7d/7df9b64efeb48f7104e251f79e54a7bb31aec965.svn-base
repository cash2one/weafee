#!/bin/sh
log_file="/var/log/nginx/access.log"
size=`ls -lrt $log_file | cut -d " " -f 5`  
if [ $size -ge 104857600 ]; then  
  /usr/sbin/logrotate /etc/logrotate.conf
fi  
