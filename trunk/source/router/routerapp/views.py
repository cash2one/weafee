"""
    routerapp view.py
"""
from django.shortcuts import render
from django.http import HttpResponse
from django.http import HttpResponseRedirect
from django.shortcuts import render_to_response
from django.template.loader import get_template
from django.template import Context
from django.contrib.auth.views import logout_then_login
from django.contrib.auth.views import redirect_to_login
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.contrib.auth import authenticate, login, logout
from django.conf import settings
from django.shortcuts import redirect
from routerapp.models import *

import json
import helper

def index(request):        
    if request.user.is_authenticated():
        return render(request, "dashboard.html", {})
    else:
        return render(request, "login.html", {})    
    
def logout(request):    
    #return HttpResponse("LOGOUT")    
    return logout_then_login(request, '/router/login')
    
def authHandler(request):
    #return HttpResponse("authHandler")
    action = request.GET['action']
    username = request.GET['username']
    password = request.GET['password']
    
        
    response_arr = {}
    
    if action == 'logout':
        return logout_then_login(request, '/router/login')
    elif action == 'login':
        user = authenticate(username=username, password=password)
        if user is not None:
            if user.is_active:                
                login(request, user)
                #return redirect_to_login('33','pageHandler/dashboard.html','xx.html');   #redirect to dashboard.html
                redirect_url = 'http://' + request.META['SERVER_NAME'] + ':' + request.META['SERVER_PORT'] + '/router/pageHandler/dashboard.html'
                response_arr['ret_code'] = 'success'
                response_arr['err_msg'] = redirect_url    
                response_arr['redirect_url'] = redirect_url
                
            else:
                response_arr['ret_code'] = 'faild'
                response_arr['err_msg'] = 'account disabled'
        else:
            response_arr['ret_code'] = 'faild'
            response_arr['err_msg'] = 'Wrong Password or invalid user name'
            
    response_json = json.dumps(response_arr)
    return HttpResponse(response_json)
    
