<?php

/**
 * Directory server settings controller.
 *
 * @category   apps
 * @package    openldap-directory
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/openldap_directory/
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

use \clearos\apps\accounts\Accounts_Engine as Accounts_Engine;
use \Exception as Exception;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Directory_server settings controller.
 *
 * We are actually initializing two layers here:
 * - The base LDAP layer using the (basically, an empty LDAP directory)
 * - The base accounts layer (all things related to user accounts)
 *
 * @category   apps
 * @package    openldap-directory
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/openldap_directory/
 */

class Settings extends ClearOS_Controller
{
    /**
     * Directory server settings default  controller
     *
     * @return view
     */

    function index()
    {
        $this->_item('view');
    }

    /**
     * Edit view
     *
     * @return view
     */

    function edit()
    {
        $this->_item('edit');
    }

    /**
     * View view
     *
     * @param string $action action
     *
     * @return view
     */

    function view($action)
    {
        $this->_item('view');
    }

    /**
     * Updates domain.
     *
     * @param string $action action
     *
     * @return view
     */

    function action($action)
    {
        // Load libraries
        //---------------

        $this->load->library('openldap_directory/OpenLDAP');
        $this->load->library('openldap/LDAP_Driver');

        // Handle form submit
        //-------------------

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Fri, 01 Jan 2010 05:00:00 GMT');
        header('Content-type: application/json');

        try {
            if ($action === 'initialize') {
                $this->openldap->initialize($this->input->post('domain'));
            } else {
                $this->openldap->set_base_internet_domain($this->input->post('domain'));
                $this->ldap_driver->reset();
            }

            echo json_encode(array('code' => 0));
        } catch (Exception $e) {
            echo json_encode(array('code' => clearos_exception_code($e), 'error_message' => clearos_exception_message($e)));
        }
    }

    /**
     * Common form handler
     *
     * @param string $form_type form type
     *
     * @return view
     */

    function _item($form_type)
    {
        // Load dependencies
        //------------------

        $this->lang->load('openldap_directory');
        $this->load->library('openldap/LDAP_Driver');
        $this->load->library('openldap_directory/Accounts_Driver');

        // Set validation rules
        //---------------------

        $this->form_validation->set_policy('domain', 'openldap/LDAP_Driver', 'validate_domain', TRUE);
        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if ((($this->input->post('update') || $this->input->post('initialize')) && $form_ok)) {
            try {
                $this->ldap_driver->prepare_initialize();
                $data['validated_action'] = ($this->input->post('update')) ? 'update' : 'initialize';
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load view data
        //---------------

        try {
            $data['domain'] = $this->ldap_driver->get_base_internet_domain();
            $data['mode'] = $this->ldap_driver->get_mode();
            $data['mode_text'] = $this->ldap_driver->get_mode_text();
            $data['initialized'] = $this->accounts_driver->is_initialized();
            $data['driver'] = $this->accounts_driver->get_driver_status();

            // Go straight to edit mode when unitialized
            if ($data['initialized'])
                $data['form_type'] = $form_type;
            else
                $data['form_type'] = 'init';

        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('openldap_directory/settings', $data, lang('openldap_directory_app_name'));
    }
}
