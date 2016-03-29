"""
	routerapp url.py
"""

from django.conf.urls import url
from . import views

urlpatterns = [
	url(r'^jsonHandler', views.jsonHandler, name='jsonHandler'),
	url(r'^authHandler', views.authHandler, name='authHandler'),
	url(r'^pageHandler', views.pageHandler, name='pageHandler'),
	url(r'^login', views.index, name='index'),
	url(r'^logout', views.logout, name='logout'),
	url(r'^test', views.test, name='test'),
	url(r'^$', views.index, name='index'),
]
