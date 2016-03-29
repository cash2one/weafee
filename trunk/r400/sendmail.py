#!/usr/bin/python
import socket
import sys
import smtplib
from email.mime.text import MIMEText

MailBody = '''
Dear withfi:
[%s] disk is almost full, please handle it right now!

'''%(socket.gethostname())


def send_notiry_mail(server):
    sender = "nick@withfi.com"
    rcpts = ["mengdong6257@163.com"]
    msg = MIMEText(MailBody,'plain','ascii')
    msg['Subject'] = "Disk is almost full"
    msg['From'] = sender
    msg['To'] = ",".join(rcpts)
    #print(len(rcpts))
    print(msg.as_string())
    smtp = smtplib.SMTP()
    smtp.connect(server)
    smtp.login('nick@withfi.com', '16269927')
    smtp.sendmail(sender, rcpts, msg.as_string())
    smtp.quit()

def main():
    if len(sys.argv) == 2:
        server = sys.argv[1]
        send_notiry_mail(server)
    else:
        sys.exit(1)
if __name__ == "__main__":
    main()
