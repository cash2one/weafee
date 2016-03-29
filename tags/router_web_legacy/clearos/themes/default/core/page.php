<?php



//////////////////////////////////////////////////////////////////////////////
// P A G E  L A Y O U T
//////////////////////////////////////////////////////////////////////////////

function theme_page($page)
{
    if ($_SERVER['SERVER_PORT'] == 1501 && !preg_match('/.*hide_devel$/', $_SERVER['REQUEST_URI'])) {

        $page['devel_theme_source'] = (preg_match('/^\/usr\/clearos/', __FILE__)) ? 'Live' : 'Development';

        $app_style = ($page['devel_app_source'] == 'Live') ? 'green' : '#daa520';
        $framework_style = ($page['devel_framework_source'] == 'Live') ? 'green' : '#daa520';
        $theme_style = ($page['devel_theme_source'] == 'Live') ? 'green' : '#daa520';

        $page['devel_message'] = 
            "<p>" .
                "App: <span style='color: $app_style'>" . $page['devel_app_source'] . "</span> | " .
                "Framework: <span style='color: $framework_style'>" . $page['devel_framework_source'] . "</span> | " .
                "Theme: <span style='color: $theme_style'>" . $page['devel_theme_source'] . "</span>" .
            "</p>";
    } else {
        $page['devel_message'] = '';
    }

    if ($page['type'] == MY_Page::TYPE_CONFIGURATION)
        return _configuration_page($page);
    else if ($page['type'] == MY_Page::TYPE_WIDE_CONFIGURATION)
        return _wide_configuration_page($page);
    else if ($page['type'] == MY_Page::TYPE_REPORT) // TODO: deprecated
        return _wide_configuration_page($page);
    else if ($page['type'] == MY_Page::TYPE_REPORTS)
        return _report_page($page);
    else if ($page['type'] == MY_Page::TYPE_REPORT_OVERVIEW)
        return _report_overview_page($page);
    else if ($page['type'] == MY_Page::TYPE_SPOTLIGHT)
        return _spotlight_page($page);
    else if ($page['type'] == MY_Page::TYPE_DASHBOARD)
        return _dashboard_page($page);
    else if (($page['type'] == MY_Page::TYPE_SPLASH) || ($page['type'] == MY_Page::TYPE_SPLASH_ORGANIZATION))
        return _splash_page($page);
    else if ($page['type'] == MY_Page::TYPE_LOGIN)
        return _login_page($page);
    else if ($page['type'] == MY_Page::TYPE_WIZARD)
        return _wizard_page($page);
    else if ($page['type'] == MY_Page::TYPE_CONSOLE)
        return _console_page($page);
}

/**
 * Returns the configuration type page.
 *
 * @param array $page page data
 *
 * @return string HTML output
 */

function _configuration_page($page)
{
    $menus = _get_menu($page['menus']);

    return "
<!-- Body -->
<body>
" . $page['devel_message'] . "

<!-- Page Container -->
<div id='theme-page-container'>
    " .
    _get_banner($page, $menus) .
    "
    <!-- Main Content Container -->
    <div id='theme-main-content-container'>
        <div class='theme-main-content-top'>
            <div class='theme-content-border-top'></div>
            <div class='theme-content-border-left'></div>
            <div class='theme-content-border-right'></div>
        </div>
        <div class='theme-core-content'>
        " .
            _get_left_menu($menus) .
            _get_basic_app_layout($page) .
        "
        </div>
        " .
        _get_footer($page) .
        "
    </div>
</div>
</body>
</html>
";
}

/**
 * Returns the wide configuration page.
 *
 * @param array $page page data   
 *
 * @return string HTML output
 */   

function _wide_configuration_page($page)
{
    $menus = _get_menu($page['menus']);

    return "
<!-- Body -->
<body>
" . $page['devel_message'] . "

<!-- Page Container -->
<div id='theme-page-container'>
    " .
    _get_banner($page, $menus) .
    "
    <!-- Main Content Container -->
    <div id='theme-main-content-container'>
        <div class='theme-main-content-top'>
            <div class='theme-content-border-top'></div>
            <div class='theme-content-border-left'></div>
            <div class='theme-content-border-right'></div>
        </div>
        <div class='theme-core-content'>
        " .  _get_left_menu($menus) . "
            <div id='theme-content-container'>
                <div id='theme-help-box-container'>
                    <div class='theme-help-box'>
                    " . $page['page_help'] . "
                    </div>
                </div>
                <div id='theme-content-report'>
                    " . _get_message() . "
                    " . $page['app_view'] . "
                </div>
            </div>

        </div>
        " .
        _get_footer($page) .
        "
    </div>
</div>
</body>
</html>
";
}

