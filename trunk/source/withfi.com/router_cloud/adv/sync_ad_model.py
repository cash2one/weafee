#coding=utf-8
import os
import shutil
import datetime
import time
import json
from django.http import HttpResponse
from adv.models import *



def get_ad_info(ad_owner):
	dbconn = Ad.objects.filter(owner=ad_owner).order_by("id")
	return dbconn
	

	
	
def copy_comm_ad(ad_owner):
	rel = 0
	ret_msg = ''
	
	output_path = '/home/r400/router_cloud/adv/output'
	srcPath_404 = '/home/r400/router_cloud/adv/output/comm_ad/404'
	destPath_404 = os.path.join(os.path.join(output_path, ad_owner),'ad/404')
	
	srcPath_ios = '/home/r400/router_cloud/adv/output/comm_ad/ios'
	destPath_ios = os.path.join(os.path.join(output_path, ad_owner),'ad/ios')
	
	#copy 404 ad
	if os.path.exists(destPath_404):
		shutil.rmtree(destPath_404)
	
	shutil.copytree(srcPath_404,destPath_404)
	
	#copy ios ad
	if os.path.exists(destPath_ios):
		shutil.rmtree(destPath_ios)
	
	shutil.copytree(srcPath_ios,destPath_ios)
	
	rel = 1
	ret_msg = 'success'
	return rel,ret_msg
	
	


