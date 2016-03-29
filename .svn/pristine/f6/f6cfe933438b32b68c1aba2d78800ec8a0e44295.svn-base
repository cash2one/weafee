<?php

/**
 * User certificates view.
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

$this->lang->load('user_certificates');
$this->lang->load('certificate_manager');

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('certificate_manager_certificate'),
);

///////////////////////////////////////////////////////////////////////////////
// Anchors
///////////////////////////////////////////////////////////////////////////////

$anchors = array(anchor_custom('/app/user_certificates/certificates/reset', lang('base_reset')));

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($certs as $basename => $title) {
    if (($username !== 'root') || ($basename === 'ca-cert.pem')) {
        $item['title'] = $title;
        $item['action'] = '/app/user_certificates/certificates/download/' . $basename;
        $item['anchors'] = button_set(
            array(
                anchor_custom('/app/user_certificates/certificates/download/' . $basename, lang('base_download')),
                anchor_custom('/app/user_certificates/certificates/install/' . $basename, lang('base_install'))
            )
        );
        $item['details'] = array($item['title']);
        $items[] = $item;
    }
}

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

echo summary_table(
    lang('certificate_manager_security_certificates'),
    $anchors,
    $headers,
    $items
);
