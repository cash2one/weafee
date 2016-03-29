import os
import shutil
import subprocess
from adv.models import *



def gen_sync_verion_file(destPath,version):
	
	sync_version_file_path = os.path.join(destPath,'data_version')	#destPath == '/data/withfi/3301/data'
	fp = open(sync_version_file_path,"w")
	fp.write(str(version))
	fp.close()



def move_global_blacklist_to_sync_path(srcPath,ad_owner):
	# srcPath = '/home/r400/router_cloud/adv/output/global_blacklist/global_blacklist'
	# full_destPath = '/data/withfi/3301/data/black/global_blacklist'
	# data_version_path = '/data/withfi/3301/data/data_version'
	rel = 0
	ret_msg = ''
	sync_type = 'global_blacklist'

	remote_sync_path = '/data/withfi/'
	private_blacklist_folder_name = 'data/black'
	global_blacklist_folder_name = 'global'
	
	
	dbconn = sync_log.objects.get(owner=ad_owner)
	version = dbconn.version + 1	
	
	
	
	if os.path.exists(srcPath):
		pathes = os.listdir(remote_sync_path)
		#print pathes		
		
		for folder_name in pathes:
			if folder_name.isdigit():
								
				full_destPath = os.path.join(remote_sync_path, os.path.join(folder_name, private_blacklist_folder_name))		
				
				if os.path.exists(os.path.join(full_destPath,'global_blacklist')):
					os.remove(os.path.join(full_destPath,'global_blacklist'))
					
				shutil.copy(srcPath,full_destPath)			
				
				data_version_path = os.path.join(remote_sync_path, os.path.join(folder_name, 'data'))
				gen_sync_verion_file(data_version_path, version)
				
			else:
				continue
		
		
		
		#insert sync log into DB	
		dbconn.version = version		
		dbconn.sync_type = sync_type
		dbconn.src_path = srcPath
		dbconn.dest_path = remote_sync_path
		dbconn.sync_dest_router_id = os.listdir(remote_sync_path)
		dbconn.sync_src_folder = pathes
		dbconn.result = 1   # 0 failed 1 success
		
		dbconn.save()
		
		rel = 1
		ret_msg = 'success'
		return rel,ret_msg
			
	else:
		rel = 0
		ret_msg = srcPath + ' -- not exist, sync failed'
		return rel,ret_msg