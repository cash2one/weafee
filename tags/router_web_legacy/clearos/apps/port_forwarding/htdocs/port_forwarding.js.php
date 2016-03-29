<?php

/**
 * Javascript helper for port forwarding.
 *
 * @category   apps
 * @package    port-forwarding
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/port_forwarding/
 */

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

clearos_load_language('port_forwarding');
clearos_load_language('base');

header('Content-Type: application/x-javascript');

echo "

$(document).ready(function() {

    if ($(location).attr('href').match('.*\/add$') != null) {
        $('#port_from').css('width', '50');
        $('#port_to').css('width', '50');
        $('#range_start').css('width', '50');
        $('#range_end').css('width', '50');
    }

});

";
// vim: syntax=php ts=4
