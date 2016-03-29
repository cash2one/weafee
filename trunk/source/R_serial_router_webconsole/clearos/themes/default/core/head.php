<?php

/**
 * Head tags handler for the ClearOS theme.
 *
 * @category  Theme
 * @package   ClearOS
 * @author    ClearFoundation <developer@clearfoundation.com>
 * @copyright 2011-2012 ClearFoundation
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link      http://www.clearfoundation.com/docs/developer/theming/
 */

//////////////////////////////////////////////////////////////////////////////
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

/**
 * Returns additional <head> data required for the theme.
 *
 * @param string $theme_path them path
 *
 * @return string HTML output
 */

function theme_page_head($theme_path)
{
	$basepath = preg_replace('/\/core\/.*/', '', realpath(__FILE__));

    $version = '6.5.0';

    // TODO fix theme paths in get_theme_url in libraries/Config.php
    $theme_path = preg_replace(array('|^//|', '|/$|'), array('/', ''), $theme_path);

    $theme_extras = '';

	if (file_exists("$basepath/css/theme-extras.css"))
		$theme_extras .= "<link type='text/css' href='$theme_path/css/theme-extras.css?v=$version' rel='stylesheet'>\n";

	if (file_exists("$basepath/css/theme-organization.css"))
		$theme_extras .= "<link type='text/css' href='$theme_path/css/theme-organization.css?v=$version' rel='stylesheet'>\n";

	if (file_exists("$basepath/images/favicon-orange.ico"))
		$color = "orange";
	else
		$color = "green";

    return "
<!-- Theme Favicon -->
<link href='$theme_path/images/favicon-$color.ico' rel='shortcut icon' >

<!-- Theme Style Sheets -->
<link type='text/css' href='$theme_path/css/jquery-ui-1.10.3.custom.css?v=$version' rel='stylesheet'>
<link type='text/css' href='$theme_path/css/jquery.jqplot.min.css?v=$version' rel='stylesheet'>
<link type='text/css' href='$theme_path/css/superfish.css?v=$version' rel='stylesheet'>
<link type='text/css' href='$theme_path/css/jquery.lightbox-0.5.css?v=$version' rel='stylesheet'>
<link type='text/css' href='$theme_path/css/summary-table.css?v=$version' rel='stylesheet'>
<link type='text/css' href='$theme_path/css/font-awesome.min.css?v=$version' rel='stylesheet'>
<link type='text/css' href='$theme_path/css/theme.css?v=$version' rel='stylesheet'>
$theme_extras
<!-- Theme Javascript -->
<script type='text/javascript' src='$theme_path/js/jquery-ui-1.10.3.custom.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/jquery.jqplot.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.json2.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.barRenderer.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.pieRenderer.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.dateAxisRenderer.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.categoryAxisRenderer.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.canvasTextRenderer.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.pointLabels.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jqplot/plugins/jqplot.highlighter.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jquery.dataTables.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jquery.lightbox-0.5.min.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/superfish.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/jquery-tools-tooltip-1.2.7.js?v=$version'></script>
<script type='text/javascript' src='$theme_path/js/theme.js?v=$version'></script>

";
}
