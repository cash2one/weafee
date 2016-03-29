<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'ipsec';
$app['version'] = '1.1.1';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('ipsec_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('ipsec_app_name');
$app['category'] = lang('base_category_network');
$app['subcategory'] = lang('base_subcategory_vpn');
$app['menu_enabled'] = FALSE;

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_only'] = TRUE;

$app['core_requires'] = array(
    '/usr/sbin/ipsec',
);

$app['core_file_manifest'] = array(
    'ipsec.php' => array('target' => '/var/clearos/base/daemon/ipsec.php'),
);
