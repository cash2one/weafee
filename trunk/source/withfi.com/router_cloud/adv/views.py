#coding=utf-8
from django.shortcuts import render
from django.http import HttpResponse, HttpResponseRedirect
import ImageFile
import json
import os
import time
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.views import logout_then_login
from django.views.decorators.csrf import csrf_exempt, csrf_protect
from django.shortcuts import render_to_response
#from django import forms
import django.forms as forms
from adv.models import *
from adv.upload_file_model import *
from adv.global_blacklist_model import *
from adv.adctl_model import *
from adv.sync_ad_model import *


@login_required
def adv(request):
    #return HttpResponse("Hello, world. You're at the adv's index.")
    context = {}
    return render(request, 'adv_manager.html', context)


@login_required
def global_blacklist_show(request):
    #return HttpResponse("global_blacklist_show")
    file_name = 'global_blacklist'
    #file_path = 'output/global_blacklist/'
    file_path = '/home/r400/router_cloud/adv/output/global_blacklist'

    #file_full_path = file_path + file_name
    file_full_path = os.path.join(file_path, file_name)
    global_blacklist_tuple = []
    if os.path.isfile(file_full_path):
        with open(file_full_path, "r") as ins:
            for line in ins:
                url = line.strip('\n')
                global_blacklist_tuple.append(url)
    response_arr = {}
    response_arr['ret_code'] = 'success'
    response_arr['global_blacklist'] = global_blacklist_tuple
    response_json = json.dumps(response_arr)
    return HttpResponse(response_json)

@login_required
def global_blacklist_submit(request):
    #return HttpResponse("global_blacklist_submit")
    rel = 0
    ret_msg = ''
    response_arr = {};
    ad_owner = 'admin'

    file_name = 'global_blacklist'
    file_path = '/home/r400/router_cloud/adv/output/global_blacklist'

    #file_full_path = file_path + file_name
    file_full_path = os.path.join(file_path, file_name)

    #if os.path.isfile(file_full_path):
    #	os.remove(file_full_path)

    blacklist_arr = json.loads(request.POST['blacklist'])


    #data = ["http://baidu.com", "http://xxx.com", "http://bing.com"]
    str = ''
    for url in blacklist_arr:
        str = str + url + '\n'

    fp = open(file_full_path,"w")
    fp.write(str)
    fp.flush()

    #move global_blacklist to router sync path
    #srcPath = os.path.join(file_path, file_name)
    rel,ret_msg = move_global_blacklist_to_sync_path(file_full_path,ad_owner)
    #return HttpResponse(ret_msg)

    if rel == 0:
        response_arr['ret_code'] = 'failed'
        response_arr['ret_msg'] = ret_msg
    else:
        response_arr['ret_code'] = 'success'
        response_arr['ret_msg'] = 'success'

    response_json = json.dumps(response_arr)
    return HttpResponse(response_json)

@login_required	
def upload_file(request):
    #return HttpResponse('upload_file')
    #return HttpResponse(os.getcwd())   #/home/r400/router_cloud/adv
    #rel = upload_pic(request,'upfile','./output')
    rel = upload_pic(request,'upfile','./adv/tmp')
    return HttpResponse(rel)

@login_required	
def add_new_ads(request):
    if not (request.user.is_superuser or request.user.username == 'jpz'):
        obj = {'ret_code': 'failed', 'ret_msg': 'Permission denied.'}
        return HttpResponse(json.dumps(obj))
    response_arr = {}
    rel = 0
    ret_msg = ' '
    #return HttpResponse(os.getcwd())   #/home/r400/router_cloud/adv
    #return HttpResponse('add_new_ads')
    if request.method != 'POST':
        response_arr['ret_code'] = 'failed'
        response_arr['ret_msg'] = 'Invalid request method, POST is prefer'
        response_json = json.dumps(response_arr)
        return HttpResponse(response_json)
    else:
        rel,ret_msg = generate_ad_package(request)
        #return HttpResponse(ret_msg)

        if rel == 1:
            response_arr['ret_code'] = 'success'
        else:
            response_arr['ret_code'] = 'failed'
            response_arr['ret_msg'] = ret_msg

        response_json = json.dumps(response_arr)
        return HttpResponse(response_json)


@login_required	
def delete_ads(request):
    response_arr = {}
    ad_owner = 'admin'
    ad_no = int(request.POST['ad_no'])  # request.POST['ad_no'] type is unicode, therefore translate it into int
    rel,ret_msg = delete_ad(ad_no,ad_owner)

    if rel == 0:
        response_arr['ret_code'] = 'success'
    else:
        response_arr['ret_code'] = 'failed'
        response_arr['ret_msg'] = 'Delete failed,please try again'

    response_json = json.dumps(response_arr)
    return HttpResponse(response_json)


@login_required
def edit_ads(request):
    return HttpResponse('edit_ads')


@login_required
def show_ads(request):
    response_json = {}
    if request.user.is_superuser or request.user.username == 'jpz':
        ad_owner = 'admin'
        response_json = get_ad_data(ad_owner)
    return HttpResponse(response_json)


@login_required
def sync_ads(request):
    if not (request.user.is_superuser or request.user.username == 'jpz'):
        obj = {'ret_code': 'failed', 'ret_msg': 'Permission denied'}
        return HttpResponse(json.dumps(obj))
    response_arr = {}
    if request.method != 'GET':
        response_arr['ret_code'] = 'failed'
        response_arr['ret_msg'] = 'Invalid request method, GET is prefer'
        response_json = json.dumps(response_arr)
        return HttpResponse(response_json)
    else:
        ad_owner = 'admin'
        #ret_msg = move_ads_to_sync_path(ad_owner)
        rel,ret_msg = move_ads_to_sync_path(ad_owner)
        #return HttpResponse(ret_msg)
        if rel == 1:
            response_arr['ret_code'] = 'success'
        else:
            response_arr['ret_code'] = 'failed'
            response_arr['ret_msg'] = ret_msg
        response_json = json.dumps(response_arr)
        return HttpResponse(response_json)


@login_required
def unitest(request):
    return HttpResponse("unitest")
    if request.POST.has_key('blacklist'):
        blacklist_arr['global_blacklist'] = json.loads(request.POST['blacklist'])
    response_arr = {}
    #response_arr['ret_code'] = 'success'
    response_arr['ret_code'] = blacklist_arr['global_blacklist']
    response_json = json.dumps(response_arr)
    return HttpResponse(response_json)
