
    
// Credit for this function belongs to http://stackoverflow.com/questions/5047498/how-do-you-animate-the-value-for-a-jquery-ui-progressbar
$(function() {
    $.fn.animate_progressbar = function(value, duration, easing, complete) {
        // Jump the progress bar if 0 or 100
        if (value == 0 || value == 100) {
            $(this.selector).progressbar({value: value});
            return;
        }
        // If we're decreasing, jump to new position rather than animate
        // This script does not set the value internally to the Jquery extension
        // So we hack it a bit to find out the current value
        if (value < Math.round($(this.selector).children().width() / $(this.selector).width() * 100)) {
            $(this.selector).progressbar({value: value});
            return;
        }
        if (value == null)
            value = 0;
        if (duration == null)
            duration = 1000;
        if (easing == null)
            easing = 'swing';
        if (complete == null)
            complete = function(){};
        var progress = this.find('.ui-progressbar-value');
        progress.stop(true).animate({
            width: value + '%'
        }, duration, easing, function(){
            if(value>=99.5){
                progress.addClass('ui-corner-right');
            } else {
                progress.removeClass('ui-corner-right');
            }
            complete();
        });
    }
});
    
$(function(){
	// Anchors
	$(".theme-anchor").button();

	// Buttons
	$(".theme-form-submit").button();

	// Anchor and button sets
	$(".theme-button-set").buttonset();

	// Progress bar
	/*
	$(".theme-progress-bar").progressbar({ 
		value: 0
	});
*/

    // Charts
    $.jqplot.config.enablePlugins = true;
   
	// Forms / FIXME
	$('fieldset').addClass('ui-widget-content ui-corner-all');
	$('legend').addClass('ui-widget-header ui-corner-all');
	$('label').addClass('ui-widget ui-corner-all');

    // Wizard - "next" button triggered by hitting enter button
    $("#theme_wizard_nav_next").keyup(function(event){
        if (event.keyCode == 13) {
            $("#wizard_nav_next").click();
        }
    });

    $("#theme_wizard_nav_previous").keyup(function(event){
        if (event.keyCode == 13) {
            $("#wizard_nav_previous").click();
        }
    });
});

function theme_clearos_dialog_box(id, title, message, options)
{
    $('#theme-page-container').append('<div id=\"' + id + '\" title=\"' + title + '\">' +
          '<div class=\"dialog_alert_icon\"></div>' +
          '<div class=\"dialog_alert_text\">' + message + '</div>' +
      '</div>'
    );
    if (options == undefined)
        options = new Object();
    $('#' + id).dialog({
        modal: true,
        resizable: false,
        draggable: false,
        width: (options.width == undefined) ? 400 : options.width,
        buttons: {
            'close': {
                text: lang_close,
                click: function() {
                    $(this).dialog('close');
                    if (options.reload_on_close)
                        window.location.reload();
                    else if (options.redirect_on_close)
                        window.location = options.redirect_on_close;
                }
            }
        }
    });
    $('.ui-dialog-titlebar-close').hide();
}

