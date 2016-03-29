<?php

/**
 * Network map summary view.
 *
 * @category   apps
 * @package    network-map
 * @subpackage views
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012-2013 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('base');
$this->lang->load('network');
$this->lang->load('network_map');

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('base_username'),
    lang('network_map_device'),
    lang('network_mac_address'),
);

///////////////////////////////////////////////////////////////////////////////
// Anchors
///////////////////////////////////////////////////////////////////////////////

if (($form_type === 'edit') && ($subscription['available'] != 0))
    $anchors = array(anchor_add('/app/network_map/mapped/add'));
else
    $anchors = array();

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($mappings as $mac => $details) {
    if ($form_type == 'view') {
        $item_anchors =  array(
            anchor_view('/app/network_map/mapped/view/' . $mac, 'high'),
        );
    } else {
        $item_anchors =  array(
            anchor_edit('/app/network_map/mapped/edit/' . $mac, 'high'),
            anchor_delete('/app/network_map/mapped/delete/' . $mac, 'low')
        );
    }

    $item['title'] = $mac;
    $item['action'] = anchor_edit('/app/network_map/mapped/edit/' . $mac, 'high');
    $item['anchors'] = button_set($item_anchors);

    $item['details'] = array(
        $details['username'],
        $details['nickname'],
        $mac,
    );

    $items[] = $item;
}

sort($items);

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

$options['default_rows'] = 25;

echo summary_table(
    lang('network_map_mapped_devices'),
    $anchors,
    $headers,
    $items,
    $options
);