/**
 * Returns the report page.
 *
 * @param array $page page data   
 *
 * @return string HTML output
 */   

function _report_page($page)
{
    $menus = _get_menu($page['menus']);

    return "
<!-- Body -->
<body>
" . $page['devel_message'] . "

<!-- Page Container -->
<div id='theme-page-container'>
    " .
    _get_banner($page, $menus) .
    "
    <!-- Main Content Container -->
    <div id='theme-main-content-container'>
        <div class='theme-main-content-top'>
            <div class='theme-content-border-top'></div>
            <div class='theme-content-border-left'></div>
            <div class='theme-content-border-right'></div>
        </div>
        <div class='theme-core-content'>
        " .
            _get_left_menu($menus) .
        "
            <!-- Content -->
            <div id='theme-content-container'>
                <div id='theme-help-box-container'>
                    <div class='theme-help-box'>
                    " . $page['page_help'] . "
                    </div>
                </div>
                <div id='theme-sidebar-container'>
                    <div class='theme-sidebar-report-top'>
                    " . $page['page_report_helper'] . "
                    </div>
                    $report
                    <div class='theme-sidebar-bottom'></div>
                </div>
                <div id='theme-content-left'>
                    " . _get_message() . "
                    " . $page['page_report_chart'] . "
                </div>
                <div>
                    " . $page['page_report_table'] . "
                </div>
            </div>
        </div>
        " .
        _get_footer($page) .
        "
    </div>
</div>
</body>
</html>
";
}

/**
 * Returns the configuration type page.
 *
 * @param array $page page data
 *
 * @return string HTML output
 */

function _report_overview_page($page)
{
    $menus = _get_menu($page['menus']);

    return "
<!-- Body -->
<body>
" . $page['devel_message'] . "

<!-- Page Container -->
<div id='theme-page-container'>
    " .
    _get_banner($page, $menus) .
    "
    <!-- Main Content Container -->
    <div id='theme-main-content-container'>
        <div class='theme-main-content-top'>
            <div class='theme-content-border-top'></div>
            <div class='theme-content-border-left'></div>
            <div class='theme-content-border-right'></div>
        </div>
        <div class='theme-core-content'>
        " .
            _get_left_menu($menus) .
        "
            <!-- Content -->
            <div id='theme-content-container'>
                <div id='theme-help-box-container'>
                    <div class='theme-help-box'>
                    " . $page['page_help'] . "
                    </div>
                </div>
                <div id='theme-sidebar-container'>
                    <div class='theme-sidebar-report-top'>
                    " . $page['page_report_helper'] . "
                    </div>
                    &nbsp; 
                    <!--div class='theme-sidebar-top'>
                    " . $page['page_summary'] . "
                    </div--><!--roy-->
                    $report
                    <div class='theme-sidebar-bottom'></div>
                </div>
                <div id='theme-content-left'>
                    " . _get_message() . "
                    " . $page['app_view'] . "
                </div>
            </div>
        </div>
        " .
        _get_footer($page) .
        "
    </div>
</div>
</body>
</html>
";
}

/**
 * Returns the dashboard page.
 *
 * @param array $page page data   
 *
 * @return string HTML output
 */   

function _dashboard_page($page)
{
    $menus = _get_menu($page['menus']);

    return "
<!-- Body -->
<body>

<!-- Page Container -->
<div id='theme-page-container'>
    " .
    _get_banner($page, $menus) .
    "
    <!-- Main Content Container -->
    <div id='theme-main-content-container'>
        <div class='theme-main-content-top'>
            <div class='theme-content-border-top'></div>
            <div class='theme-content-border-left'></div>
            <div class='theme-content-border-right'></div>
        </div>
        <div class='theme-core-content'>
        " .
            _get_left_menu($menus) .
            _get_basic_app_layout($page) .
        "
        </div>
        " .
        _get_footer($page) .
        "
    </div>
</div>
</body>
</html>
";
}