function theme_clearos_is_authenticated()
{

    data_payload = 'ci_csrf_token=' + $.cookie('ci_csrf_token');
    if ($('#sdn_username').val() != undefined)
        data_payload += '&username=' + $('#sdn_username').val();
    $('#sdn_login_dialog_message_bar').html('');
    if (auth_options.action_type == 'login') {
        if ($('#sdn_password').val() == '') {
            $('#sdn_login_dialog_message_bar').html(lang_sdn_password_invalid);
            $('.autofocus').focus();
            return;
        } else {
            data_payload += '&password=' + $('#sdn_password').val();
        }
    } else if (auth_options.action_type == 'lost_password') {
        if ($('#sdn_email').val() == '') {
            $('#sdn_login_dialog_message_bar').html(lang_sdn_email_invalid);
            $('.autofocus').focus();
            return;
        } else {
            data_payload += '&email=' + $('#sdn_email').val();
        }
    }

    // Translations
    //-------------

    lang_warning = '<?php echo lang("base_warning"); ?>';

    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: data_payload,
        url: '/app/marketplace/ajax/is_authenticated',
        success: function(data) {
            if (data.code == 0 && data.authorized) {
                // Might have pages where account is displayed (eg. Marketplace)
                $('#display_sdn_username').html(data.sdn_username);
                // Only case where authorized is true.
                $('#sdn_login_dialog').dialog('close');
                // If we're logged in and there is a 'check_sdn_edit' function defined on page, check to see if we need to get settings
                if (window.check_sdn_edit)
                    check_sdn_edit();
                if (auth_options.action_type == 'login' && auth_options.reload_after_auth)
                    window.location.reload();
            } else if (data.code == 0 && !data.authorized) {

                // Open dialog and change some look and feel
                $('#sdn_login_dialog').dialog('open');
                $('.ui-dialog-titlebar-close').hide();

                // If email was submitted...reset was a success...
                if (data.email != undefined) {
                    $('#sdn_lost_password_group').hide();
                    $('#sdn_login_dialog_message_bar').css('color', '#686868');
                    $('#sdn_login_dialog_message_bar').html(lang_sdn_password_reset + ': ' + data.email);
                    $('.ui-dialog-buttonpane button:contains(\'' + lang_reset_password_and_send + '\') span').parent().hide();
                    return;
                }
                
                // Marketplace 1.1 sends back array of admins
                $.each(data.sdn_admins, function(key, value) {   
                    $('#sdn_username')
                    .append($('<option>', { value : value })
                    .text(value)); 
                });

            } else if (data.code == 10) {
                // Code 10 is an invalid email
                $('#sdn_login_dialog_message_bar').html(lang_sdn_email_invalid);
            } else if (data.code == 11) {
                // Code 11 is an email mismatch for lost password
                $('#sdn_login_dialog_message_bar').html(lang_sdn_email_mismatch);
            } else if (data.code > 0) {
                $('#sdn_login_dialog_message_bar').html(lang_sdn_password_invalid);
            } else if (data.code < 0) {
                theme_clearos_dialog_box('login_failure', lang_warning, data.errmsg);
                return;
            }
            $('.autofocus').focus();
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                theme_clearos_dialog_box('some-error', lang_warning, xhr.responseText.toString());
            $('#sidebar_setting_status').html('---');
        }
    });
}

