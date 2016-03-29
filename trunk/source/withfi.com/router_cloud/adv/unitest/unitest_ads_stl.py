import os
import datetime
import time




'''
new_ad_type	5
popup_budget	2000
popup_name	Popup Name
popup_pic_path	./output/1443624408.55.jpg
popup_relocate_url	http://baidu.com
popup_target_country[]	3
popup_target_country[]	5
popup_target_routers[]	1
popup_target_terminal_os[]	2
popup_target_terminal_os[]	3
popup_target_website_qual[]   2
popup_timerange	 09/10/2015 12:00-11/11/2015 12:00

'''






def gen_popup_ad_html(request,ads_folder_name):
	
	#tmplate = '<div id="withfi_float" style="width: 225px; height: 142px; position: fixed; bottom: 10px;  right:30px ; z-index: 2147483647"  > <a href="http://www.alibaba.com" target="_blank" onclick="document.getElementById(\'withfi_float\').style.display=\'none\'"> <img width="220px" border="0" height="140px" src="/withficode/popup/7.jpg">   </a>  <div  style="cursor: pointer"   onclick="javascript:document.getElementById(\'withfi_float\').style.display=\'none\'" >  <img style="width:15px; height: 15px; position:absolute; top:1px; right: 1px" src="/withficode/popup/closebtn.png" >    </div></div>', 
	tmplate = '<div id="withfi_float" style="width: 225px; height: 142px; position: fixed; bottom: 10px;  right:30px ; z-index: 2147483647"  > <a href="' + request['popup_relocate_url'] + '" target="_blank" onclick="document.getElementById(\'withfi_float\').style.display=\'none\'"> <img width="220px" border="0" height="140px" src="'+ request['popup_pic_path'] +'">   </a>  <div  style="cursor: pointer"   onclick="javascript:document.getElementById(\'withfi_float\').style.display=\'none\'" >  <img style="width:15px; height: 15px; position:absolute; top:1px; right: 1px" src="/withficode/popup/closebtn.png" >    </div></div>', 
	
	ad_arr = {};
	ad_arr['adhtml'] = tmplate
	ad_json = json.dumps(ad_arr)	
	
	#write ad_json into file
	file_name = ads_folder_name	
	if os.path.isfile(file_name):
		os.remove(file_name)
	else:
		fp = open(file_name,"w")
		fp.write(ad_json)	
		fp.flush()
	return
	
	
	
	
	
request = {};

request['popup_name'] = 'popup_name'
request['popup_relocate_url'] = 'http://baidu.com'
request['popup_pic_path'] = './output/1443624408.55.jpg'


ads_folder_name = str(time.time())

print ads_folder_name

output_path = 'adv/output/ad'
ads_folder_full_path = os.path.join(output_path, ads_folder_name)

print ads_folder_full_path


os.mkdir(ads_folder_full_path)
exit

gen_popup_ad_html(request,ads_folder_name)











