from django.conf.urls import url
from . import views

urlpatterns = [
	url(r'^index$', views.index, name='index'),
	url(r'^login', views.login_user, name='login_user'),
	url(r'^logout', views.logout, name='logout'),
	url(r'^info$', views.info, name='info'),
	url(r'^password$', views.password, name='password'),
	url(r'^system$', views.system, name='system'),
	url(r'^$', views.info, name='index'),
]