def gen_router_ad_controlor(ad_obj_list,ad_owner):	
	#return 'gen_router_ad_controlor'
	
	rel = 0
	ret_msg = ''
	
	ad_andriod_str = '("'
	ad_ios_str = '("'
	ad_pc_str = '("'
	
	#start gen ad for ad_andriod_str	
	for ad_obj in ad_obj_list:
		target_terminal_os = json.loads(ad_obj.target_terminal_os)
		for terminal_os in target_terminal_os:
			if terminal_os == '1':
				ad_andriod_str = ad_andriod_str + ad_obj.folder_name + '","'
			elif terminal_os == '2':
				ad_ios_str = ad_ios_str + ad_obj.folder_name + '","'
			elif terminal_os == '3':
				ad_pc_str = ad_pc_str + ad_obj.folder_name + '","'
			else:
				continue
	
	#ad_andriod_str = ad_andriod_str + '")'
	#ad_ios_str = ad_ios_str + '")'
	#ad_pc_str = ad_pc_str + '")'
	
	ad_andriod_str = ad_andriod_str[:-2] + ')'
	ad_ios_str = ad_ios_str[:-2] + ')'
	ad_pc_str = ad_pc_str[:-2] + ')'
	
	template = '''
		var ad_info = {
			proj: ""
		};

		(function (win, doc, undefined) {
			window.miwifi_toolbar_info = window.miwifi_toolbar_info || {};
			var CONF = {
				device_info: "/withficode/miwifi_toolbar_info.js",
				jquery: "/withficode/com_src/jquery.min.js",
				monitor: "/withficode/com_src/withfi_monitor.js"
			};

			var isMobile = (function () {
				return {
					Android: function () {
						return navigator.userAgent.match(/Android/i)
					},
					BlackBerry: function () {
						return navigator.userAgent.match(/BlackBerry/i)
					},
					iOS: function () {
						return navigator.userAgent.match(/iPhone|iPad|iPod/i)
					},
					Opera: function () {
						return navigator.userAgent.match(/Opera Mini/i)
					},
					Windows: function () {
						return navigator.userAgent.match(/IEMobile/i)
					},
					any: function () {
						return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows())
					}
				}
			}());
			var isIframe = function () {
				return win.self !== win.top
			};
			var loadJs = function (url, callback, options) {
				options = options || {};
				var head = document.getElementsByTagName("head")[0] || document.documentElement,
						script = document.createElement("script"),
						done = false;
				script.src = url;
				if (options.charset) {
					script.charset = options.charset
				}
				if ("async" in options) {
					script.async = options["async"] || ""
				}
				script.onerror = script.onload = script.onreadystatechange = function () {
					if (!done && (!this.readyState || this.readyState == "loaded" || this.readyState == "complete")) {
						done = true;
						if (callback) {
							callback()
						}
						script.onerror = script.onload = script.onreadystatechange = null;
						head.removeChild(script)
					}
				};
				head.insertBefore(script, head.firstChild)
			};
			var init = function () {

				loadJs(CONF.device_info,
						function () {
						}
				);

				if (isIframe()) {
					return;
				} else {
					loadJs(CONF.jquery,
							function () {
								var wjq = $.noConflict(true);
								
								
								

								if (isMobile.any()) {
									wjq.getScript("/withficode/com_src/ggj.js");
									loadJs(CONF.monitor,
													function () {
														MIWIFI_MONITOR.setProject("banner");
														if (isMobile.any()) {
															MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
														} else {
															MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
														}
													});
									//wjq.getScript("/withficode/com_src/zhengbang.js");
									
									/*load icon ad from com_src*/
									/*
									var url_dest = '/withficode/com_src/icon/ad';
									wjq.ajax({
										type: "GET",
										url: url_dest,
										dataType: "json",
										success: function (resp) {
											if (resp.adtype) {
												ad_info.proj = resp.adtype;
											}
											wjq("body").prepend(resp.adhtml);
											loadJs(CONF.monitor,
													function () {
														MIWIFI_MONITOR.setProject(ad_info.proj);
														if (isMobile.any()) {
															MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
														} else {
															MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
														}
													})
										},
										error: function (resp) {
										}
									});
									*/
									/*end load icon ad from com_src*/

									if (isMobile.iOS()) {
										//IOS
										var bid_url = 'http://admin.withfi.com/bid?device_id=' + miwifi_toolbar_info.device_id;
										wjq.ajax({
											//async: false,
											type: "GET",
											url: bid_url,
											dataType: "jsonp",
											jsonp: "bidjsonp",
											success: function (resp) {
												var url_dest = '/withficode/' + resp.adhtml + '/ad';
												wjq.ajax({
													type: "GET",
													url: url_dest,
													dataType: "json",
													success: function (resp) {
														if (resp.adtype) {
															ad_info.proj = resp.adtype;
														}
														wjq("body").prepend(resp.adhtml);
														loadJs(CONF.monitor,
																function () {
																	MIWIFI_MONITOR.setProject(ad_info.proj);
																	//MIWIFI_MONITOR.setProject('tt');
																	if (isMobile.any()) {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	} else {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	}
																})
													},
													error: function (resp) {
														return;
													}
												});
											},
											error: function (resp) {

												var ad_array = new Array''' + ad_ios_str + ''';

												var ad_number = ad_array.length;
												var ad_id = Math.floor(Math.random() * ad_number);

												var url_dest = '/withficode/' + ad_array[ad_id] + '/ad';

												wjq.ajax({
													type: "GET",
													url: url_dest,
													dataType: "json",
													success: function (resp) {
														if (resp.adtype) {
															ad_info.proj = resp.adtype;
														}
														wjq("body").prepend(resp.adhtml);
														loadJs(CONF.monitor,
																function () {
																	MIWIFI_MONITOR.setProject(ad_info.proj);
																	//MIWIFI_MONITOR.setProject('tt');
																	if (isMobile.any()) {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	} else {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	}
																})
													},
													error: function (resp) {
														return;
													}
												});
											}

										});

									} else {
										//andriod and other cell phone
										var bid_url = 'http://admin.withfi.com/bid?device_id=' + miwifi_toolbar_info.device_id;
										wjq.ajax({
											//async: false,
											type: "GET",
											url: bid_url,
											dataType: "jsonp",
											jsonp: "bidjsonp",
											success: function (resp) {
												var url_dest = '/withficode/' + resp.adhtml + '/ad';
												wjq.ajax({
													type: "GET",
													url: url_dest,
													dataType: "json",
													success: function (resp) {
														if (resp.adtype) {
															ad_info.proj = resp.adtype;
														}
														wjq("body").prepend(resp.adhtml);
														loadJs(CONF.monitor,
																function () {
																	MIWIFI_MONITOR.setProject(ad_info.proj);
																	//MIWIFI_MONITOR.setProject('tt');
																	if (isMobile.any()) {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	} else {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	}
																})
													},
													error: function (resp) {
														return;
													}
												});
											},
											error: function (resp) {

												var ad_array = new Array''' + ad_andriod_str + ''';

												var ad_number = ad_array.length;
												var ad_id = Math.floor(Math.random() * ad_number);

												var url_dest = '/withficode/' + ad_array[ad_id] + '/ad';

												wjq.ajax({
													type: "GET",
													url: url_dest,
													dataType: "json",
													success: function (resp) {
														if (resp.adtype) {
															ad_info.proj = resp.adtype;
														}
														wjq("body").prepend(resp.adhtml);
														loadJs(CONF.monitor,
																function () {
																	MIWIFI_MONITOR.setProject(ad_info.proj);
																	//MIWIFI_MONITOR.setProject('tt');
																	if (isMobile.any()) {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	} else {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	}
																})
													},
													error: function (resp) {
														return;
													}
												});
											}
										});
									}

								} else {
									//pc
									
									//wjq.getScript("/withficode/com_src/zhengbang_pc.js"); // get zhegnbang pc ssp ad
									wjq.getScript("/withficode/com_src/ggj_pc.js");
									loadJs(CONF.monitor,
													function () {
														MIWIFI_MONITOR.setProject("couplet");
														if (isMobile.any()) {
															MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
														} else {
															MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
														}
													});
									
									var bid_url = 'http://admin.withfi.com/bid?device_id=' + miwifi_toolbar_info.device_id;
									wjq.ajax({
										//async: false,
										type: "GET",
										url: bid_url,
										dataType: "jsonp",
										jsonp: "bidjsonp",
										success: function (resp) {
											var url_dest = '/withficode/' + resp.adhtml + '/ad';
												wjq.ajax({
													type: "GET",
													url: url_dest,
													dataType: "json",
													success: function (resp) {
														if (resp.adtype) {
															ad_info.proj = resp.adtype;
														}
														wjq("body").prepend(resp.adhtml);
														loadJs(CONF.monitor,
																function () {
																	MIWIFI_MONITOR.setProject(ad_info.proj);
																	//MIWIFI_MONITOR.setProject('tt');
																	if (isMobile.any()) {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	} else {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	}
																})
													},
													error: function (resp) {
														return;
													}
												});
										},
										error: function (resp) {
											var ad_array = new Array''' + ad_pc_str + ''';
											var ad_number = ad_array.length;

											var ad_id = Math.floor(Math.random() * ad_number);

											var url_dest = '/withficode/' + ad_array[ad_id] + '/ad';

											wjq.ajax({
												type: "GET",
												url: url_dest,
												dataType: "json",
												success: function (resp) {
													if (resp.adtype) {
														ad_info.proj = resp.adtype;
													}
													wjq("body").prepend(resp.adhtml);
													loadJs(CONF.monitor,
																function () {
																	MIWIFI_MONITOR.setProject(ad_info.proj);
																	//MIWIFI_MONITOR.setProject('tt');
																	if (isMobile.any()) {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	} else {
																		MIWIFI_MONITOR.log(wjq.extend(MIWIFI_MONITOR.data.getTrack(), miwifi_toolbar_info), "track")
																	}
																})
												},
												error: function (resp) {
													return;
												}
											});
										}

									});

								}

							})


				}

			};

			init()

		})(window, document, undefined);
	
	'''	
	
	#copy comm_src to ads_folder_full_path
	output_path = '/home/r400/router_cloud/adv/output'
	srcPath = '/home/r400/router_cloud/adv/output/com_src'
		
	dest_com_src_full_path = os.path.join(os.path.join(output_path, ad_owner),'ad/com_src')
	
	if os.path.exists(dest_com_src_full_path):
		shutil.rmtree(dest_com_src_full_path)
	
	shutil.copytree(srcPath,dest_com_src_full_path)
	
	#write template into file
	dest_ad_controlor_full_path = os.path.join(os.path.join(output_path, ad_owner),'ad/withfi_ad.js')
		
	fp = open(dest_ad_controlor_full_path,"w")
	fp.write(template)
	fp.close()
	
	rel = 1
	ret_msg = 'success'
	return rel,ret_msg
	


