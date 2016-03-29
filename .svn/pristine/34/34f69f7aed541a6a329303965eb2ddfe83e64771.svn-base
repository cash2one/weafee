from django.contrib import admin
from .models import Device, Release


class ReleaseAdmin(admin.ModelAdmin):
    fields = ['release_time', 'version', 'description']


class DeviceAdmin(admin.ModelAdmin):
    fields = ['user', 'mac', 'release', 'register_time', 'poweron_time', 'free_memory', 'free_cpu', 'ads_enabled', 'last_beacon_time', 'connection_count']

admin.site.register(Release, ReleaseAdmin)
admin.site.register(Device, DeviceAdmin)
