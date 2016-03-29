<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'network_map';
$app['version'] = '1.5.20';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('network_map_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('network_map_app_name');
$app['category'] = lang('base_category_network');
$app['subcategory'] = lang('base_subcategory_infrastructure');

/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['network_map']['title'] = lang('network_map_app_name');
$app['controllers']['mapped']['title'] = lang('network_map_mapped_devices');
$app['controllers']['unknown_summary']['title'] = lang('network_map_unknown_devices');
$app['controllers']['subscription']['title'] = lang('base_subscription');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-network',
    'app-tasks-core',
);

// app-openldap-core required for schema additions
$app['core_requires'] = array(
    'app-clearcenter-core >= 1:1.4.70',
    'app-ldap-core >= 1:1.4.70',
    'app-network-core >= 1:1.4.70',
    'app-openldap-core >= 1:1.4.70',
    'app-mode-core >= 1:1.4.70',
    'arpwatch'
);

$app['core_directory_manifest'] = array(
    '/var/clearos/network_map' => array(),
);

$app['core_file_manifest'] = array(
    'arpwatch.php'=> array('target' => '/var/clearos/base/daemon/arpwatch.php'),
    'app-network-map.cron' => array('target' => '/etc/cron.d/app-network-map'),
    'network_map_subscription' =>  array('target' => '/var/clearos/clearcenter/subscriptions/network_map'),
    'export-network-map' => array(
        'target' => '/usr/sbin/export-network-map',
        'mode' => '0755',
    ),
);
