from django.db import models
from django.contrib import admin, auth
from django.utils import timezone
from django.contrib.auth.models import User
import json
import datetime


class Release(models.Model):
    release_time = models.DateTimeField('Release Time')
    version = models.CharField(max_length=100)
    description = models.CharField(max_length=1000)

    def __str__(self):
        return self.version


class Device(models.Model):
    user = models.ForeignKey(User)
    name = models.CharField(default = '',max_length=255)
    mac = models.CharField(max_length=18)
    release = models.ForeignKey(Release)
    register_time = models.DateTimeField('Register Time', default = timezone.now)
    poweron_time = models.DateTimeField("Power On Time", default = timezone.now)
    free_memory = models.IntegerField("Free Memory", default = 0)
    free_cpu = models.IntegerField("Free CPU", default = 0)
    ads_enabled = models.IntegerField("Ads Enabled", default = 0)
    memo = models.TextField(default = '',blank=True)
    last_beacon_time = models.DateTimeField("Last Beacon Time", default = timezone.now)
    connection_count = models.IntegerField("Connection Count", default = 0)
    hardware = models.CharField(default = '', max_length=16)

    def __str__(self):
        return ', '.join((str(self.id), self.mac, self.user.username))

    def ToJson(self):
        last_beacon_time = int((timezone.now() - self.last_beacon_time).seconds)
        obj = {
            'FreeMemory': self.free_memory, 
            'LastBeaconTime': last_beacon_time,
            'MAC': self.mac,
            'ConnectionCount': self.connection_count,
            'PowerOnTime': self.poweron_time.strftime('%Y-%m-%d %H:%M:%S'),
            'FreeCPU': self.free_cpu,
            'id': self.id,
            'name': self.name,
            'AdsEnabled': self.ads_enabled,
            'memo': self.memo,
            'Hardware': self.hardware,
        }
        return json.dumps(obj)

