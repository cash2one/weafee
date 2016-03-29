all:
	python setup.py build_ext --inplace
clean:
	rm -rf *.c *.so build/
release:all
	cp -rf apple_browser.py beacon.py BlackListUpdate.py register.py withfiupgrade.py *.so clearos ../firmware
scp:all
	scp apple_browser.py beacon.py BlackListUpdate.py withfiupgrade.py apple_browser.so beacon.so BlackListUpdate.so withfiupgrade.so r400@tunnel.withfi.com:~/release/firmware/
prepare:
	yum install gcc && pip install Cython
