<?php

/**
 * Daemon controller.
 *
 * @category   apps
 * @package    base
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/base/
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
 * Daemon controller.
 *
 * @category   apps
 * @package    base
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/base/
 */

class Daemon extends ClearOS_Controller
{
    protected $daemon_name = NULL;
    protected $app_name = NULL;

    /**
     * Daemon constructor.
     *
     * @param string $daemon_name daemon name
     * @param string $app_name    app that manages the daemon
     *
     * @return view
     */

    function __construct($daemon_name, $app_name)
    {
        $this->daemon_name = $daemon_name;
        $this->app_name = $app_name;
    }

    /**
     * Default daemon controller.
     *
     * @return view
     */

    function index()
    {
        // Load dependencies
        //------------------

        $this->lang->load('base');

        $data['daemon_name'] = $this->daemon_name;
        $data['app_name'] = $this->app_name;

        // Load views
        //-----------

        $options['javascript'] = array(clearos_app_htdocs('base') . '/daemon.js.php');

        $this->page->view_form('base/daemon', $data, lang('base_server_status'), $options);
    }

    /**
     * Daemon status.
     *
     * @return view
     */

    function status()
    {
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        $this->load->library('base/Daemon', $this->daemon_name);

        $status['status'] = $this->daemon->get_status();

        echo json_encode($status);
    }

    /**
     * Daemon start.
     *
     * @return view
     */

    function start()
    {
        $this->load->library('base/Daemon', $this->daemon_name);

        // Shutdown daemon in case it is dead or in a funk (tracker #1239)
        try {
            $this->daemon->set_running_state(FALSE);
        } catch (Exception $e) {
            //
        }

        try {
            $this->daemon->set_running_state(TRUE);
            $this->daemon->set_boot_state(TRUE);
        } catch (Exception $e) {
            //
        }
    }

    /**
     * Daemon stop.
     *
     * @return view
     */

    function stop()
    {
        $this->load->library('base/Daemon', $this->daemon_name);

        try {
            $this->daemon->set_running_state(FALSE);
            $this->daemon->set_boot_state(FALSE);
        } catch (Exception $e) {
            //
        }
    }
}