function theme_clearos_on_page_ready(my_location)
{
    get_marketplace_data(my_location.basename);

    // Insert login dialog
    $('#theme-page-container').append(
       '<div id=\'sdn_login_dialog\' title=\'' + sdn_org + ' ' + lang_sdn_authentication_required + '\' class=\'theme-hidden\'> \
          <p style=\'text-align: left\'>' + lang_sdn_authentication_required_help + '</p> \
          <div style=\'float: left; width: 120px; text-align: right; padding-top: 4px;\'>' + lang_username + '</div> \
          <div style=\'float: left; margin-left: 10px;\'><select id=\'sdn_username\' style=\'width: 120px;\'></select></div> \
          <br><br> \
          <div id=\'sdn_password_group\'> \
            <div style=\'float: left; width: 120px; text-align: right; padding-top: 5px;\'>' + lang_password + '</div> \
            <div style=\'float: left; margin-left: 10px;\'> \
              <input id=\'sdn_password\' type=\'password\' style=\'width: 120px\' name=\'password\' value=\'\' class=\'autofocus\' /> \
            </div> \
            <div style=\'padding: 2px 2px 0px 0px; width: 250px; text-align: right; clear: both;\'> \
              <a href=\'#\' id=\'sdn_forgot_password\' style=\'font-size: 9px;\'>' + lang_forgot_password + '</a> \
            </div> \
          </div> \
          <div id=\'sdn_lost_password_group\' class=\'theme-hidden\'> \
            <div style=\'float: left; width: 120px; text-align: right; padding-top: 5px;\'>' + lang_sdn_email + '</div> \
            <div style=\'float: left; width: 120px; margin-left: 10px;\'> \
              <input id=\'sdn_email\' type=\'text\' style=\'width: 120px\' name=\'sdn_email\' value=\'\' class=\'autofocus\' /> \
            </div> \
          </div> \
          <div style=\'padding: 0px 170px 10px 0px; text-align: right\' id=\'sdn_login_dialog_message_bar\'></div> \
        </div>'
    );

    $('#sdn_login_dialog').dialog({
        autoOpen: false,
        bgiframe: true,
        title: false,
        modal: true,
        resizable: false,
        draggable: false,
        closeOnEscape: false,
        height: 250,
        width: 450,
        buttons: {
            'authenticate': {
                text: lang_authenticate,
                click: function() {
                    auth_options.action_type = 'login';

                    if ($('#sdn_lost_password_group').is(':visible'))
                        auth_options.action_type = 'lost_password';
                    theme_clearos_is_authenticated();
                }
            },
            'close': {
                text: lang_close,
                click: function() {
                    // Go back to basename
                    $(this).dialog('close');
                    // If not at default controller, reload page
                    if (!my_location.default_controller)
                        return;
                    window.location = 'https://' + document.location.host + '/app/' + my_location.basename;
                }
            }
        }
    });

    $('input#sdn_password').keyup(function(event) {
        if (event.keyCode == 13) {
            auth_options.action_type = 'login';
            theme_clearos_is_authenticated();
        }
    });

    $('input#sdn_email').keyup(function(event) {
        if (event.keyCode == 13) {
            auth_options.action_type = 'lost_password';
            theme_clearos_is_authenticated();
        }
    });

    $('a#sdn_forgot_password').click(function (e) {
        e.preventDefault();
        $('#sdn_login_dialog_message_bar').html('');
        $('#sdn_password_group').remove();
        $('#sdn_lost_password_group').show();
        $('.autofocus').focus();
        $('.ui-dialog-buttonpane button:contains(\'' + lang_authenticate + '\') span').text(lang_reset_password_and_send);
    });

    $('#theme-banner-my-account-nav').click(function (e) {
        e.preventDefault();
        if (!$('#theme-banner-my-account-container').is(':visible')) {
            $('#theme-banner-my-account-container').show('slide', {direction: 'right'}, 500);
        } else {
            $('#theme-banner-my-account-container').hide('slide', {direction: 'right'}, 500);
        }
    });

    $('#theme-banner-my-account-container').mouseleave(function (e) {
        setTimeout(function() {$('#theme-banner-my-account-container').hide('slide', {direction: 'right'}, 500)}, 1000);
    });
}

