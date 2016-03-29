<?php

/**
 * Memory information javascript helper.
 *
 * @category   apps
 * @package    dashboard
 * @subpackage javascript
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/dashboard/
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
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('base');

///////////////////////////////////////////////////////////////////////////////
// J A V A S C R I P T
///////////////////////////////////////////////////////////////////////////////

header('Content-Type:application/x-javascript');
?>

$(document).ready(function() {

    // Translations
    //-------------

    lang_free = '<?php echo lang("base_free"); ?>';
    lang_cached = '<?php echo lang("base_cached"); ?>';
    lang_buffers = '<?php echo lang("base_buffers"); ?>';
    lang_kernel_and_apps = '<?php echo lang("base_kernel_and_apps"); ?>';

    // Main
    //-----

    if ($('#memory_chart').length != 0)
        get_report();
});


function get_report() {
    $.ajax({
        url: '/app/dashboard/mem/get_data',
        method: 'GET',
        dataType: 'json',
        success : function(payload) {
            graph_data(payload);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            window.setTimeout(get_report, 3000);
        }
    });
}

function graph_data(payload) {
    var data = new Array();

    var data = [
        [ lang_free, payload.free_percent],
        [ lang_cached, payload.cached_percent],
        [ lang_buffers, payload.buffers_percent],
        [ lang_kernel_and_apps, payload.kernel_and_apps_percent],
    ];

    var chart = jQuery.jqplot ('memory_chart', [data],
    {
        legend: { show: true, location: 'e' },
        seriesColors: [ "#579575", "#839557", "#c5b47f", "#EAA228" ],
        seriesDefaults: {
            renderer: jQuery.jqplot.PieRenderer,
            shadow: true,
            rendererOptions: {
                showDataLabels: true,
                sliceMargin: 8,
            }
        },
        grid: { 
            gridLineColor: 'transparent', 
            background: 'transparent', 
            borderColor: 'transparent', 
            shadow: false 
        }
    });

    chart.redraw();
}

// vim: ts=4 syntax=javascript
