from django.db import models
from django.utils import timezone
import json
import datetime


class Ad(models.Model):
    owner = models.CharField(max_length=128)
    create_time = models.DateTimeField('Create Time', default=timezone.now)
    entity = models.CharField(max_length=6144)
    base_folder = models.CharField(max_length=1024)
    folder_name = models.CharField(max_length=64)
    sync_flag = models.IntegerField('Sync Flag', default=0)
    ad_type = models.IntegerField('Ad Type', default=0)
    ad_name = models.CharField(max_length=256)
    ad_relocate_url = models.CharField(max_length=2048)
    target_terminal_os = models.CharField(max_length=256)
    ad_start_timerange = models.CharField(max_length=256)
    ad_end_timerange = models.CharField(max_length=256)
    ad_target_website_quality = models.IntegerField('Website Quality', default=0)
    ad_target_country = models.CharField(max_length=256)
    ad_target_routers = models.CharField(max_length=2046)
    ad_budget = models.IntegerField('Budget', default=0)

    def ToJson(self):
        obj = {
            'id': self.id,
            'ad_type': self.ad_type,
            'ad_name': self.ad_name,
            'ad_relocate_url': self.ad_relocate_url,
            'ads_folder_name': self.folder_name,
            'target_terminal_os': self.target_terminal_os,
            'ad_start_timerange': self.ad_start_timerange,
            'ad_end_timerange': self.ad_end_timerange,
            'ad_target_website_quality': self.ad_target_website_quality,
            'ad_target_country': self.ad_target_country,
            'ad_target_routers': self.ad_target_routers,
            'ad_budget': self.ad_budget,
            'sync_flag': self.sync_flag,
        }
        return json.dumps(obj)

class sync_log(models.Model):
    owner = models.CharField(max_length=128)
    sync_type = models.CharField(max_length=128)
    last_sync_time =  models.DateTimeField('Last Sync Time',auto_now=True)
    src_path = models.CharField(max_length=1024)
    dest_path = models.CharField(max_length=1024)
    sync_dest_router_id = models.TextField(default = '',blank=True)
    sync_src_folder = models.TextField(default = '',blank=True)
    version = models.IntegerField('Version',default = 1)
    result = models.IntegerField('Result',default = 0)


