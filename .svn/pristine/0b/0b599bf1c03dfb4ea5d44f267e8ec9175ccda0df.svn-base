"""
	report url.py
"""

from django.conf.urls import url
from . import views

urlpatterns = [
	url(r'^dashboard.*$', views.dashboard, name='dashboard'),
	url(r'^summary/traffic$', views.summary_traffic, name='traffic'),
	url(r'^summary/traffic/daily$', views.summary_traffic_daily, name='traffic_daily'),
	url(r'^summary/terminal$', views.summary_terminal, name='terminal'),
	url(r'^summary/hardware$', views.summary_hardware, name='hardware'),
	url(r'^summary/ad_type$', views.summary_source, name='ad_type'),
	url(r'^$', views.dashboard, name='dashboard'),
]
