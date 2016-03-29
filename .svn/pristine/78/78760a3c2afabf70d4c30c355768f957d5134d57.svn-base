#!/usr/bin/python

import json
import datetime
import os
import shutil
import sys
import re
import util
import sqlite3


config_file = "./conf.json"
access_logs=dict()


class PATTERNS:
    #192.168.1.10 [23/Jun/2015:04:54:52 +0800] openapi.baidu.com GET /social/api/2.0/topic/info?callback=xnJSONP684326&app_id=3629560&third_source_id=3629560 HTTP/1.1 200 0.088 123 "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.124 Safari/537.36"
    #$remote_addr [$time_local] $http_host $request $status $request_time $body_bytes_sent "$http_user_agent";
    _body = re.compile(r'^(.*?) \[(.*)\] (.*?) ([A-Z]*) (\S*\??\S*) (\S*) (\d+) ([\d.]+) (\d+) (\S+)"(.*)"')

MONTH_ABBREVIATIONS = {'Jan': 1, 'Feb': 2, 'Mar': 3, 'Apr': 4,
                       'May': 5, 'Jun': 6, 'Jul': 7, 'Aug': 8,
                       'Sep': 9, 'Oct': 10, 'Nov': 11, 'Dec': 12}
def read_conf(conf_file):
    f = open(conf_file)
    o = json.load(f)
    f.close()
    return o

def whether_to_go_ahead(err_code, ignore_err = False):
    if err_code != 0 and not ignore_err:
        util.show_err("Error! code = %s" % err_code)
        sys.exit(err_code)
def main():
    util.log("Read configuration from %s ..." % config_file)
    o = read_conf(config_file)
    output_dir = o["output_dir"]
    index_spec = o["index_spec"]
    rawlog_dir = o["rawlog_dir"]
    sqlite_db_per_day = o["sqlite_db_per_day"]
    if not os.path.exists(rawlog_dir):
        os.mkdir(rawlog_dir)
    shutil.rmtree(output_dir, True)
    os.mkdir(output_dir)

    dbs = dict()

    def provision_db(output_dir, filename):
        db_name = "%s/%s" % (output_dir, filename)
        util.log("Creating database %s" % db_name)
        SQL_CREATE_TABLE_ACCESS_TEMPLATE = "CREATE TABLE %s (id INTEGER PRIMARY KEY \
                           ,  host VARCHAR(32) \
                           ,  remote_addr VARCHAR(32) \
                           ,  time_local DATETIME \
                           ,  http_host VARCHAR(256) \
                           ,  request_type VARCHAR(8) \
                           ,  request_uri VARCHAR(2048) \
                           ,  http_version VARCHAR(16) \
                           ,  status_code INTEGER \
                           ,  request_time FLOAT \
                           ,  bytes_sent INTEGER \
                           ,  content_type VARCHAR(32) \
                           ,  user_agent VARCHAR(256) \
                            );"
        conn = sqlite3.connect(db_name)
        cu = conn.cursor()
        #for table_name in ["tb_pre_accept", "tb_pre_send", "tb_pre_reject", "tb_post_accept", "tb_post_send", "tb_post_reject"]:
        #    cu.execute("drop table if exists %s;" % table_name)
        cu.execute(SQL_CREATE_TABLE_ACCESS_TEMPLATE % ("tb_access"))
        cu.execute("PRAGMA synchronous = OFF;")
        cu.execute("PRAGMA temp_store = MEMORY;")
        #util.log("Database has been created.")
        return conn, cu
    SQL_INSERT_ACCESS_TEMPLATE = "INSERT INTO tb_access(host, remote_addr, time_local, http_host, request_type, request_uri, http_version, status_code, request_time, bytes_sent, content_type, user_agent) \
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);"
    def get_domainpart(addr):
        domain = ""
        if addr is not None:
            addr_list = addr.split('@')
            if len(addr_list) > 1:
                domain = str.lower(addr_list[-1])
        return domain
    dir_files = dict()
    for root, dirs, files in os.walk(rawlog_dir):
        if root.find('.svn') == -1:
            dir_files[root] = sorted(files)
    for root in sorted(dir_files.keys()):
        for fn in dir_files[root]:
            parse(root + "/" + fn)
            for host_day, logs in access_logs.items():
                host = host_day[0]
                day = host_day[1]
                if sqlite_db_per_day:
                    db_name = "%s.db" % day
                    if not dbs.has_key(day):
                        dbs[day] = provision_db(output_dir, db_name)
                    conn, cu = dbs[day]
                else:
                    db_name = "log.db"
                    if not dbs.has_key(db_name):
                        dbs[db_name] = provision_db(output_dir, db_name)
                    conn, cu = dbs[db_name]
                util.log("Committing access logs to %s ..." % db_name)
                sql_insert_access = SQL_INSERT_ACCESS_TEMPLATE
                for log in logs:
                    cu.execute(sql_insert_access, (host, log['remote_addr'], log['time_local'], log['http_host'], log['request_type'], log['request_uri'], log['http_version'], log['status_code'], log['request_time'], log['bytes_sent'], log['content_type'], log['user_agent']))
                conn.commit()
            access_logs.clear()
    for k, v in dbs.items():
        conn, cu = v
        #print k, v
        util.log("Building index for %s.db ..." % k)
        for index_sql in index_spec:
            #print index_sql
            cu.execute(index_sql)
        conn.commit()
        conn.close()
    util.log("Done!")
