from django.contrib.auth.models import User
from django.contrib import admin
from django.contrib.auth.admin import UserAdmin
from django.contrib.auth.models import User
from django.db import models

class WfUser(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE)
    hosted = models.BooleanField(default=False)
    #name = user.firstname + user.lastname
    # TODO a ForignKey restriction
    parent_user = models.IntegerField(default = 1)
    points = models.IntegerField(default = 0)
    level = models.IntegerField(default = 1)
    ip_address = models.CharField(max_length = 20, default = '')
    #reg_time = user.date_joined
    #reg_name = username
    contactor = models.CharField(max_length = 256, default = '')
    contactor_title = models.CharField(max_length = 64, default = '')
    telephone = models.CharField(max_length = 24, default = '')
    cellphone = models.CharField(max_length = 24, default = '')
    qq = models.CharField(max_length = 16, default = '')
    # email = user.email
    address = models.CharField(max_length = 256, default = '')

# Define an inline admin descriptor for Employee model
# which acts a bit like a singleton
class WfUserInline(admin.StackedInline):
    model = WfUser
    can_delete = False
    verbose_name_plural = 'withfi_user'

# Define a new User admin
class UserAdmin(UserAdmin):
    inlines = (WfUserInline, )

# Re-register UserAdmin
admin.site.unregister(User)
admin.site.register(User, UserAdmin)
