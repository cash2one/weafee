<?php

/////////////////////////////////////////////////////////////////////////////
// General information
/////////////////////////////////////////////////////////////////////////////

$app['basename'] = 'port_forwarding';
$app['version'] = '1.6.7';
$app['release'] = '1';
$app['vendor'] = 'ClearFoundation';
$app['packager'] = 'ClearFoundation';
$app['license'] = 'GPLv3';
$app['license_core'] = 'LGPLv3';
$app['description'] = lang('port_forwarding_app_description');

/////////////////////////////////////////////////////////////////////////////
// App name and categories
/////////////////////////////////////////////////////////////////////////////

$app['name'] = lang('port_forwarding_app_name');
$app['category'] = lang('base_category_network');
$app['subcategory'] = lang('base_subcategory_firewall');

/////////////////////////////////////////////////////////////////////////////
// Packaging
/////////////////////////////////////////////////////////////////////////////

$app['requires'] = array(
    'app-network',
);

$app['core_requires'] = array(
    'app-firewall >= 1:1.5.19',
    'app-network-core >= 1:1.5.1',
);

$app['delete_dependency'] = array(
    'app-port-forwarding-core'
);