/**
 * Returns the spotlight page.
 *
 * @param array $page page data   
 *
 * @return string HTML output
 */   

function _spotlight_page($page)
{
    $menus = _get_menu($page['menus']);

    return "
<!-- Body -->
<body>

<!-- Page Container -->
<div id='theme-page-container'>
    " .
    _get_banner($page, $menus) .
    "
    <!-- Main Content Container -->
    <div id='theme-main-content-container'>
        <div class='theme-main-content-top'>
            <div class='theme-content-border-top'></div>
            <div class='theme-content-border-left'></div>
            <div class='theme-content-border-right'></div>
        </div>
        <div class='theme-core-content'>

            <!-- Left menu Javascript -->
            <script type='text/javascript'> 
                $(document).ready(function(){
                    $('#theme-left-menu').accordion({ heightStyle: 'content', active: 0, collapsible: false });
                });
            </script>

            <!-- Left Menu -->
            <div id='theme-left-menu-container'>
                <div id='theme-left-menu-container-border'>"
                    . $page['page_app_helper'] . " 
                </div>
            </div>

        <div id='theme-content-container'>
            <div id='theme-help-box-container'>
                <div class='theme-help-box'>
                " . $page['page_help'] . "
                </div>
            </div>
            " . _get_message() . "
            " . $page['app_view'] . "
        </div>
        </div>
    </div>
</div>
</body>
</html>
";
}

/**
 * Returns the login type page.
 *
 * @param array $page page data   
 *
 * @return string HTML output
 */   

function _login_page($page)
{
    return "
<!-- Body -->
<body>

<!-- Page Container -->
<div class='theme-login-container'>
    <div class='theme-login-logo'></div>
    " . _get_message() . "
    " . $page['app_view'] . "
</div>
</body>
</html>
";
}

/**
 * Returns the splash page.
 *
 * @param array $page page data   
 *
 * @return string HTML output
 */   

function _splash_page($page)
{
    $org_css = preg_replace('/\/core\/.*/', '', realpath(__FILE__)) . '/css/theme-organization.css';

    if (!preg_match('/Community/', $page['os_name']) && ($page['type'] == MY_Page::TYPE_SPLASH_ORGANIZATION) && file_exists($org_css))
        $class = 'theme-splash-organization-logo';
    else
        $class = 'theme-splash-logo';

    return "
<!-- Body -->
<body>

<!-- Page Container -->
<div id='theme-page-console-container'>
    <div class='$class'></div>
    <div id='theme-content-splash-container'>
        " . _get_message() . "
        " . $page['app_view'] . "
    </div>
</div>
</body>
</html>
";
}

/**
 * Returns the wizard page.
 *
 * @param array $page page data   
 *
 * @return string HTML output
 */   

function _wizard_page($page)
{
    $menus = _get_menu($page['wizard_menu'], TRUE);

    // Add a sidebar on normal pages.  Some pages need the full width.
    $content = _get_message() . $page['app_view'];
    $nav = _get_wizard_navigation($page['wizard_navigation']);
    $intro = '';
    $sidebar = '';

    if ($page['page_wizard_intro']) {
        $intro = "
            <div id='theme-help-box-container'>
                <div class='theme-help-box'>
                " . $page['page_wizard_intro'] . "
                </div>
            </div>
        ";
    }

    if ($page['page_inline_help']) {
        $sidebar = "
            <div id='theme-sidebar-container'>
                <!--div class='theme-sidebar-top'>
                " . $page['page_inline_help'] . "
                </div--><!--roy-->
                <div>
                " . $page['page_report'] . "
                </div>
            </div>
        ";
    }

    // Use a wide "intro" layout for pages without a sidebar.
    // Add a sidebar div if a sidebar exists.

    $content = $intro . $content;

    if ($sidebar)
        $content = "<div id='theme-content-left'>$content</div>";

    return "
<!-- Body -->
<body>

<!-- Page Container -->
<div id='theme-page-container'>
    " .
    _get_banner($page) . 
    "
    <!-- Main Content Container -->
    <div id='theme-main-content-container'>
        <div class='theme-main-content-top'>
            <div class='theme-content-border-top'></div>
            <div class='theme-content-border-left'></div>
            <div class='theme-content-border-right'></div>
        </div>
        <div class='theme-core-content'>
            " .
                _get_left_menu($menus, TRUE, $nav) .
            "
            <!-- Content -->
            <div id='theme-content-container'>
                $sidebar
                $content
            </div>
        </div>
        " .
        _get_footer($page) .
        "
    </div>
</div>
</body>
</html>
";
}

