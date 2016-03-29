#coding=utf-8
import os
import shutil
import datetime
import time
import json
from django.http import HttpResponse
from adv.models import *



'''
new_ad_type	
5
popup_budget	
2000
popup_name	
Popup Name
popup_pic_path	
./output/1443624408.55.jpg
popup_relocate_url	
http://baidu.com
popup_target_country[]	
3
popup_target_country[]	
5
popup_target_routers[]	
1
popup_target_terminal_os[...	
2
popup_target_terminal_os[...	
3
popup_target_website_qual...	
2
popup_timerange	
09/10/2015 12:00-11/11/2015 12:00
'''


def move_ads_to_sync_path(srcPath):

	remote_sync_path = '/data/withfi/'
	private_blacklist_folder_name = 'ad'
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


def gen_popup_ad(request,ads_folder_full_path,ads_folder_name,ad_owner):
	
	rel = 0
	err_msg = ''
	
	ad_file_name = 'ad'
	ad_file_path = os.path.join(ads_folder_full_path, ad_file_name)
	production_env_path = os.path.join('/withficode', ads_folder_name)
	
	production_env_relocation_url = request.POST['popup_relocate_url']
	
	tmp_arr = request.POST['popup_pic_path'].split('/')
	img_name = tmp_arr[(len(tmp_arr)-1)]
	production_env_img_path = os.path.join(production_env_path, img_name)  #  /withficode/1443807421.52/1443806475.17.jpg	
	
	production_env_close_btn_img_path = os.path.join(production_env_path, 'closebtn.png') #/withficode/1443807421.52/closebtn.png
	
	#tmplate = '<div id="withfi_float" style="width: 225px; height: 142px; position: fixed; bottom: 10px;  right:30px ; z-index: 2147483647"  > <a href="http://www.alibaba.com" target="_blank" onclick="document.getElementById(\'withfi_float\').style.display=\'none\'"> <img width="220px" border="0" height="140px" src="/withficode/popup/7.jpg">   </a>  <div  style="cursor: pointer"   onclick="javascript:document.getElementById(\'withfi_float\').style.display=\'none\'" >  <img style="width:15px; height: 15px; position:absolute; top:1px; right: 1px" src="/withficode/popup/closebtn.png" >    </div></div>', 
	template = '<div id="withfi_float" style="width: 225px; height: 142px; position: fixed; bottom: 10px;  left:30px ; z-index: 2147483647"  > <a href="' + production_env_relocation_url + '" target="_blank" onclick="document.getElementById(\'withfi_float\').style.display=\'none\'"> <img width="220px" border="0" height="140px" src="'+ production_env_img_path +'">   </a>  <div  style="cursor: pointer"   onclick="javascript:document.getElementById(\'withfi_float\').style.display=\'none\'" >  <img style="width:15px; height: 15px; position:absolute; top:1px; right: 1px" src="'+ production_env_close_btn_img_path +'" >    </div></div>', 
	
	ad_arr = {};
	ad_arr['adtype'] = 'popup'
	ad_arr['adhtml'] = template
	ad_json = json.dumps(ad_arr)	
	
	#write ad_json into file
	
	#print ad_file_path	
	if os.path.isfile(ad_file_path):
		os.remove(ad_file_path)
	
	fp = open(ad_file_path,"w")
	fp.write(ad_json)	
	fp.flush()	 
	# end write ad_json into file
	
	#copy ad material	
	srcPath = request.POST['popup_pic_path']
	
	if not os.path.exists(srcPath):
		rel = 0
		err_msg = 'copy failed,srcPath not exist [ ' + srcPath + ' ]'
		return rel,err_msg
	#print srcPath	
	if not os.path.exists(ads_folder_full_path):
		rel = 0
		err_msg = 'copy failed, ads_folder_full_path not exist [ ' + ads_folder_full_path + ' ]'
		return rel,err_msg
		
	shutil.copy(srcPath,ads_folder_full_path)
	shutil.copy('adv/output/com_src/closebtn.png',ads_folder_full_path)	#copy comm img for ad	
	#end copy ad material
	
	
	
	#insert ad info into DB
	'''
	<!--option value="1">Bahrain</option>
    <option value="2">Brazil</option-->
    <option value="3">Cambodia</option>
    <option value="4">China</option>
    <option value="5">Indonesia</option>
    <!--option value="6">Saudi Arabia</option-->
    <option value="7">Thailand</option>
    <!--option value="8">Vietnam</option-->
	'''	
	ad_type = int(request.POST['new_ad_type'])
	ad_name = request.POST['popup_name']
	ad_relocate_url = request.POST['popup_relocate_url']
	target_terminal_os = request.POST['popup_target_terminal_os']
	ad_timerange = request.POST['popup_timerange'].split('_')  # ad start time  && end time	
	ad_target_website_quality = int(request.POST['popup_target_website_quality'])
	ad_target_country = request.POST['popup_target_country']
	ad_target_routers = request.POST['popup_target_routers']  # 1 == all routers	
	ad_budget = int(request.POST['popup_budget'])
	
	
	
	dbconn = Ad(owner=ad_owner,entity='',base_folder =ads_folder_full_path,folder_name =ads_folder_name,ad_type=ad_type,ad_name=ad_name,ad_relocate_url=ad_relocate_url,target_terminal_os=target_terminal_os,ad_start_timerange=ad_timerange[0],ad_end_timerange=ad_timerange[1],ad_target_website_quality=ad_target_website_quality,ad_target_country=ad_target_country,ad_target_routers=ad_target_routers,ad_budget=ad_budget)
	dbconn.save()	
	#end insert ad info into DB
	
	rel=1
	err_msg = 'success'
	return rel,err_msg
	

