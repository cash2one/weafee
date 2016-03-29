"""
	adv urls.py
"""

from django.conf.urls import url
from . import views

urlpatterns = [
	url(r'^adv$', views.adv, name='adv'),
	url(r'^global_blacklist/show$', views.global_blacklist_show, name='global_blacklist_show'),
	url(r'^global_blacklist/submit$', views.global_blacklist_submit, name='global_blacklist_submit'),
	url(r'^adctl/upload_file$',views.upload_file, name='upload_file'),	
	url(r'^adctl/show$',views.show_ads, name='show_ads'),	
	url(r'^adctl/add$',views.add_new_ads, name='add_new_ads'),	
	url(r'^adctl/delete$',views.delete_ads, name='delete_ads'),
	url(r'^adctl/edit$',views.edit_ads, name='edit_ads'),
	url(r'^adctl/sync$',views.sync_ads, name='sync_ads'),
	url(r'^$', views.adv, name='adv'),
]
