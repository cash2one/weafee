from django.shortcuts import render
from django.http import HttpResponse, HttpResponseRedirect
import json
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.views import logout_then_login

	
@login_required
def system(request):
    context = {}
    return render(request, 'system.html', context)
	