def gen_couplet_ad(request,ads_folder_full_path,ads_folder_name,ad_owner):
	rel = 0
	err_msg = ''
	
	ad_file_name = 'ad'
	ad_file_path = os.path.join(ads_folder_full_path, ad_file_name)
	
	relocate_url = request.POST['couplet_relocate_url']
	
	production_env_path = os.path.join('/withficode', ads_folder_name)
	
	tmp_arr = request.POST['couplet_pic_path'].split('/')
	img_name = tmp_arr[(len(tmp_arr)-1)]
	production_env_img_path = os.path.join(production_env_path, img_name)  #  /withficode/1443807421.52/1443806475.17.jpg	
	
	production_env_close_btn_img_path = os.path.join(production_env_path, 'closebtn.png') #/withficode/1443807421.52/closebtn.png
	
	
	
	#template = '<div id="withfi_couplet_left" style="width: 90px; height: 200px; position: fixed; bottom: 35%;  left:0px ; z-index: 9999"  >  <a href="http://www.alibaba.com" target="_blank" onclick="document.getElementById(\'withfi_couplet_left\').style.display=\'none\'">  <img style="width: 90px; border:0; height: 200px" src="/withficode/couplet/1.gif">     </a>   <div  style="cursor: pointer"   onclick="javascript:document.getElementById(\'withfi_couplet_left\').style.display=\'none\'" >   <img style="width:20px; height: 20px; position:absolute; top:5px; right: 0" src="/withficode/couplet/closebtn.png">  </div>    </div>   <div id="withfi_couplet_right" style="width: 90px; height: 200px; position: fixed; bottom: 35%;  right:0px ; z-index: 9999"  >  <a href="http://www.alibaba.com" target="_blank" onclick="document.getElementById(\'withfi_couplet_right\').style.display=\'none\'"> <img style="width: 90px; border:0; height: 200px" src="/withficode/couplet/1.gif">  </a>   <div  style="cursor: pointer"   onclick="javascript:document.getElementById(\'withfi_couplet_right\').style.display=\'none\'" >  <img style="width:20px; height: 20px; position:absolute; top:5px; right: 0" src="/withficode/couplet/closebtn.png">  </div> </div>'
	template = '<div id="withfi_couplet_left" style="width: 90px; height: 200px; position: fixed; bottom: 35%;  left:0px ; z-index: 9999"  >  <a href="' + relocate_url + '" target="_blank" onclick="document.getElementById(\'withfi_couplet_left\').style.display=\'none\'">  <img style="width: 90px; border:0; height: 200px" src="'+  production_env_img_path +'">     </a>   <div  style="cursor: pointer"   onclick="javascript:document.getElementById(\'withfi_couplet_left\').style.display=\'none\'" >   <img style="width:20px; height: 20px; position:absolute; top:5px; right: 0" src="'+ production_env_close_btn_img_path +'">  </div>    </div>   <div id="withfi_couplet_right" style="width: 90px; height: 200px; position: fixed; bottom: 35%;  right:0px ; z-index: 9999"  >  <a href="' + relocate_url + '" target="_blank" onclick="document.getElementById(\'withfi_couplet_right\').style.display=\'none\'"> <img style="width: 90px; border:0; height: 200px" src="'+ production_env_img_path +'">  </a>   <div  style="cursor: pointer"   onclick="javascript:document.getElementById(\'withfi_couplet_right\').style.display=\'none\'" >  <img style="width:20px; height: 20px; position:absolute; top:5px; right: 0" src="'+ production_env_close_btn_img_path +'">  </div> </div>'
	
	
	ad_arr = {};
	ad_arr['adtype'] = 'couplet'
	ad_arr['adhtml'] = template
	ad_json = json.dumps(ad_arr)	
	
	#write ad_json into file
	
	#print ad_file_path	
	if os.path.isfile(ad_file_path):
		os.remove(ad_file_path)
	
	fp = open(ad_file_path,"w")
	fp.write(ad_json)	
	fp.flush()	 
	# end write ad_json into file
	
	#copy ad material	
	srcPath = request.POST['couplet_pic_path']
	
	if not os.path.exists(srcPath):
		rel = 0
		err_msg = 'copy failed,srcPath not exist [ ' + srcPath + ' ]'
		return rel,err_msg
	#print srcPath	
	if not os.path.exists(ads_folder_full_path):
		rel = 0
		err_msg = 'copy failed, ads_folder_full_path not exist [ ' + ads_folder_full_path + ' ]'
		return rel,err_msg
		
	shutil.copy(srcPath,ads_folder_full_path)	
	shutil.copy('adv/output/com_src/closebtn.png',ads_folder_full_path)	#copy comm img for ad
	#end copy ad material
	
	
	
	#insert ad info into DB
	'''
	<!--option value="1">Bahrain</option>
    <option value="2">Brazil</option-->
    <option value="3">Cambodia</option>
    <option value="4">China</option>
    <option value="5">Indonesia</option>
    <!--option value="6">Saudi Arabia</option-->
    <option value="7">Thailand</option>
    <!--option value="8">Vietnam</option-->
	'''
	ad_type = int(request.POST['new_ad_type'])
	ad_name = request.POST['couplet_name']	
	ad_relocate_url = request.POST['couplet_relocate_url']
	target_terminal_os = request.POST['couplet_target_terminal_os']
	ad_timerange = request.POST['couplet_timerange'].split('_')  # ad start time  && end time	
	ad_target_website_quality = int(request.POST['couplet_target_website_quality'])
	ad_target_country = request.POST['couplet_target_country']
	ad_target_routers = request.POST['couplet_target_routers']  # 1 == all routers	
	ad_budget = int(request.POST['couplet_budget'])
	
	dbconn = Ad(owner=ad_owner,entity='',base_folder =ads_folder_full_path,folder_name =ads_folder_name,ad_type=ad_type,ad_name=ad_name,ad_relocate_url=ad_relocate_url,target_terminal_os=target_terminal_os,ad_start_timerange=ad_timerange[0],ad_end_timerange=ad_timerange[1],ad_target_website_quality=ad_target_website_quality,ad_target_country=ad_target_country,ad_target_routers=ad_target_routers,ad_budget=ad_budget)
	dbconn.save()	
	#end insert ad info into DB
	
	rel=1
	err_msg = 'success'
	return rel,err_msg

