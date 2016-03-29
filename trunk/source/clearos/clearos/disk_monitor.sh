#!/bin/sh
base_dir='/var/spool/withfi/firmware/clearos'

if [ `df -m / | tail -1 | awk -F' ' '{print $3}'` -le 500 ] ; then
  python $base_dir/sendmail.py 'smtp.ym.163.com'
fi
