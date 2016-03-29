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

$this->lang->load('network');

echo infobox_highlight(
    lang('network_map_unknown_devices'),
    lang('network_map_number_of_unknown_devices:') . 
    " <span id='unknown_scanning' class='theme-loading-small'> &nbsp;</span>" .
    " <span id='unknown_count'></span> &nbsp; " .
    " <span id='unknown_details_link'>" .  anchor_custom('/app/network_map/unknown', 'Show Details') . "</span>"
);