def gen_banner_ad(request,ads_folder_full_path,ads_folder_name,ad_owner):
	
	rel = 0
	err_msg = ''
	
	ad_file_name = 'ad'
	ad_file_path = os.path.join(ads_folder_full_path, ad_file_name)
	
	relocate_url = request.POST['banner_relocate_url']
	banner_title = request.POST['banner_title']
	banner_content = request.POST['banner_content']
	
	production_env_path = os.path.join('/withficode', ads_folder_name)
	
	tmp_arr = request.POST['banner_pic_path'].split('/')	
	img_name = tmp_arr[(len(tmp_arr)-1)]
	
	production_env_img_path = os.path.join(production_env_path, img_name)  #  /withficode/1443807421.52/1443806475.17.jpg	
	
	production_env_close_btn_img_path = os.path.join(production_env_path, 'closebtn.png') #/withficode/1443807421.52/closebtn.png
	
	#template = '<div id="toolbarbox" style="background-color: rgb(52, 58, 71);bottom: 0;color: #fff;height: 55px;left: 0;position: fixed;width: 100%;z-index: 1000000000;"> <div style="margin: auto;padding: 7px 0;position: relative;width: 380px; "><div style="color: #fff;height: 50px;padding: 0 5px 0 62px;position: relative; text-decoration: none;" > <img src="'+ production_env_img_path +'" style="position:absolute;top:7px; left:20px; width:36px; border-radius:5px; "> <p style=" font-size:16px;line-height:16px; margin:0;padding:6px 0 0; ">'+ banner_title +'</p><p style=" font-size:10px;line-height:10px;opacity:.6;margin:0;padding:7px 0 0;">'+ banner_content +'</p><a style="display:block; position:absolute;color:#fff; top:12px; right:40px; width:50px; height:25px; background-color:#f84c4b; font-size:14px; line-height:25px; text-align:center;font-weight:600; text-decoration: none;border-radius: 4px; " href="'+ relocate_url +'" target="_Blank" onclick="document.getElementById(\'toolbarbox\').style.display=\'none\';">Click</a><a id="toolbarhide" style="position:absolute; right:0; top:0;height:50px;width:45px; display:block; z-index:2147483647;" href="javascript:void(0);" onclick="document.getElementById(\'toolbarbox\').style.display=\'none\';"><img src="'+ production_env_close_btn_img_path +'" style=" position:absolute;top:15px;left:20px;width:18px;border-radius:5px;"></a></div></div></div>'
	template = '<div id="toolbarbox" style="background-color: rgb(52, 58, 71);bottom: 0;position: fixed;width: 100%;z-index: 1000000000; "><div style="color: #fff;height: 50px;padding: 0 5px 0 62px;position: relative; text-decoration: none;" > <img src="'+ production_env_img_path +'" style="position:absolute;top:7px; left:20px; width:36px; border-radius:5px; "><p style=" font-size:16px;line-height:16px; margin:0;padding:6px 0 0; ">'+ banner_title +'</p> <p style=" font-size:10px;line-height:10px;opacity:.6;margin:0;padding:7px 0 0;">'+ banner_content +'</p> <a style="display:block; position:absolute;color:#fff; top:12px; right:40px; width:50px; height:25px; background-color:#f84c4b; font-size:14px; line-height:25px; text-align:center;font-weight:600; text-decoration: none;border-radius: 4px; " href="'+ relocate_url +'" target="_Blank" onclick="document.getElementById(\'toolbarbox\').style.display=\'none\';">Click</a> <a id="toolbarhide" style="position:absolute; right:0; top:0;height:50px;width:45px; display:block; z-index:2147483647;" href="javascript:void(0);" onclick="document.getElementById(\'toolbarbox\').style.display=\'none\';"><img src="'+ production_env_close_btn_img_path +'" style=" position:absolute;top:15px;left:20px;width:18px;border-radius:5px;"></a></div></div>'
	'''
	banner_budget	
	2333
	banner_content	
	Banner Content
	banner_name	
	Banner Name
	banner_pic_path	
	./output/1443806475.17.jpg
	banner_relocate_url	
	http://baidu.com
	banner_target_country	
	["3","5"]
	banner_target_routers	
	["1"]
	banner_target_terminal_os	
	["1","2"]
	banner_target_website_qua...	
	1
	banner_timerange	
	10/03/2015 12:00-10/03/2015 11:59
	banner_title	
	Banner Title
	new_ad_type	
	1
	'''	
	
	
	ad_arr = {};
	ad_arr['adtype'] = 'banner'
	ad_arr['adhtml'] = template
	ad_json = json.dumps(ad_arr)	
	
	#write ad_json into file
	
	#print ad_file_path	
	if os.path.isfile(ad_file_path):
		os.remove(ad_file_path)
	
	fp = open(ad_file_path,"w")
	fp.write(ad_json)	
	fp.flush()	 
	# end write ad_json into file
	
	#copy ad material	
	srcPath = request.POST['banner_pic_path']
	
	if not os.path.exists(srcPath):
		rel = 0
		err_msg = 'copy failed,srcPath not exist [ ' + srcPath + ' ]'
		return rel,err_msg
		
	#print srcPath	
	if not os.path.exists(ads_folder_full_path):
		rel = 0
		err_msg = 'copy failed, ads_folder_full_path not exist [ ' + ads_folder_full_path + ' ]'
		return rel,err_msg
	
	shutil.copy(srcPath,ads_folder_full_path)
	
	shutil.copy('adv/output/com_src/closebtn.png',ads_folder_full_path)	#copy comm img for ad
	#end copy ad material	
	
	#insert ad info into DB
	'''
	<!--option value="1">Bahrain</option>
    <option value="2">Brazil</option-->
    <option value="3">Cambodia</option>
    <option value="4">China</option>
    <option value="5">Indonesia</option>
    <!--option value="6">Saudi Arabia</option-->
    <option value="7">Thailand</option>
    <!--option value="8">Vietnam</option-->
	'''
	ad_type = int(request.POST['new_ad_type'])
	ad_name = request.POST['banner_name']	
	ad_relocate_url = request.POST['banner_relocate_url']
	target_terminal_os = request.POST['banner_target_terminal_os']
	ad_timerange = request.POST['banner_timerange'].split('_')  # ad start time  && end time	
	ad_target_website_quality = int(request.POST['banner_target_website_quality'])
	ad_target_country = request.POST['banner_target_country']
	ad_target_routers = request.POST['banner_target_routers']  # 1 == all routers	
	ad_budget = int(request.POST['banner_budget'])
	
	dbconn = Ad(owner=ad_owner,entity='',base_folder=ads_folder_full_path,folder_name=ads_folder_name,ad_type=ad_type,ad_name=ad_name,ad_relocate_url=ad_relocate_url,target_terminal_os=target_terminal_os,ad_start_timerange=ad_timerange[0],ad_end_timerange=ad_timerange[1],ad_target_website_quality=ad_target_website_quality,ad_target_country=ad_target_country,ad_target_routers=ad_target_routers,ad_budget=ad_budget)
	dbconn.save()
	#end insert ad info into DB
	
	rel=1
	err_msg = 'success'
	return rel,err_msg

	
		
