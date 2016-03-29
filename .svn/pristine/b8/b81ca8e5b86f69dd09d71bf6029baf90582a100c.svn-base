<?php

/**
 * Dashboard controller.
 *
 * @category   apps
 * @package    dashboard
 * @subpackage controllers
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
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Dashboard controller.
 *
 * @category   apps
 * @package    dashboard
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/dashboard/
 */

class Dashboard extends ClearOS_Controller
{
    /**
     * Dashboard summary view.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->lang->load('dashboard');

        // Load controllers
        //-----------------

        $controllers = array();

        if (clearos_app_installed('resource_report')) {
            $controllers[] = array(
                'controller' => 'resource_report/memory',
                'method' => 'dashboard',
            );

            $controllers[] = array(
                'controller' => 'resource_report/system_load',
                'method' => 'dashboard',
            );
        } else {
            $controllers[] = array(
                'controller' => 'dashboard/mem',
                'method' => 'index',
            );
        }

        if (clearos_app_installed('system_report')) {

            $controllers[] = array(
                'controller' => 'system_report/details',
                'method' => 'index',
            );
        }

        if (clearos_app_installed('software_updates')) {
            $controllers[] = array(
                'controller' => 'software_updates/activity',
                'method' => 'index',
            );
        }

        // $options['type'] = MY_Page::TYPE_DASHBOARD;

        $this->page->view_controllers($controllers, lang('dashboard_app_name'), $options);
    }
}
