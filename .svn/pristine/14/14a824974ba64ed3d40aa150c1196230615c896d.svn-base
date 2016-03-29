.PHONY: run init debug
PYTHON=/usr/local/bin/python
iqiyi: init
	${PYTHON} /usr/local/bin/mitmdump -q --spoof -p 8888 -s /root/videos/iqiyi.py
#Add cronjob
#*/5 * * * * find /dev/shm/videos/other/20151215/54/64/ -type f  -cmin +5 -exec rm -f {} \; &> /dev/null
iqiyid: init
	${PYTHON} /usr/local/bin/mitmproxy --spoof -p 8888 -s /root/videos/iqiyi.py
qq: init
	${PYTHON} /usr/local/bin/mitmdump -q --spoof -p 8890 -s /root/videos/qq.py
#Add cronjob
#*/5 * * * * find /dev/shm/videos/other/20151215/54/64/ -type f  -cmin +5 -exec rm -f {} \; &> /dev/null
qqd: init
	${PYTHON} /usr/local/bin/mitmproxy --spoof -p 8890 -s /root/videos/qq.py
debug: init
	${PYTHON} /usr/local/bin/mitmproxy --spoof -p 9999 -s /root/videos/qq.py
init:
	mkdir -p /dev/shm/videos/other/20151215/54/64
	mkdir -p /dev/shm/videos/creative
	cp -rf /root/videos/creative/* /dev/shm/videos/creative/
