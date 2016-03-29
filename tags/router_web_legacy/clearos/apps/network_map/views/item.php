<?php

/**
 * Network map add view.
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
// Form handler
///////////////////////////////////////////////////////////////////////////////

if ($form_type === 'edit') {
    $mac_read_only = TRUE;
    $read_only = FALSE;
    $form = 'network_map/mapped/edit/' . $mac;
    $buttons = array(
        form_submit_update('submit'),
        anchor_cancel('/app/network_map/mapped')
    );
} else if ($form_type === 'add_unknown') {
    $mac_read_only = TRUE;
    $read_only = FALSE;
    $form = 'network_map/mapped/add_unknown/' . $mac;
    $buttons = array(
        form_submit_add('submit'),
        anchor_cancel('/app/network_map/unknown')
    );
} else if ($form_type === 'view') {
    $mac_read_only = TRUE;
    $read_only = TRUE;
    $form = 'network_map/mapped/add';
    $buttons = array(
        anchor_cancel('/app/network_map/mapped')
    );
} else {
    $mac_read_only = FALSE;
    $read_only = FALSE;
    $form = 'network_map/mapped/add';
    $buttons = array(
        form_submit_add('submit'),
        anchor_cancel('/app/network_map/mapped')
    );
}

///////////////////////////////////////////////////////////////////////////////
// Warning
///////////////////////////////////////////////////////////////////////////////

if ((($form_type === 'add') || ($form_type === 'add_unknown')) && ($subscription['available'] == 0)) {
    echo infobox_warning(lang('base_warning'), lang('network_map_device_limit_warning'));
    return;
}

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open($form);
echo form_header(lang('network_map_mapping'));

// Mapping
echo fieldset_header(lang('base_settings'));
echo field_input('mac', $mac, lang('network_mac_address'), $mac_read_only);
echo field_dropdown('username', $usernames, $username, lang('base_username'), $read_only);
echo fieldset_footer();

// Description
echo fieldset_header(lang('base_description'));
echo field_input('nickname', $nickname, lang('network_map_nickname'), $read_only);
echo field_input('vendor', $vendor, lang('network_map_vendor'), $read_only);
echo field_dropdown('type', $types, $type, lang('network_map_device_type'), $read_only);
echo fieldset_footer();

// IP information
echo fieldset_header(lang('network_ip'));

$inx = 1;

foreach ($mapping as $ip => $details) {
    $ip_key = strtr($ip, '/.', '-_');
    echo field_input("mapping[$ip_key]", $ip, lang('network_ip') . ' #' . $inx, $read_only);
    $inx++;
}

if (($form_type === 'edit') || ($form_type === 'add'))
    echo field_input("mapping[new]", '', lang('network_ip') . ' #' . $inx, $read_only);

// Footer
echo fieldset_footer();
echo field_button_set($buttons);

echo form_footer(); 
echo form_close();
