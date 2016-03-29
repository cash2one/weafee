<?php

/**
 * Graphical console shutdown view.
 *
 * @category   apps
 * @package    graphical-console
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/graphical_console/
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

$ip = $lan_ips['0']; // TODO: handle more scenarios

$url_ip = (empty($ip)) ? 'w.x.y.z' : $ip;

echo "
<div align='left' class='graphical-console-content'>

<h2 style='float:left;'>" . $os_name . " " . $os_version . "</h2>
<br style='clear:both;' />
<div style='float:left; width:215px; font-size: 13px;'>" . lang('graphical_console_welcome_message') . "</div> 
<img style='float:left; margin-left:15px;' src='" . clearos_app_htdocs('graphical_console') . "/browsers.png' alt=''>

<br style='clear:both;' />
<br style='clear:both;' />
<h2 style='float:left;'>" . lang('graphical_console_step_1_description') . "</h2>

<div style='float:left; font-size: 13px;'>
";

if (empty($ip)) {
    echo lang('graphical_console_no_ip_address_available');
    echo "<p align='center'>" . anchor_custom('/app/network', lang('graphical_console_configure_network_now')) . "</p>";
} else {
    echo lang('graphical_console_ip_address:') . " <b>$ip</b><br/>";
    echo lang('graphical_console_follow_link_to_change_settings:') . " " .
    "<a href='/app/network'>" . lang('graphical_console_network_console') . "</a>";
}

echo "
</div>

<br style='clear:both;' />
<br style='clear:both;' />
<h2 style='float:left;'>" . lang('graphical_console_step_2_description') . "</h2>

<div style='float:left; width:330px;'>
<div style='float:left; font-size: 13px;'>" . lang('graphical_console_web_based_interface_access') . " ";

if (empty($ip))
    echo lang('graphical_console_type_address_in_following_format:');
else
    echo lang('graphical_console_type_following_address:');

echo "
</div> <br/>
<div style='margin-top: 55px; margin-left: auto; margin-right: auto; width:162px;'><h2>https://$url_ip<span style='color: #e1852e'>:81</span></h2></div>
</div>

<img style='float:left; margin-left:15px;' src='" . clearos_app_htdocs('graphical_console') . "/webconfig.png'  alt=''>

</div>
";
