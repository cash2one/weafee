#!/bin/sh
`rm -rf /var/spool/cron/root`
echo "*/5 * * * * /bin/sh /root/disk_monitor.sh &>/dev/null" >> "/var/spool/cron/root"
echo "*/5 * * * * /bin/sh /root/nginx_log_monitor.sh &>/dev/null" >> "/var/spool/cron/root"
