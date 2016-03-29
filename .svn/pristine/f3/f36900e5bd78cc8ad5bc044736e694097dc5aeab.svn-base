from django.db import models
from django.utils import timezone
import json
import datetime
import time
import os


"""
	request: the http request object
	upload_filename: the file input name <input id="imgid" name="upfile" type="file" >   
	file_path: the location storage path for upload file 
"""
def upload_pic(request,upload_filename,file_path):
	#return 'upload_pic'
	#upload_filename = 'upfile'	
	#file_path = './output'
	file_obj = request.FILES.get(upload_filename, None)
	if file_obj == None:
		response_arr = {};
		response_arr['ret_code'] = 'failed'
		response_arr['ret_msg'] = 'file not exist in request'
		response_json = json.dumps(response_arr)
		return response_json
	else:
		timestamp = time.time()
		file_name = str(timestamp) + '.jpg'
		file_full_path = os.path.join(file_path, file_name)
		dest = open(file_full_path,'wb+')
		#return 'tt'
		dest.write(file_obj.read())
		dest.close()
		response_arr = {};
		response_arr['ret_code'] = 'success'
		response_arr['ret_msg'] = file_full_path
		response_json = json.dumps(response_arr)
		return response_json

