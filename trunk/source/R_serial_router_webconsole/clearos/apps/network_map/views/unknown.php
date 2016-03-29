<?php

/**
 * Network map summary view.
 *
 * @category   apps
 * @package    network-map
 * @subpackage views
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
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
    lang('network_mac_address'),
    lang('network_ip'),
    lang('network_hostname'),
    lang('network_map_vendor'),
    lang('network_map_last_seen')
);

///////////////////////////////////////////////////////////////////////////////
// Anchors 
///////////////////////////////////////////////////////////////////////////////

$anchors = array(
    anchor_custom('/app/network_map', lang('base_return_to_summary'))
);

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($mappings as $mac => $device_info) {
    foreach ($device_info['mapping'] as $ip => $details) {
        $order_ip = "<span style='display: none'>" . sprintf("%032b", ip2long($ip)) . "</span>" . $ip;
        $order_date = "<span style='display: none'>" . $details['last_seen'] . "</span>" . strftime('%c', $details['last_seen']);
        $vendor = (strlen($device_info['vendor']) > 15) ? substr($device_info['vendor'], 0, 15) . ' ...' : $device_info['vendor'];

        if (($form_type === 'edit') && ($subscription['available'] != 0)) 
            $item_anchors = array(anchor_custom('/app/network_map/mapped/add_unknown/' . $mac, lang('network_map_map')));
        else
            $item_anchors = array();

        $item['title'] = $order_ip;
        $item['action'] = anchor_custom('/app/network_map/mapped/add_unknown/' . $mac, lang('network_map_map'));
        $item['anchors'] = button_set($item_anchors);

        $item['details'] = array(
            $mac,
            $order_ip,
            $details['hostname'],
            $vendor,
            $order_date
        );

        $items[] = $item;
    }
}

sort($items);

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

$options['default_rows'] = 100;
$options['no_action'] = ($form_type === 'edit') ? FALSE : TRUE;
$options['sort-default-col'] = 4;
$options['sort-default-dir'] = 'desc';

echo summary_table(
    lang('network_map_unknown_devices'),
    $anchors,
    $headers,
    $items,
    $options
);
