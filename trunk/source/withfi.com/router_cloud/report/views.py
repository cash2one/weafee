from django.shortcuts import render
from django.http import HttpResponse, HttpResponseRedirect
import json
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from mongo_helper import get_traffic_summary, get_traffic_summary_daily, get_client_os_summary, get_router_hardware_summary, get_source_summary
from device.models import Device
from datetime import datetime, timedelta

@login_required
def dashboard(request):
    #return HttpResponse("Hello, world. You're at the report's index.")
    context = {}
    return render(request, 'dashboard.html', context)

@login_required
def summary_traffic(request):
    user = request.user
    user_device_ids = None
    if not user.is_superuser:
        user_device_ids = [str(device.id) for device in Device.objects.filter(user=user)]
    obj = get_traffic_summary(user_device_ids)
    return HttpResponse(json.dumps(obj))
@login_required
def summary_traffic_daily(request):
    user = request.user
    user_device_ids = None
    if user.is_superuser:
        user_device_ids = [str(device.id) for device in Device.objects.all()]
    else:
        user_device_ids = [str(device.id) for device in Device.objects.filter(user=user)]
    device_ids = []
    device_id = request.GET.get('device') 
    if device_id is None or len(device_id) == 0:
        device_ids = user_device_ids
    elif device_id in user_device_ids:
        device_ids = [device_id]
    #NOTE date = None hints latest 7 days
    date = request.GET.get('date')
    obj = get_traffic_summary_daily(device_ids, date)
    local_hours = ['00', '01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23']
    utc_hours = local_hours[16:] + local_hours[:16]
    obj['category'] = local_hours
    #UV = [22,323,33,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3]
    #PV = [22,323,33,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3]
    #obj = {'category': category, 'UV': UV, 'PV': PV}
    return HttpResponse(json.dumps(obj))
@login_required
def summary_terminal(request):
    #iphone = [22]
    #ipad = [23]
    #android = [29]
    #wphone = [2]
    #pc = [100]
    #others = [30]
    #category = ['2015-08-20']
    #obj = { "iphone": 22
    #           , "ipad": 23
    #           , "android": 29
    #           , "pc": 100
    #           , "others": 5
    #           , 'category' : category
    #           }
    user = request.user
    user_device_ids = None
    if not user.is_superuser:
        user_device_ids = [str(device.id) for device in Device.objects.filter(user=user)]
    obj = get_client_os_summary(user_device_ids)
    return HttpResponse(json.dumps(obj))
@login_required
def summary_hardware(request):
    #category = ['2015-08-20']
    #response = { 'R200' : [2]
    #           , 'R400': [4]
    #           , 'R600': [3]
    #           , 'G200': [2]
    #           , 'G400': [7]
    #           , 'G600': [2]
    #           , 'Others': [2]
    #           , 'category' : category
    #           }
    user = request.user
    user_device_ids = None
    if not user.is_superuser:
        user_device_ids = [str(device.id) for device in Device.objects.filter(user=user)]
    obj = get_router_hardware_summary(user_device_ids)
    return HttpResponse(json.dumps(obj))
@login_required
def summary_source(request):
    #obj2 =  {'data': [['Banner', 121], ['iframe', 31], ['iOS Screen Papery', 411], ['Popup', 34], ['404 Page', 14], ['Others', 12]]}
    user = request.user
    user_device_ids = None
    days_ago = 0
    if not user.is_superuser:
        user_device_ids = [str(device.id) for device in Device.objects.filter(user=user)]
    if request.GET.has_key('days_ago'):
        days_ago = int(request.GET.get('days_ago'))
    query_day = datetime.now() - timedelta(days=days_ago)
    obj = get_source_summary(user_device_ids, query_day)
    return HttpResponse(json.dumps(obj))
