import os
import shutil

def move_global_blacklist_to_sync_path(srcPath):
	#remote_sync_path = '/data/withfi/'
	remote_sync_path = '/data/test/'
	private_blacklist_folder_name = 'black'
	global_blacklist_folder_name = 'global'
	if os.path.exists(srcPath):
		pathes = os.listdir(remote_sync_path)
		#print pathes		
		
		for folder_name in pathes:
			if folder_name == global_blacklist_folder_name:
				continue
			else:
				folder_name = os.path.join(folder_name, private_blacklist_folder_name)
				#print folder_name
				full_destPath = os.path.join(remote_sync_path, folder_name)
				#print full_destPath			
				if os.path.exists(full_destPath):
					shutil.copy(srcPath,full_destPath)
					continue
				else:
					print full_destPath + ' -- not exist,sync failed'
					continue
					
	else:
		print srcPath + ' -- not exist, sync failed'
		return

srcPath = 'output/global_blacklist/global_blacklist'
move_global_blacklist_to_sync_path(srcPath)