function get_marketplace_data(basename) {

    // Let's see if we have a connection to the Internet
    // Block on this call (async set to false)
    $.ajax({
        url: '/app/network/get_internet_connection_status',
        method: 'GET',
        dataType: 'json',
        async: false,
        success : function(status) {
            if (status != 'offline')
                internet_connection = true;
        },
        error: function (xhr, text_status, error_thrown) {
        }
    });

    // Avoid furter calls if target fields do not exist
    if ($('#sidebar_additional_info').length == 0)
        return;

    // No connection to Internet...let's bail
    if (!internet_connection) {
        $('#sidebar_additional_info').html('<a href=\'/app/network\' class=\'highlight-link\'>' + lang_internet_down + '</a>');
        $('#sidebar_additional_info_row').show(200);
        return;
    }

    $.ajax({
        url: '/app/marketplace/ajax/get_app_details/' + basename,
        method: 'GET',
        dataType: 'json',
        success : function(json) {
            if (json.code != undefined && json.code != 0) {
                $('#sidebar_additional_info').css('color', 'red');
                $('#sidebar_additional_info_row').show(200);
                if (json.code < 0) {
                    // Could put real message for codes < 0, but it gets a bit technical
                    $('#sidebar_additional_info').html(lang_marketplace_connection_failure);
                } else {
                    if (json.code == 3)
                        $('#sidebar_additional_info').html('<a href=\'/app/registration/register\'>' + json.errmsg + '</a>');
                    else
                        $('#sidebar_additional_info').html(json.errmsg);
                }
                return;
            } else {
                // We add rows in the reverse order to keep this section under the Version/Vendor

                // Evaluation
                if (json.license_info != undefined && json.license_info.evaluation != undefined && json.license_info.evaluation == true) {
                    if (json.license_info.eval_limitations != undefined) {
                        $('#sidebar_additional_info_row').after(
                            c_row(
                                lang_marketplace_eval_limitations,
                                '<a id=\'eval-limit-anchor\' href=\'javascript: void(0)\'>' + lang_yes + '</a>' +
                                '<div class=\'theme-rhs-tooltip\'>' + json.license_info.eval_limitations + '</div>'
                            )
                        );
                        $('#eval-limit-anchor').tooltip({
                            offset: [-140, -315],
                            position: 'center left',
                            effect: 'slide',
                            direction: 'left',
                            slideOffset: 110, 
                            opacity: 0.95
                        });
                    }
                    $('#sidebar_additional_info_row').after(
                        c_row(
                            lang_marketplace_trial_ends,
                            $.datepicker.formatDate('M d, yy', new Date(json.license_info.expire))
                        )
                    );
                    $('#sidebar_additional_info_row').after(
                        c_row(
                            lang_status,
                            '<span style=\'color: red\'>' + lang_marketplace_evaluation + '</span>'
                        )
                    );
                } else {
                    // Redemption period
                    if (json.license_info != undefined && json.license_info.redemption != undefined && json.license_info.redemption == true) {
                        $('#sidebar_additional_info_row').after(
                            c_row(
                                lang_status,
                                '<span style=\'color: red\'>' + lang_marketplace_redemption + '</span>'
                            )
                        );
                    }

                    // No Subscription
                    if (json.license_info != undefined && json.license_info.no_subscription != undefined && json.license_info.no_subscription == true) {
                        $('#sidebar_additional_info_row').after(
                            c_row(
                                lang_status,
                                '<span style=\'color: red\'>' + lang_marketplace_expired_no_subscription + '</span>'
                            )
                        );
                    }

                    // Subscription?  A unit of 100 or greater represents a recurring subscription
                    if (json.license_info != undefined && json.license_info.unit >= 100) {
                        var bill_cycle = lang_marketplace_billing_cycle_monthly;
                        if (json.license_info.unit == 1000)
                            bill_cycle = lang_marketplace_billing_cycle_yearly;
                        else if (json.license_info.unit == 2000)
                            bill_cycle = lang_marketplace_billing_cycle_2_years;
                        else if (json.license_info.unit == 3000)
                            bill_cycle = lang_marketplace_billing_cycle_3_years;
        
                        $('#sidebar_additional_info_row').after(
                            c_row(
                                lang_marketplace_billing_cycle,
                                bill_cycle
                            )
                        );
                        if (json.license_info.expire != undefined) {
                            $('#sidebar_additional_info_row').after(
                                c_row(
                                    lang_marketplace_renewal_date,
                                    $.datepicker.formatDate('M d, yy', new Date(json.license_info.expire))
                                )
                            );
                        }
                    }
                }

                // Support Policy
                if (json.supported != undefined && !json.hide_support_policy) {
                    // TODO - there are some clearcenter references here
                    $('#sidebar_additional_info_row').after(
                        c_row(
                            lang_marketplace_support_policy,
                            '<div id=\'theme-support-policy-trigger\'>' +
                            '<div class=\'theme-support theme-support-' + (json.supported & 1) + '\'></div>' +
                            '<div class=\'theme-support theme-support-' + (json.supported & 2) + '\'></div>' +
                            '<div class=\'theme-support theme-support-' + (json.supported & 4) + '\'></div>' +
                            '<div class=\'theme-support theme-support-' + (json.supported & 8) + '\'></div>' +
                            '<div class=\'theme-support theme-support-' + (json.supported & 16) + '\'></div>' +
                            '</div>' +
                            '<div class=\'theme-rhs-tooltip\'>' +
                            '<p class=\'theme-support-legend-title\'>' + lang_marketplace_support_legend + '</p>' +
                            '<div class=\'theme-support theme-support-1\' style=\'margin-right: 5px;\'></div>' +
                            '<div class=\'theme-support-type\'>' + lang_marketplace_support_1_title + '</div>' +
                            lang_marketplace_support_1_description +
                            '</p>' +
                            '<p><div class=\'theme-support theme-support-2\' style=\'margin-right: 5px;\'></div>' +
                            '<div class=\'theme-support-type\'>' + lang_marketplace_support_2_title + '</div>' +
                            lang_marketplace_support_2_description +
                            '</p>' +
                            '<p><div class=\'theme-support theme-support-4\' style=\'margin-right: 5px;\'></div>' +
                            '<div class=\'theme-support-type\'>' + lang_marketplace_support_4_title + '</div>' +
                            lang_marketplace_support_4_description +
                            '</p>' +
                            '<p><div class=\'theme-support theme-support-8\' style=\'margin-right: 5px;\'></div>' +
                            '<div class=\'theme-support-type\'>' + lang_marketplace_support_8_title + '</div>' +
                            lang_marketplace_support_8_description +
                            '</p>' +
                            '<p><div class=\'theme-support theme-support-16\' style=\'margin-right: 5px;\'></div>' +
                            '<div class=\'theme-support-type\'>' + lang_marketplace_support_16_title + '</div>' +
                            lang_marketplace_support_16_description +
                            '</p>' +
                            '<div class=\'theme-support-learn-more\'>' +
                            '<a href=\'http://www.clearcenter.com/clearcare/landing\' target=\'_blank\'>' + lang_marketplace_learn_more + '...</a>' +
                            '</div>' +
                            '</div>'
                        )
                    );
                    $('#theme-support-policy-trigger').tooltip({
                        offset: [-140, -310],
                        position: 'center left',
                        effect: 'slide',
                        direction: 'left',
                        slideOffset: 110, 
                        opacity: 0.95,
                        delay: 500,
                        predelay: 1000
                    });
                }

                // Version updates
                if (!json.up2date) {
                    $('#sidebar_additional_info_row').after(
                        c_row(
                            lang_marketplace_upgrade,
                            json.latest_version
                        )
                    );
                }
            }
            if (json.complementary_apps != undefined && json.complementary_apps.length > 0 && !json.hide_recommended_apps) {
                comp_apps = '<h3>' + lang_marketplace_recommended_apps + '</h3>' +
                    '<div>' + lang_marketplace_sidebar_recommended_apps.replace('APP_NAME', '<b>' + json.name + '</b>') + ':</div>';
                comp_apps += '<table border=\'0\' width=\'100%\'>';
                for (index = 0 ; index < json.complementary_apps.length; index++) {
                    comp_apps += '<tr><td width=\'5\' valign=\'top\'>&#8226;</td><td width=\'60%\'><a href=\'/app/marketplace/view/' +
                        json.complementary_apps[index].basename + '\'>' +
                        json.complementary_apps[index].name + '</a></td><td width=\'35%\' valign=\'top\'>\n';
                    for (var counter = 5 ; counter > Math.round(json.complementary_apps[index].rating); counter--)
                        comp_apps += '<div class=\'star_off\' />';
                    for (var counter = 0 ; counter < Math.round(json.complementary_apps[index].rating); counter++)
                        comp_apps += '<div class=\'star_on\' />';
                    comp_apps += '</td></tr>';
                }
                comp_apps += '</table>';
                $('#sidebar-recommended-apps').html(comp_apps);
            }
        },
        error: function (xhr, text_status, error_thrown) {
            // FIXME: Firebug issue?
            //if (xhr['abort'] == undefined)
            //    $('#sidebar_additional_info').html(xhr.responseText.toString());
        }
    });
}

function c_row(field, value) {
    // TODO style should be in CSS
    return '<tr><td><b>' + field + '</b></td><td>' + value + '</td></tr>';
}
