<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'mode';
$app['version'] = '1.6.7';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('mode_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('mode_app_name');
$app['category'] = lang('base_category_system');
$app['subcategory'] = lang('base_subcategory_settings');
$app['menu_enabled'] = FALSE;

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['core_only'] = TRUE;

// Always pull in the simple mode driver for now.
$app['core_requires'] = array(
    'app-simple-mode-core',
    'app-events-core',
    'csplugin-filewatch',
    'system-mode-driver',
);

$app['core_file_manifest'] = array(
    'filewatch-mode-event.conf'=> array('target' => '/etc/clearsync.d/filewatch-mode-event.conf'),
);

$app['core_directory_manifest'] = array(
   '/var/clearos/mode' => array(),
   '/var/clearos/events/mode' => array(),
);
