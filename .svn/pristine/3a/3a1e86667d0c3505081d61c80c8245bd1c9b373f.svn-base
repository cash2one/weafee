"""
	report url.py
"""

from django.conf.urls import url
from . import views

urlpatterns = [
	url(r'^register$', views.register, name='register'),
	url(r'^beacon$', views.beacon, name='beacon'),
	url(r'^state$', views.state, name='state'),
	url(r'^edit_device_info/submit$', views.update_device_info, name='update_device_info'),	
	url(r'^index$', views.index, name='index'),
	url(r'^list$', views.device_list, name='device_list'),
	url(r'^$', views.index, name='index'),
]
