from django.shortcuts import render
from django.contrib.auth.models import User
from django.contrib.auth.decorators import login_required
from django.http import HttpResponse
import json
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.views import logout_then_login


@login_required
def info(request):
    user = request.user
    level = 'VIP-1'
    if user.is_superuser:
        level = 'Admin'
    user_db = User.objects.get(pk=user.id)
    state = 'Online'
    obj = {'username': user_db.username, 'level': level, 'state': state}
    return HttpResponse(json.dumps(obj))


def index(request):
    context = {}
    return render(request, 'index.html', context)


def login_user(request):
    username = request.POST['username']
    password = request.POST['password']
    user = authenticate(username=username, password=password)
    if user is not None and user.is_active:
         login(request, user)
         resp = { 'return_code': 0
                , 'redirect_to': "/"
                }
         return HttpResponse(json.dumps(resp))
    else:
         return HttpResponse(json.dumps({"err_msg": "Login Failed!"}))
def logout(request):
    url = '/user/index'
    return logout_then_login(request, url)
 
@login_required
def system(request):
    context = {}
    return render(request, 'system.html', context)

@login_required
def password(request):
    obj = {'ret_code': 'failed'}
    if request.method == 'POST':
        username, password = request.user.username, request.POST.get('old_password')
        new_password, new_password2 = request.POST.get('new_password'), request.POST.get('new_password2')
        if new_password is not None and new_password != new_password2:
            obj['err_msg'] = '2 types of new password does not match'
        else:
            user = authenticate(username=username, password=password)
            if user is not None and user.is_active:
                user.set_password(new_password)
                user.save()
                obj = {'ret_code': 'success'}
    return HttpResponse(json.dumps(obj))
