port:=80
server:=0.0.0.0
python:=/usr/local/bin/python
prereq: manage.py
	chmod +x manage.py
start:
	nohup $(python) ./manage.py runserver $(server):$(port) &> run.log &
status:
	@pid=`netstat -nplt | grep $(port) | grep -oP '\d*(?=/python)'`; then echo $$pid is running; else echo Stoped; fi
stop:
	@pid=`netstat -nplt | grep $(port) | grep -oP '\d*(?=/python)'`;  if [ $$? -eq 0 ]; then kill -9 $$pid && echo 'Stopped'; fi
restart: stop start
install:
	pip install pyyaml ua-parser user-agents PIL
init:
	$(python) ./manage.py makemigrations
	$(python) ./manage.py migrate
	$(python) ./manage.py createsuperuser
debug:
	sed -i 's/^\(DEBUG\).*=.*/\1 = True/' bridge/settings.py
release:
	sed -i 's/^\(DEBUG\).*=.*/\1 = False/' bridge/settings.py