/**
 * Returns the console page.
 *
 * @param array $page page data   
 *
 * @return string HTML output
 */   

function _console_page($page)
{
    if (isset($page['logged_in']) && $page['logged_in'])
        $logout_link = "<a href='/app/base/session/logout/graphical_console'><span id='theme-banner-logout'>" . lang('base_logout') . "</span></a>";
    else
        $logout_link = '';

    return "
<!-- Body -->
<body>

<!-- Page Container -->
<div id='theme-page-console-container'>
    <!-- Banner -->

    <div id='theme-banner-console-container'>
        <div id='theme-banner-background'></div>
        <div id='theme-banner-logo'></div>
        <div class='theme-banner-name-holder'>
            $logout_link
        </div>
    </div>

    <!-- Main Content Container -->
    <div id='theme-main-content-console-container'>
        <div class='theme-main-content-console-top'>
            <div class='theme-content-border-console-top'></div>
            <div class='theme-content-border-console-left'></div>
            <div class='theme-content-border-console-right'></div>
        </div>
        <div class='theme-core-content-console'>
            <div id='theme-content-container'>
                <div id='theme-sidebar-container'>
                    <!--div class='theme-sidebar-top'>
                    " . $page['page_inline_help'] . "
                    </div--><!--roy-->
                </div>
                <div id='theme-content-left'>
                    " . _get_message() . "
                    " . $page['app_view'] . "
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div id='theme-footer-console-container'>" .
            lang('base_looking_for_a_command_line?') . " "
            . anchor_custom('/app/graphical_console/shutdown', lang('base_go_to_the_command_line'), 'high') .
            "
        </div>
    </div>
</div>
</body>
</html>
";
}

//////////////////////////////////////////////////////////////////////////////
// L A Y O U T  H E L P E R S
//////////////////////////////////////////////////////////////////////////////

function _get_message()
{
    $framework =& get_instance();

    if (! $framework->session->userdata('message_text'))
        return;

    $message = $framework->session->userdata('message_text');
    $type =  $framework->session->userdata('message_code');
    $title = $framework->session->userdata('message_title');

    $framework->session->unset_userdata('message_text');
    $framework->session->unset_userdata('message_code');
    $framework->session->unset_userdata('message_title');

    return theme_infobox($type, $title, $message);
}

function _get_basic_app_layout($page)
{
    if ($page['page_report']) {
        $report = "
            <div>
            " . $page['page_report'] . "
            </div>
        ";
    } else {
        $report = '';
    }

    return "
        <!-- Content -->
        <div id='theme-content-container'>
            <div id='theme-help-box-container'>
                <div class='theme-help-box'>
                " . $page['page_help'] . "
                </div>
            </div>
            <div id='theme-sidebar-container'>
                <!--div class='theme-sidebar-top'>
                " . $page['page_summary'] . "
                </div--><!--roy-->
                $report
                <div class='theme-sidebar-bottom'></div>
            </div>
            <div id='theme-content-left'>
                " . _get_message() . "
                " . $page['app_view'] . "
            </div>
        </div>
    ";
}

function _get_footer($page) 
{
    return "
    <!-- Footer -->
    <div id='theme-footer-container'>
        Web Theme - Copyright &copy;  www.weafee.net  All Rights Reserved.
    </div>
    ";
}

/**
 * Returns the top banner.
 *
 * @param array $page page data
 *
 * @return string banner HTML
 */

