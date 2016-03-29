<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'user_certificates';
$app['version'] = '1.6.7';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('user_certificates_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('user_certificates_app_name');
$app['category'] = lang('base_category_my_account');
$app['subcategory'] = lang('base_subcategory_accounts');

$app['user_access'] = TRUE;

/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['user_certificates']['title'] = $app['name'];
$app['controllers']['certificates']['title'] = lang('user_certificates_certificates');
$app['controllers']['openvpn']['title'] = lang('base_configuration');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-accounts',
    'app-groups',
    'app-certificate-manager',
);

$app['core_requires'] = array(
    'app-accounts-core',
    'app-certificate-manager-core',
    'app-user-certificates-plugin-core',
    'system-users-driver', 
);

$app['core_file_manifest'] = array(
   'user_certificates.acl' => array( 'target' => '/var/clearos/base/access_control/authenticated/user_certificates' ),
);
