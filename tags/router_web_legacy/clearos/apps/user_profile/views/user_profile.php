<?php

/**
 * User profile view.
 *
 * @category   apps
 * @package    user-profile
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/user_profile/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.  
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('base');
$this->lang->load('user_profile');

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('/user_profile', array('autocomplete' => 'off'));
echo form_header(lang('base_settings'));

///////////////////////////////////////////////////////////////////////////////
// Core fields
///////////////////////////////////////////////////////////////////////////////
//
// Some directory drivers separate first and last names into separate fields,
// while others only support the full name (common name).  If the separate 
// fields don't exist, fall back to the full name.
//
///////////////////////////////////////////////////////////////////////////////

echo fieldset_header(lang('user_profile_contact_information'));

foreach ($info_map['core'] as $key_name => $details) {
    $name = "user_info[core][$key_name]";
    $value = $user_info['core'][$key_name];
    $description =  $details['description'];

    if ($details['field_priority'] !== 'normal')
        continue;

    if ($details['field_type'] === 'list') {
        echo field_dropdown($name, $details['field_options'], $value, $description, TRUE);
    } else if ($details['field_type'] === 'simple_list') {
        echo field_simple_dropdown($name, $details['field_options'], $value, $description, TRUE);
    } else if ($details['field_type'] === 'text') {
        echo field_input($name, $value, $description, TRUE);
    } else if ($details['field_type'] === 'integer') {
        echo field_input($name, $value, $description, TRUE);
    }
}

echo fieldset_footer();

///////////////////////////////////////////////////////////////////////////////
// Password fields
///////////////////////////////////////////////////////////////////////////////

if ($mode === 'edit') {
    echo fieldset_header(lang('base_password'));
    echo field_password('old_password', '', lang('base_current_password'));
    echo field_password('password', '', lang('base_new_password'));
    echo field_password('verify', '', lang('base_verify'));
    echo fieldset_footer();

    echo field_button_set(
        array(form_submit_update('submit'))
    );
}

echo form_footer();
echo form_close();