def generate_ad_package(request):
	#return 0,HttpResponse(os.getcwd())   #/home/r400/router_cloud/adv	
	#return HttpResponse(request.POST['popup_timerange'])
	
	response_arr = {};
	ads_folder_name = str(time.time())
	output_path = 'adv/output'
	ad_owner = 'admin'
	output_path = os.path.join(os.path.join(output_path, ad_owner),'ad')
	
	ads_folder_full_path = os.path.join(output_path, ads_folder_name)
	
	os.makedirs(ads_folder_full_path)
	
	new_ad_type = int(request.POST['new_ad_type'])  # type of request.POST['new_ad_type'] is unicode, therefore, turn it to int
	

	if new_ad_type == 1:		
		#banner	
		rel,err_msg = gen_banner_ad(request,ads_folder_full_path,ads_folder_name,ad_owner)
		return rel,err_msg

	elif new_ad_type == 4:
		#couplet
		rel,err_msg = gen_couplet_ad(request,ads_folder_full_path,ads_folder_name,ad_owner)
		return rel,err_msg
		
	elif new_ad_type == 5:
		#popup
		rel,err_msg = gen_popup_ad(request,ads_folder_full_path,ads_folder_name,ad_owner)	
		return rel,err_msg
		
	else:
		rel = 0
		err_msg = 'unknown new_ad_type = [' + request.POST['new_ad_type'] + ']'
		return rel,err_msg

		
def get_ad_data(ad_owner):
	ad_owner = 'admin'
	dbconn = Ad.objects.filter(owner=ad_owner).order_by("id")
	obj_str = [ad.ToJson() for ad in dbconn]
	return HttpResponse('[' + ','.join(obj_str) + ']')
	#return HttpResponse(obj_str)
	
def delete_ad(ad_no,ad_owner):
	rel = 0
	ret_msg = 'success'
	#delete ad folder
	#dbconn = Ad.objects.filter(ad_owner=ad_owner,id=ad_no)
	
	dbconn = Ad.objects.get(owner=ad_owner,id=ad_no)
	#若广告folder存在，删除
	if os.path.exists(dbconn.base_folder):
		shutil.rmtree(dbconn.base_folder)
	
	Ad.objects.filter(owner=ad_owner,id=ad_no).delete()
	
	return rel,ret_msg
	
		
		

	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
	
	
	