def gen_sync_verion_file(destPath,version):
	sync_version_file_path = os.path.join(destPath,'data_version')
	#if os.path.isfile(os.path.join(destPath,'timestamp')):
	#	os.remove(os.path.join(destPath,'timestamp'))
				
	fp = open(sync_version_file_path,"w")
	fp.write(str(version))
	fp.close()
	


def move_ads_to_sync_path(ad_owner):
	#return 'move_ads_to_sync_path'
	#return HttpResponse(os.getcwd())   #/home/r400/router_cloud/adv
	
	sync_type = 'ad'
	rel = 0
	ret_msg = ''
	
	srcPath = os.path.join(os.path.join('/home/r400/router_cloud/adv/output', ad_owner),'ad')
	#srcPath = os.path.join(os.path.join('adv/output', ad_owner),'ad')
	
	sync_src_folder = os.listdir(srcPath)	
	
	ad_obj_list = get_ad_info(ad_owner)
	
	rel,ret_msg = gen_router_ad_controlor(ad_obj_list,ad_owner)
	
	if rel==0:
		ret_msg = 'gen_router_ad_controlor failed,return'
		return rel,ret_msg	
	
	#copy comm ad
	rel,ret_msg = copy_comm_ad(ad_owner)
	if rel==0:
		ret_msg = 'copy comm_ad failed,return'
		return rel,ret_msg
	#end copy comm ad
	
	dest_sync_root_path = '/data/withfi'
	
	if not os.path.exists(dest_sync_root_path):
		#return rel,ret_msg
		ret_msg = 'dest_sync_root_path is not exist, failed to sync ad [' + dest_sync_root_path + ']'
		rel = 0
		return rel,ret_msg
		
	pathes = os.listdir(dest_sync_root_path)  # get all the folder names
	
	dbconn = sync_log.objects.get(owner=ad_owner)
	version = dbconn.version + 1	
	
	for folder_name in pathes:
		if folder_name.isdigit():
			#router sync folder,start to copy			
			destPath1 = os.path.join(os.path.join(dest_sync_root_path, folder_name),'data/')
			destPath = os.path.join(destPath1,'ad')
			
			if os.path.exists(destPath):
				shutil.rmtree(destPath)
				
			shutil.copytree(srcPath,destPath)
			gen_sync_verion_file(destPath1, version)			
			
		else:
			continue
			
	#insert sync log into DB
	
	src_path = srcPath
	dest_path = dest_sync_root_path
	sync_dest_router_id = pathes
	#sync_src_folder = os.listdir(srcPath)
	result = 1 # 0 failed 1 success
	
	#dbconn = sync_log(owner=ad_owner,sync_type=sync_type,src_path=src_path , dest_path=dest_path , sync_dest_router_id=sync_dest_router_id ,  sync_src_folder=sync_src_folder , version=version , result=result)
	#dbconn.save()
		
	
	dbconn.version = version
	dbconn.sync_type = sync_type
			
	dbconn.src_path = src_path
	dbconn.dest_path = dest_path
	dbconn.sync_dest_router_id = sync_dest_router_id
	dbconn.sync_src_folder = sync_src_folder
	dbconn.result = result
	
	dbconn.save()	
	# end db operate
	
	rel = 1
	ret_msg = 'success'
	return rel,ret_msg

		
		
		
		
		
		
		
		
		
		
	
	
	
