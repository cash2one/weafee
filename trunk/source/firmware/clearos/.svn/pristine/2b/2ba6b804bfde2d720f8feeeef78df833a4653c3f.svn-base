#/bin/sh
remote_server='withfi.com'
hostname='r400-c04'
remote_dir="~/nginx_logs/${hostname}/"
for f in /var/log/nginx/access.log-*; do
  rsync -avz $f r400@${remote_server}:${remote_dir} && rm -f $f
done
