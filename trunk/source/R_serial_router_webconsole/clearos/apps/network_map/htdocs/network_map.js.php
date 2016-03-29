<?php

/**
 * System time javascript helper.
 *
 * @category   apps
 * @package    network-map
 * @subpackage javascript
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('date');

///////////////////////////////////////////////////////////////////////////////
// J A V A S C R I P T
///////////////////////////////////////////////////////////////////////////////

header('Content-Type:application/x-javascript');
?>

$(document).ready(function() {

    // Prep
    //-----

    $("#unknown_details_link").hide();
    $("#unknown_count").hide();

    // Main
    //-----

    if ($("#unknown_details_link").length)
        getData();
});

function getData() {
    $.ajax({
        url: '/app/network_map/unknown_summary/get_count',
        method: 'GET',
        dataType: 'json',
        success : function(payload) {
            showData(payload);
            window.setTimeout(getData, 10000);
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            window.setTimeout(getData, 10000);
        }

    });
}

function showData(payload) {
    previous_count = $("#unknown_count").html();

    if (previous_count != payload.count)
        $("#unknown_count").addClass('theme-text-highlight');
    else
        $("#unknown_count").removeClass('theme-text-highlight');

    $("#unknown_count").html(payload.count);
    $("#unknown_scanning").hide();
    $("#unknown_count").show();

    if (payload.count > 0)
        $("#unknown_details_link").show();
    else
        $("#unknown_details_link").hide();
}

// vim: ts=4 syntax=javascript
