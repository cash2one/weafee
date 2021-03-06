<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'incoming_firewall';
$app['version'] = '1.6.7';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('incoming_firewall_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('incoming_firewall_app_name');
$app['category'] = lang('base_category_network');
$app['subcategory'] = lang('base_subcategory_firewall');


/////////////////////////////////////////////////////////////////////////////
// Controllers
/////////////////////////////////////////////////////////////////////////////

$app['controllers']['incoming_firewall']['title'] = lang('incoming_firewall_app_name');
$app['controllers']['allow']['title'] = lang('incoming_firewall_allowed_incoming_connections');
$app['controllers']['block']['title'] = lang('incoming_firewall_blocked_incoming_connections');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-network',
);

$app['core_requires'] = array(
    'app-firewall',
    'app-network-core >= 1:1.5.1',
);

$app['core_file_manifest'] = array(
    'allow-port' => array(
        'target' => '/usr/sbin/allow-port',
        'mode' => '0755',
    ),
);
