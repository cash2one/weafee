# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import models, migrations
from django.conf import settings


class Migration(migrations.Migration):

    dependencies = [
        migrations.swappable_dependency(settings.AUTH_USER_MODEL),
    ]

    operations = [
        migrations.CreateModel(
            name='WfUser',
            fields=[
                ('id', models.AutoField(verbose_name='ID', serialize=False, auto_created=True, primary_key=True)),
                ('hosted', models.BooleanField(default=False)),
                ('parent_user', models.IntegerField(default=1)),
                ('points', models.IntegerField(default=0)),
                ('level', models.IntegerField(default=1)),
                ('ip_address', models.CharField(default=b'', max_length=20)),
                ('contactor', models.CharField(default=b'', max_length=256)),
                ('contactor_title', models.CharField(default=b'', max_length=64)),
                ('telephone', models.CharField(default=b'', max_length=24)),
                ('cellphone', models.CharField(default=b'', max_length=24)),
                ('qq', models.CharField(default=b'', max_length=16)),
                ('address', models.CharField(default=b'', max_length=256)),
                ('user', models.OneToOneField(to=settings.AUTH_USER_MODEL)),
            ],
        ),
    ]
