<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'user_profile';
$app['version'] = '1.6.7';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('user_profile_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('user_profile_app_name');
$app['category'] = lang('base_category_my_account');
$app['subcategory'] = lang('base_subcategory_accounts');

$app['user_access'] = TRUE;

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-accounts',
    'app-groups',
    'app-users',
);

$app['core_requires'] = array(
    'app-accounts-core',
    'app-groups-core',
    'app-users-core >= 1.0.6',
);

$app['core_file_manifest'] = array(
   'user_profile.acl' => array( 'target' => '/var/clearos/base/access_control/authenticated/user_profile' ),
);