function _get_banner($page, $menus = array())
{
    $framework =& get_instance();

    // In this theme, we treat the "Spotlight" category a special way
    //---------------------------------------------------------------

    $banner_links = '';
    $framework =& get_instance();

    // Set to FALSE if you hate icons
    $use_icons = TRUE;
    $icon_mapping = array(
        lang('base_subcategory_dashboard') => "<i class='theme-banner-nav-icons fa fa-dashboard' title='" . lang('base_subcategory_dashboard') . "'></i>",
        lang('base_category_my_account') => "<i class='theme-banner-nav-icons fa fa-user' title='" . lang('base_category_my_account') . "'></i>",
    );

    if (! isset($framework->session->userdata['wizard'])) {
        foreach ($page['menus'] as $url => $details) {
            if ($details['category'] == lang('base_category_spotlight'))
                $banner_links .= '<a href="' . $url . '">' . ($use_icons ? $icon_mapping[$details['title']] : $details['title']) . '</a>|';
        }
    }

    $top_menu = empty($menus) ? '' : _get_top_menu($menus);

    // FIXME: continue with edge cases
    $my_account = "<h1 style='padding-bottom: 5px;'>" . lang('base_category_my_account') . "</h1>";
    $my_account .= "<div class='theme-banner-my-account-login'>" . lang('base_username') . ":  " . $framework->session->userdata['username'] . "</div>";
    if (! isset($framework->session->userdata['wizard'])) {
        foreach ($page['menus'] as $route => $details) {
            if ($details['category'] == lang('base_category_my_account')) {
                $my_account .= "<div class='theme-banner-my-account-links'><a href='$route'>" . $details['title'] . "</a></div>\n";
            }
        }
    }

    // Add 'My Account'
    $banner_links .= "<a id='theme-banner-my-account-nav' href='#'>" .
        ($use_icons ? $icon_mapping[lang('base_category_my_account')] : lang('base_category_my_account')) . "</a>";

    // div block directly after link must contain contents of pop-up
    $banner_links .= "<div id='theme-banner-my-account-container'><div id='theme-banner-my-account-content'>" . $my_account . "</div>";

    // Add styled logout button
    $banner_links .= "<div class='theme-banner-my-account-logout'><a class='' href='/app/base/session/logout'><i class='fa fa-sign-out' style='padding-right: 5px;'></i>" . lang('base_logout') . "</a></div>";

    // Close out div
    $banner_links .= "</div>";

    return "
<!-- Banner -->
<div id='theme-banner-container'>
    <div id='theme-banner-background'></div>
    <div id='theme-banner-logo'></div>
    <div class='theme-banner-name-holder'>
       <div class='theme-banner-nav'>$banner_links</div>
    </div>
    $top_menu
</div>
";
}
    
/**
 * Returns the top navigation menu.
 *
 * @param array $menus page menu data
 *
 * @return string top navigation menu HTML
 */

function _get_top_menu($menus)
{
    $top_menu = $menus['top_menu'];
    $active_category_number = $menus['active_category'];

    $html = "
    <!-- Top menu Javascript -->
    <script type='text/javascript'> 
        $(document).ready(function() { 
            $('#theme-top-menu-list').superfish({
                delay: 800,
                pathLevels: 0
            });
        });
    </script>

    <!-- Top Menu -->
    <div id='theme-top-menu-container'>
        <ul id='theme-top-menu-list' class='sf-menu'>
$top_menu
        </ul>        
    </div>
";
    return $html;
}

/**
 * Returns the left navigation menu.
 *
 * @param array $menus page menu data
 *
 * @return string left navigation menu HTML
 */

function _get_left_menu($menus, $is_wizard = FALSE, $nav_buttons = NULL)
{
    $left_menu = $menus['left_menu'];
    $active_category_number = $menus['active_category'];

    // A link for testing wizards
    if (($_SERVER['SERVER_PORT'] == 1501) && $is_wizard)
        $wizard_test = "<p align='center'><a href='/app/base/wizard/stop'>Stop Wizard Test</a></p>";
    else
        $wizard_test = '';

    $html = "
    <!-- Left menu Javascript -->
    <script type='text/javascript'> 
        $(document).ready(function(){
            $('#theme-left-menu').accordion({ heightStyle: 'content', active: $active_category_number, collapsible: false });
        });
    </script>

    <!-- Left Menu -->
    <div id='theme-left-menu-container'>
        <div id='theme-left-menu-container-border'>
            <div id='theme-left-menu-top'></div>
            <div id='theme-left-menu'>
                $left_menu
            </div>
        </div>
        $nav_buttons
        $wizard_test
    </div>
    ";

    return $html;
}

