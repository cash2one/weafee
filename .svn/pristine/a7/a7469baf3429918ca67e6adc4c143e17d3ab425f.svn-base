# tweak max open files
sed -i '/^#\s*End of file/d;/soft\s*nofile/d;/hard\s*nofile/d' /etc/security/limits.conf
echo '*                soft    nofile          10240' >> /etc/security/limits.conf
echo '*                hard    nofile          10240' >> /etc/security/limits.conf
echo '# End of file' >> /etc/security/limits.conf