def get_time_struct(when):
	#18/Jul/2012:17:00:01 +0800
	
	day = int(when[0:2])
	month = MONTH_ABBREVIATIONS[when[3:6]]
	year = int(when[7:11])
	hour = int(when[12:14])
	minute = int(when[15:17])
	second = int(when[18:20])
	#ignore timezone
	return (year, month, day, hour, minute, second)
	
	

def parse(filename):
    to_time_str = lambda time_struct: "%04d-%02d-%02d %02d:%02d:%02d" % time_struct
    to_date_str = lambda time_struct: "%4d%02d%02d" % time_struct[:3]
    util.log("Parsing %s" % filename)
    f = open(filename, "r")

    line_number = 0;
    #acceptlogs_h = dict()
    #rejectlogs_h = []
    hostname = 'n/a'
    i1 = filename.rfind('/')
    if i1 != -1:
        i2 = filename[:i1].rfind('/')
        hostname = filename[i2 + 1: i1]
    while True:
        line_number += 1
        line = f.readline()
        if len(line) == 0:
            break
        if line_number % 100000 == 0:
            util.log("Parsing line %d" % line_number)
        #print line,
        texts = PATTERNS._body.match(line)
        if texts is None:
            #print('[Unexpected Text] Line %d: %s' % (line_number, line))
            #print("Unexpected text - %d: %s" % (line_number, line))
            continue
        when = texts.group(2)

        #day_obj = datetime.datetime.now()
        #continue #debug
        time_struct = get_time_struct(when)
        #below sentance eats lots of CPU time
        #day_obj = datetime.datetime.strptime('2015 ' + when, '%Y %b %d %H:%M:%S')
        date_str = to_date_str(time_struct)
	time_str = to_time_str(time_struct)
        #date_str = '20150630'
	alog = {
            'host': hostname,
            'remote_addr': texts.group(1),
            'time_local':  time_str,
            'http_host':  texts.group(3),
            'request_type':  texts.group(4),
            'request_uri':  texts.group(5),
            'http_version':  texts.group(6),
            'status_code':  texts.group(7),
            'request_time':  texts.group(8),
            'bytes_sent':  texts.group(9),
            'content_type':  texts.group(10),
            'user_agent':  texts.group(11),
        }
        key1 = (hostname, date_str)
        if access_logs.get(key1) == None:
            access_logs[key1] = []
        access_logs_h = access_logs[key1]
        access_logs_h.append(alog)
    f.close()


if __name__ == "__main__":
    main()
