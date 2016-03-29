from django.shortcuts import render
from django.http import HttpResponse, HttpResponseRedirect
import json
from user_agents import parse
from adv.models import Ad
from random import choice

def bid(request):
    ad_id = ''
    device_id = None
    if request.GET.has_key('device_id'):
        device_id = str(request.GET['device_id']).strip()
    ua_str = request.META['HTTP_USER_AGENT']
    user_agent = parse(ua_str)
    os = '1' #Android
    if user_agent.is_pc:
        os = '3' #PC
    elif (user_agent.is_mobile or user_agent.is_tablet) and user_agent.device.brand == 'Apple':
        os = '2' #iOS
    else:
        pass
        
    ad_objs = Ad.objects.filter(owner='admin').order_by('id')
    filtered_ads = []
    for ad in ad_objs:
        target_terminal_os = json.loads(ad.target_terminal_os)
        if os in target_terminal_os:
            if ad.ad_budget > 1:
                filtered_ads.append(ad)
    context = {'adtype': 4, 'adhtml':'0'}
    if len(filtered_ads) > 0:
        ad = choice(filtered_ads)
        if ad is not None:
            ad.ad_budget -= 1
            ad.save()
            context = {'adtype': ad.ad_type, 'adhtml':ad.folder_name}
    callback = ''
    if request.GET.has_key('jsonp'):
        callback = request.GET['jsonp']
    elif request.GET.has_key('bidjsonp'):
        callback = request.GET['bidjsonp']
    elif request.GET.has_key('callback'):
        callback = request.GET['callback']
    else:
        pass
    resp = HttpResponse('%s(%s)'% (callback,json.dumps(context)))
    resp['Access-Control-Allow-Origin'] = '*'
    return resp
