<?php

/**
 * Initialize certificate view.
 *
 * @category   apps
 * @package    user-certificates
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/user_certificates/
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
$this->lang->load('user_certificates');

///////////////////////////////////////////////////////////////////////////////
// Infobox
///////////////////////////////////////////////////////////////////////////////

echo infobox_highlight(lang('base_help'), lang('user_certificates_initialize_certificate_help'));

///////////////////////////////////////////////////////////////////////////////
// Form
///////////////////////////////////////////////////////////////////////////////

echo form_open('/user_certificates', array('autocomplete' => 'off'));
echo form_header(lang('base_settings'));

echo field_password('password', '', lang('base_password'));
echo field_password('verify', '', lang('base_verify'));

echo field_button_set(
    array(form_submit_custom('submit', lang('user_certificates_create_certificates')))
);

echo form_footer();
echo form_close();
