# -*- coding: utf-8 -*-
from __future__ import unicode_literals

from django.db import models, migrations


class Migration(migrations.Migration):

    dependencies = [
        ('device', '0001_initial'),
    ]

    operations = [
        migrations.AddField(
            model_name='device',
            name='hardware',
            field=models.CharField(default=b'', max_length=16),
        ),
    ]