def jsonHandler(request):    
    if not request.user.is_authenticated():
        #return HttpResponseRedirect("")        
        return HttpResponse("no_login")
    #return HttpResponse("jsonHandler !!!!!!!!!")

    #handler router console pages json request
    
    """
    if request.method == 'GET':
        method = 'GET'
    elif request.method == 'POST':
        method = 'POST'
    """    
    action = request.POST['action']
    
    """
        system.html functions
    """
    if action == 'change_password':
        user = request.POST['user_name']
        old_passwd = request.POST['old_passwd']
        new_passwd = request.POST['new_passwd']
        response_arr = {}
        response_arr['ret_code'] = 'success'        
        response_json = json.dumps(response_arr)
        
        return HttpResponse(response_json)
        #return HttpResponse("action change_password")
        
    elif action == 'reboot':        
        response_arr = {}
        response_arr['ret_code'] = 'success'        
        response_json = json.dumps(response_arr)
        return HttpResponse(response_json)        
        #return HttpResponse("action reboot")
        
    elif action == 'restore':
        response_arr = {}
        response_arr['ret_code'] = 'success'        
        response_json = json.dumps(response_arr)
        return HttpResponse(response_json)        
        #return HttpResponse("action restore")
        
    elif action == 'export_conf':
        # do something()        
        
        return HttpResponse("action export_conf")    
    
    
        #firewall.html start#    
    elif action == 'load_firewall_setting':
        #return HttpResponse("action load_firewall_setting")
        #get setting from router
        
        response_arr = {}
        response_arr['ret_code'] = 'success'
        response_arr['enable_ddos_firewall'] = 'true'
        response_arr['enable_arp_firewall'] = 'true'
        response_arr['enable_smart_connection_control'] = 'true'

        
        response_json = json.dumps(response_arr)        
        return HttpResponse(response_json)
        
    elif action == 'submit_firewall_setting':
        #return HttpResponse("action submit_firewall_setting")
        enable_ddos_firewall = request.POST['enable_ddos_firewall']
        enable_arp_firewall = request.POST['enable_arp_firewall']
        enable_smart_connection_control = request.POST['enable_smart_connection_control']
        
        response_arr = {}
        response_arr['ret_code'] = 'success'        
        response_json = json.dumps(response_arr)        
        return HttpResponse(response_json)
        
        #network.html            
    elif action == 'load_network_setting':
        #return HttpResponse("action load_network_page")
        
        response_arr = {}
        
        response_arr['wan_type'] = 2  #  pppoe==1   dhcp==2  static==3
        #wan setting
        response_arr['wanusername'] = 'wanusername'
        response_arr['wanpassword'] = 'wanpassword'
        #staitc wan setting
        response_arr['staticipaddr'] = '1.1.1.1'
        response_arr['staticsubnet'] = '1.1.1.1'
        response_arr['staticgateway'] = '1.1.1.1'
        response_arr['staticdns'] = '1.1.1.1'
        response_arr['staticalternatedns'] = '1.1.1.1'
        #lan setting
        response_arr['lan_ip'] = '1.1.1.1'
        response_arr['enable_dhcp'] = '1.1.1.1'
        response_arr['dhcp_ip_start'] = '1.1.1.1'
        response_arr['dhcp_ip_end'] = '1.1.1.1'
        
        response_json = json.dumps(response_arr)        
        return HttpResponse(response_json)    
        
    elif action == 'submit_wan':
        category = 'wan'
        wan_type = request.POST['wan_type']
        if wan_type == 'pppoe':
            value_str_tmp = request.POST['value_str']            
            value_str = '[' + value_str_tmp + ']'    
            #value_str = '[{"index": 1,"name": "WAN1","status": "enabled","phy_interface": "eth0","logic_interface": "eth0","wan_type": "dhcp","mtu": 1500}]'
            obj = None
            try:
                obj = Setting.objects.get(category = 'wan')
            except Exception, e:
                pass
            if obj is None:
                obj = Setting(category='wan', value = value_str, last_modify_time = timezone.now(), last_result = 1, last_execute_time = timezone.now())
            else:
                obj.value = value_str
            obj.save()
            response_arr = {};
            response_arr['ret_code'] = 'success'
            response_arr['err_msg'] = 'PPPoE submit success'
            response_arr['value_str'] = value_str
            response_json = json.dumps(response_arr)    
            return HttpResponse(response_json)
        elif wan_type == 'static':
            value_str_tmp = request.POST['value_str']            
            value_str = '[' + value_str_tmp + ']'
            #value_str = '[{"index": 1,"name": "WAN1","status": "enabled","phy_interface": "eth0","logic_interface": "eth0","wan_type": "static","mtu": 1500, "static_ip": "1.1.1.1","static_netmask": "255.255.255.0","static_gateway": "1.1.1.0","DNS1","8.8.8.8","DNS2","114.114.114.114"}]'
            obj = None
            try:
                obj = Setting.objects.get(category = 'wan')
            except Exception, e:
                pass
            if obj is None:
                obj = Setting(category='wan', value = value_str, last_modify_time = timezone.now(), last_result = 1, last_execute_time = timezone.now())
            else:
                obj.value = value_str
            obj.save()
            response_arr = {};
            response_arr['ret_code'] = 'success'
            response_arr['err_msg'] = 'dhcp submit success'
            response_arr['value_str'] = value_str
            response_json = json.dumps(response_arr)    
            return HttpResponse(response_json)
        
        elif wan_type == 'dhcp':
            value_str_tmp = request.POST['value_str']            
            value_str = '[' + value_str_tmp + ']'    
            obj = None
            try:
                obj = Setting.objects.get(category = 'wan')
            except Exception, e:
                pass
            if obj is None:
                obj = Setting(category='wan', value = value_str, last_modify_time = timezone.now(), last_result = 1, last_execute_time = timezone.now())
            else:
                obj.value = value_str
            obj.save()
            (ret, msg) = helper.take_effect_setting('wan')
            response_arr = {};
            if ret == 0:
                response_arr['ret_code'] = 'success'
            else:
                response_arr['ret_code'] = 'failed'
            response_arr['err_msg'] = '%d: %s' % (ret, msg)
            response_json = json.dumps(response_arr)    
            return HttpResponse(response_json)
        
        else:
            response_arr = {};
            response_arr['ret_code'] = 'failed'
            response_arr['err_msg'] = 'unknown wan_type [' + wan_type + ']'
            response_json = json.dumps(response_arr)    
            return HttpResponse(response_json)
            
    elif action == 'submit_lan':
        
        value_str_tmp = request.POST['value_str']            
        value_str = '[' + value_str_tmp + ']'
            
        response_arr = {};
        response_arr['ret_code'] = 'success'
        response_arr['err_msg'] = 'dhcp submit success'
        response_arr['value_str'] = value_str
        response_json = json.dumps(response_arr)    
        return HttpResponse(response_json)    
        
def pageHandler(request):
    if not request.user.is_authenticated():
        return render(request, "login.html", {})
        
    path_str = request.path    
    file_path = path_str.replace('/router/pageHandler/','')
    #return HttpResponse("pageHandler path ==> %s" % file_path)
    return render(request, file_path, {})        

def test(request):
    value_str = '[{"index": 3,"name": "WAN3","status": "disabled","phy_interface": "eth2","logic_interface": "eth2","wan_type": "dhcp","mtu": 1500}]'
    obj = Setting(category='wan', value = value_str, last_modify_time = timezone.now(), last_result = 1, last_execute_time = timezone.now())
    obj.save()
    response_arr = {};
    response_arr['ret_code'] = 'success'
    response_arr['err_msg'] = 'dhcp submit success'
    response_json = json.dumps(response_arr)    
    return HttpResponse(response_arr)
    
    
    
    
