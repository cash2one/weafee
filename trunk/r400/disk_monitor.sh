#!/bin/sh
if [ `df -m / | tail -1 | awk -F' ' '{print $3}'` -le 500 ] ; then
  $HOME/sendmail.py localhost
fi
