# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import models, migrations
import django.utils.timezone
from django.conf import settings


class Migration(migrations.Migration):

    dependencies = [
        migrations.swappable_dependency(settings.AUTH_USER_MODEL),
    ]

    operations = [
        migrations.CreateModel(
            name='Device',
            fields=[
                ('id', models.AutoField(verbose_name='ID', serialize=False, auto_created=True, primary_key=True)),
                ('name', models.CharField(default=b'', max_length=255)),
                ('mac', models.CharField(max_length=18)),
                ('register_time', models.DateTimeField(default=django.utils.timezone.now, verbose_name=b'Register Time')),
                ('poweron_time', models.DateTimeField(default=django.utils.timezone.now, verbose_name=b'Power On Time')),
                ('free_memory', models.IntegerField(default=0, verbose_name=b'Free Memory')),
                ('free_cpu', models.IntegerField(default=0, verbose_name=b'Free CPU')),
                ('ads_enabled', models.IntegerField(default=0, verbose_name=b'Ads Enabled')),
                ('memo', models.TextField(default=b'', blank=True)),
                ('last_beacon_time', models.DateTimeField(default=django.utils.timezone.now, verbose_name=b'Last Beacon Time')),
                ('connection_count', models.IntegerField(default=0, verbose_name=b'Connection Count')),
            ],
        ),
        migrations.CreateModel(
            name='Release',
            fields=[
                ('id', models.AutoField(verbose_name='ID', serialize=False, auto_created=True, primary_key=True)),
                ('release_time', models.DateTimeField(verbose_name=b'Release Time')),
                ('version', models.CharField(max_length=100)),
                ('description', models.CharField(max_length=1000)),
            ],
        ),
        migrations.AddField(
            model_name='device',
            name='release',
            field=models.ForeignKey(to='device.Release'),
        ),
        migrations.AddField(
            model_name='device',
            name='user',
            field=models.ForeignKey(to=settings.AUTH_USER_MODEL),
        ),
    ]