/**
 * Returns wizard navigation.
 *
 * @param array $nav_data navigation data
 *
 * @return string HTML for wizard navigation
 */

function _get_wizard_navigation($nav_data)
{
    $options_previous['id'] = 'wizard_nav_previous';
    $options_previous['class'] = 'shit';
    $options_previous['tabindex'] = '1001';
    $options_next['tabindex'] = '1000';

    if (empty($nav_data['previous']))
        $previous = '';
    else
        $previous = theme_anchor($nav_data['previous'], lang('base_previous'), 'high', 'theme-anchor-previous', $options_previous);

    if (empty($nav_data['next']))
        $next = '';
    else
        $next = anchor_javascript('wizard_nav_next', lang('base_next'), 'high', $options_next);

    $framework =& get_instance();
    $framework->session->set_userdata('wizard_redirect', preg_replace('/^\/app\//', '', $nav_data['next']));

    return "
        <div id='theme_wizard_nav'>
            <p align='center'>
                <span id='theme_wizard_nav_previous'>$previous</span>
                <span id='theme_wizard_nav_next'>$next</span>
            </p>
        </div>
        <div id='theme_wizard_complete' style='display: none; clear: both;'>
            <p align='center'>" . theme_anchor('/app/marketplace/wizard/stop', lang('base_finish_install_wizard'), 'high', 'theme_wizard_stop', NULL) . "</p>
        </div>
    ";
}

///////////////////////////////////////////////////////////////////////////////
// Menu handling
///////////////////////////////////////////////////////////////////////////////

/**
 * Converts menu array into HTML layout
 * 
 * @param array   $menu_  data menu data
 * @param boolean $wizard set to TRUE if wizard menu data
 *
 * @return string menu HTML output
 */

