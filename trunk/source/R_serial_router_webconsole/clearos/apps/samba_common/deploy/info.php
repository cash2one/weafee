<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'samba_common';
$app['version'] = '1.6.7';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('samba_common_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('samba_common_app_name');
$app['category'] = lang('base_category_server');
$app['subcategory'] = lang('base_subcategory_file');
$app['menu_enabled'] = FALSE;

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_only'] = TRUE;

$app['core_requires'] = array(
    'app-network-core >= 1:1.4.70',
    'app-events-core',
    'csplugin-filewatch',
);

$app['core_directory_manifest'] = array(
    '/var/clearos/events/samba_configuration' => array(),
    '/var/clearos/samba_common/lock' => array(
        'mode' => '0775',
        'owner' => 'root',
        'group' => 'webconfig',
    ),
);

$app['core_file_manifest'] = array(
    'filewatch-samba-configuration-event.conf'=> array('target' => '/etc/clearsync.d/filewatch-samba-configuration-event.conf'),
    'samba_common.conf' => array(
        'target' => '/etc/clearos/samba_common.conf',
        'config' => TRUE,
        'config_params' => 'noreplace',
    ),
    'network-configuration-event' => array(
        'target' => '/var/clearos/events/network_configuration/samba_common',
        'mode' => '0755'
    ),
);

