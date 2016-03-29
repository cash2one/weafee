"""
	bidding url.py
"""

from django.conf.urls import url
from . import views

urlpatterns = [
	url(r'^$', views.bid, name='bid')
]
