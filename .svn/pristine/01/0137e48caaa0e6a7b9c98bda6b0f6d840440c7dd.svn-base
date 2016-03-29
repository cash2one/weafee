prereq: manage.py
	chmod +x manage.py
start:
	nohup python ./manage.py runserver 0.0.0.0:8000 &> run.log &
status:
	@a=`pidof python`; if [ $$? -eq 0 ]; then echo $$a is running; else echo Stoped; fi
stop:
	kill -9 `pidof python` || true
restart: stop start
reset_device_id:
	echo 'ALTER TABLE device_device AUTO_INCREMENT=3318;' | mysql -uroot router;
install:
	pip install pyyaml ua-parser user-agents PIL
init:
	python ./manage.py makemigrations
	python ./manage.py migrate
	python ./manage.py createsuperuser
