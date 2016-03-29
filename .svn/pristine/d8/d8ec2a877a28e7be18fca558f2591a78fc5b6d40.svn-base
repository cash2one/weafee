#!/usr/bin/env python
from pymongo import MongoClient, DESCENDING
from bson.json_util import dumps
from bson import BSON
from bson.code import Code
import threading
import json
import re
import logging
from datetime import timedelta
from datetime import datetime
#from django.utils import timezone
def get_collection_dao(server, database_name, collection_name):
    conn = MongoClient(server, 27017)
    db = conn[database_name]
    collection = db[collection_name]
    return conn, collection
def get_database_dao(server, database_name):
    conn = MongoClient(server, 27017)
    db = conn[database_name]
    return conn, db
def gen_collection_names(time_to, days, collection_name_prefix = 'access'):
    the_time = time_to
    time_range = []
    day = 0
    while day < days:
        time1 = the_time.strftime('%Y%m%d')
        the_time = the_time - timedelta(days = 1)
        day += 1
        time_range.append(time1)
    names = map(lambda x: '_'.join((collection_name_prefix, x)), time_range)
    return names[::-1]
def main():
    conn, collection = get_collection_dao(server='127.0.0.1', database_name = 'probe', collection_name = 'access')
    query_spec = {}
    results = []
    records = collection.find(query_spec).limit(20)
    for r in records:
        results.append(r)
    conn.close()
    print dumps(results, indent = 2)
def global_blacklist_show():
    conn, db = get_database_dao(server='127.0.0.1', database_name = 'global_blacklist')
    all_collection_names = db.collection_names()
    my_collection_names = gen_collection_names(datetime.now(), 7)
    pipeline = [ 
        { '$group': { '_id': "$id"}  },
        { '$group': { '_id': 1, 'count': { '$sum': 1 } } }
    ];

    dates = []
    UV = []
    PV = []
    for collection_name in my_collection_names:
        if collection_name not in all_collection_names:
            continue
        dates.append(collection_name.split('_')[1][4:])
        collection = db[collection_name]
        PV.append(collection.count())
        ret = collection.aggregate(pipeline)
        for r in ret:
            UV.append(r['count'])
            break
        #PV.append(collection.aggregate(pipeline)['result'][0]['count'])
    conn.close()
    obj = {'category': dates, 'UV': UV, 'PV': PV}
    return obj
def global_blacklist_submit():
	
	obj = {'data': dates, 'UV': UV, 'PV': PV}
	return obj
    
if __name__ == "__main__":
    obj = get_source_summary()
    print dumps(obj, indent = 2)