function _get_menu($menu_data, $wizard = FALSE)
{
    // Highlight information for given page
    //-------------------------------------    

    $highlight = array();
    $matches = array();
    preg_match('/\/app\/[^\/]*/', $_SERVER['PHP_SELF'], $matches);
    $basepage = $matches[0];

    // FIXME: temporary hack until full dashboard is implemented. 
    // Load the "reports" menu when the dashboard is selected.
    if ($basepage == '/app/dashboard')
        $basepage = '/app/system_report';

    // The menu data format is a little different for the wizard,
    // so the logic for detecting the "highlight" is different.

    if ($wizard) {
        // Search for an exact match first.  Otherwise, use fuzzy matching.
        // The edit preg is a hack - redo all of this wizard handling -- too messy.
        $full_url = preg_replace('/\/(add|edit).*$/', '', $_SERVER['PHP_SELF']);
        $full_url = preg_replace('/\/$/', '', $full_url);

        foreach ($menu_data as $url => $pageinfo) {
            if ($full_url == $pageinfo['nav'] || preg_match('|' . addslashes($pageinfo['nav']) . '.*|', $full_url)) {
                $highlight['page'] = $pageinfo['nav'];
                $highlight['category'] = $pageinfo['category'];
                $highlight['subcategory'] = $pageinfo['category'] . $pageinfo['subcategory'];
            }
        }

        // No exact URL, do the fuzzy match
        if (empty($highlight)) {
            $basepage_quoted  = preg_quote($basepage, '/');
            foreach ($menu_data as $url => $pageinfo) {
                if (preg_match("/$basepage_quoted/", $pageinfo['nav'])) {
                    $highlight['page'] = $pageinfo['nav'];
                    $highlight['category'] = $pageinfo['category'];
                    $highlight['subcategory'] = $pageinfo['category'] . $pageinfo['subcategory'];
                }
            }
        }

    } else {
        foreach ($menu_data as $url => $pageinfo) {
            if ($url == $basepage) {
                // FIXME: temporary hack until full dashboard is implemented. 
                if (! preg_match('/app\/dashboard/', $_SERVER['PHP_SELF']))
                    $highlight['page'] = $url;

                $highlight['category'] = $pageinfo['category'];
                $highlight['subcategory'] = $pageinfo['category'] . $pageinfo['subcategory'];
            }
        }
    }

    // Loop through to build menu
    //---------------------------

    $top_menu = "";
    $left_menu = "";
    $current_category = "";
    $current_subcategory = "";
    $category_count = 0;
    $active_category_number = 0;

    foreach ($menu_data as $url => $page) {

        if ($page['category'] === lang('base_category_spotlight'))
            continue;

        // Ugly hack - wizard menu data is different
        //------------------------------------------

        if ($wizard)
            $url = $page['nav'];
        
        // Category transition
        //--------------------

        if ($page['category'] != $current_category) {

            // Detect active category for given page
            //--------------------------------------

            if (isset($page['category']) && isset($highlight['category']) && ($page['category'] == $highlight['category'])) {
                $active_category_number = $category_count;
                $class = 'sfCurrent';
            } else {
                $class = '';
            }

            // Don't close top menu category on first run
            //-------------------------------------------

            if (! empty($top_menu)) {
                if ($page['category'] !== lang('base_category_my_account')) {
                    $top_menu .= "\t\t\t</ul>\n";
                    $top_menu .= "\t\t</li>\n";
                }

                $left_menu .= "\t\t\t</ul>\n";
                $left_menu .= "\t\t</div>\n";
            }

            // Top Menu
            //---------

            if ($page['category'] !== lang('base_category_my_account')) {
                $top_menu .= "\t\t<li class='$class'>\n";
                $top_menu .= "\t\t\t<a class='sf-with-url $class' href='#' onclick=\"$('#theme-left-menu').accordion('option', 'active', $category_count);\">" . $page['category'] . "</a>\n";

                $top_menu .= "\t\t\t<ul>\n";
            }

            // Left Menu
            //----------

            $left_menu .= "\t\t<h3 class='theme-left-menu-category'><a href='#'>{$page['category']}</a></h3>\n";
            $left_menu .= "\t\t<div>\n";
            $left_menu .= "\t\t\t<ul class='theme-left-menu-list'>\n";

            // Counters
            //---------

            $current_category = $page['category'];
            $category_count++;
        }
        
        // Subcategory transition
        //-----------------------

        if ($current_subcategory != $page['subcategory']) {
            $current_subcategory = $page['subcategory'];
            $left_menu .= "\t\t\t\t<li class='theme-left-menu-subcategory'>{$page['subcategory']}</li>\n";

            if ($page['category'] !== lang('base_category_my_account'))
                $top_menu .= "\t\t\t\t<li class='theme-top-menu-subcategory'>{$page['subcategory']}</li>\n";
        }

        // Page transition
        //----------------

        $activeClass = (isset($highlight['page']) && ($url == $highlight['page'])) ? 'theme-menu-item-active' : '';

        // Newly installed app
        //--------------------

        $new_app = '';

        if (isset($page['new']) && $page['new'])
            $new_app = "<span class='theme-menu-new-install' title='" . lang('base_new_or_upgraded_app') . "'><!-- new --></span>";

        if ($wizard) {
            // $top_menu .= "\t\t\t\t<li><a class='{$activeClass}' href='{$url}'>$new_app{$page['title']}</a></li>\n";
            $left_menu .= "\t\t\t\t<li class='theme-left-menu-item'><span class='{$activeClass}'>{$page['title']}</span></li>\n";
        } else {
            $left_menu .= "\t\t\t\t<li class='theme-left-menu-item'><a class='{$activeClass}' href='{$url}'>$new_app{$page['title']}</a></li>\n";

            if ($page['category'] !== lang('base_category_my_account'))
                $top_menu .= "\t\t\t\t<li><a class='{$activeClass}' href='{$url}'>$new_app{$page['title']}</a></li>\n";
        }
    }

    // Close out open HTML tags
    //-------------------------

    $top_menu .= "\t\t\t</ul>\n";
    $top_menu .= "\t\t</li>\n";

    $left_menu .= "\t\t\t</ul>\n";
    $left_menu .= "\t\t</div>\n";

    // Return HTML formatted menu
    //---------------------------

    $menus['top_menu'] = $top_menu;
    $menus['left_menu'] = $left_menu;
    $menus['active_category'] = $active_category_number;

    return $menus;
}
