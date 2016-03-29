# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import models, migrations
import django.utils.timezone


class Migration(migrations.Migration):

    dependencies = [
    ]

    operations = [
        migrations.CreateModel(
            name='Ad',
            fields=[
                ('id', models.AutoField(verbose_name='ID', serialize=False, auto_created=True, primary_key=True)),
                ('owner', models.CharField(max_length=128)),
                ('create_time', models.DateTimeField(default=django.utils.timezone.now, verbose_name=b'Create Time')),
                ('entity', models.CharField(max_length=6144)),
                ('base_folder', models.CharField(max_length=1024)),
                ('folder_name', models.CharField(max_length=64)),
                ('sync_flag', models.IntegerField(default=0, verbose_name=b'Sync Flag')),
                ('ad_type', models.IntegerField(default=0, verbose_name=b'Ad Type')),
                ('ad_name', models.CharField(max_length=256)),
                ('ad_relocate_url', models.CharField(max_length=2048)),
                ('target_terminal_os', models.CharField(max_length=256)),
                ('ad_start_timerange', models.CharField(max_length=256)),
                ('ad_end_timerange', models.CharField(max_length=256)),
                ('ad_target_website_quality', models.IntegerField(default=0, verbose_name=b'Website Quality')),
                ('ad_target_country', models.CharField(max_length=256)),
                ('ad_target_routers', models.CharField(max_length=2046)),
                ('ad_budget', models.IntegerField(default=0, verbose_name=b'Budget')),
            ],
        ),
        migrations.CreateModel(
            name='sync_log',
            fields=[
                ('id', models.AutoField(verbose_name='ID', serialize=False, auto_created=True, primary_key=True)),
                ('owner', models.CharField(max_length=128)),
                ('sync_type', models.CharField(max_length=128)),
                ('last_sync_time', models.DateTimeField(auto_now=True, verbose_name=b'Last Sync Time')),
                ('src_path', models.CharField(max_length=1024)),
                ('dest_path', models.CharField(max_length=1024)),
                ('sync_dest_router_id', models.TextField(default=b'', blank=True)),
                ('sync_src_folder', models.TextField(default=b'', blank=True)),
                ('version', models.IntegerField(default=1, verbose_name=b'Version')),
                ('result', models.IntegerField(default=0, verbose_name=b'Result')),
            ],
        ),
    ]
