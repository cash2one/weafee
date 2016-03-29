.PHONY: sync parse all
all: sync parse
sync:
	@for i in {3..3}; do rsync --rsh="ssh -p220$$i" -av root@localhost:/var/log/nginx/access* rawlogs/C0$$i/; done
gunzip:
	for i in `find rawlogs/ -name '*.gz'`; do gunzip $$i; done
parse:
	./parser.py
syncerrlogs:
	@for i in {3..3}; do rsync --rsh="ssh -p220$$i" -av root@localhost:/var/log/nginx/error* errlogs/C0$$i/; done
