from django.db import models
from django.contrib import admin, auth
from django.utils import timezone
import json
import datetime

# Create DB table models here.
class Setting(models.Model):
    category = models.CharField(max_length=200)
    value = models.CharField(max_length=2400)
    last_modify_time = models.DateTimeField('Last Time Modified')
    last_result = models.IntegerField()
    last_execute_time = models.DateTimeField('Last Execute Time Modified')
    def __str__(self):
        return self.category + self.value


