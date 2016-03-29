<?php

/**
 * User profile controller.
 *
 * @category   apps
 * @package    user-profile
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/user_profile/
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
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\accounts\Accounts_Engine as Accounts_Engine;
use \Exception as Exception;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * User profile controller.
 *
 * @category   apps
 * @package    user-profile
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/user_profile/
 */

class User_Profile extends ClearOS_Controller
{
    /**
     * User profile default controller.
     *
     * @return view
     */

    function index()
    {
        if ($this->session->userdata('username') === 'root')
            $this->_index_root();
        else
            $this->_index_normal();
    }

    /**
     * Normal user controller.
     */

    function _index_normal()
    {
        // Load libraries
        //---------------

        $username = $this->session->userdata('username');

        $this->lang->load('user_profile');
        $this->load->factory('users/User_Factory', $username);
        $this->load->factory('accounts/Accounts_Factory');

        // Validation
        //-----------

        $this->form_validation->set_policy('old_password', 'users/User_Engine', 'validate_password', TRUE);
        $this->form_validation->set_policy('password', 'users/User_Engine', 'validate_password', TRUE);
        $this->form_validation->set_policy('verify', 'users/User_Engine', 'validate_password', TRUE);

        $form_ok = $this->form_validation->run();

        // Extra Validation
        //------------------

        $old_password = ($this->input->post('old_password')) ? $this->input->post('old_password') : '';
        $password = ($this->input->post('password')) ? $this->input->post('password') : '';
        $verify = ($this->input->post('verify')) ? $this->input->post('verify') : '';

        if ($password != $verify) {
            $this->form_validation->set_error('verify', lang('base_password_and_verify_do_not_match'));
            $form_ok = FALSE;
        } else if ($old_password && !$this->user->check_password($old_password)) {
            $this->form_validation->set_error('old_password', lang('base_password_is_invalid'));
            $form_ok = FALSE;
        }

        // Handle form submit
        //-------------------

        $password_updated = FALSE;

        if ($this->input->post('submit') && ($form_ok)) {
            try {
                $retval = $this->user->set_password(
                    $this->input->post('old_password'),
                    $this->input->post('password'),
                    $this->input->post('verify'),
                    $username
                );

                // Password change requests cannot be 100% pre-validated 
                // (for example, a password that already exists in the password
                // history).  Handle page status a bit differently.

                if (empty($retval))
                    $password_updated = TRUE;
                else
                   $this->form_validation->set_error('verify', $retval);

            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load the view data 
        //------------------- 

        try {
            $data['info_map'] = $this->user->get_info_map();
            $data['user_info'] = $this->user->get_info();
            $data['mode'] = ($this->accounts->get_capability() === Accounts_Engine::CAPABILITY_READ_WRITE) ? 'edit' : 'view';
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load the views
        //---------------

        if ($password_updated)
            $this->page->view_form('updated', $data, lang('user_profile_app_name'));
        else
            $this->page->view_form('user_profile', $data, lang('user_profile_app_name'));
    }

    /**
     * Root user controller.
     *
     * @return view
     */

    function _index_root()
    {
        // Load libraries
        //---------------

        $this->lang->load('user_profile');
        $this->load->library('base/Posix_User', 'root');

        // Validation
        //-----------

        $this->form_validation->set_policy('old_password', 'users/User_Engine', 'validate_password', TRUE);
        $this->form_validation->set_policy('password', 'users/User_Engine', 'validate_password', TRUE);
        $this->form_validation->set_policy('verify', 'users/User_Engine', 'validate_password', TRUE);

        $form_ok = $this->form_validation->run();

        // Extra Validation
        //------------------

        $old_password = ($this->input->post('old_password')) ? $this->input->post('old_password') : '';
        $password = ($this->input->post('password')) ? $this->input->post('password') : '';
        $verify = ($this->input->post('verify')) ? $this->input->post('verify') : '';

        if ($password != $verify) {
            $this->form_validation->set_error('verify', lang('base_password_and_verify_do_not_match'));
            $form_ok = FALSE;
        } else if ($old_password && !$this->posix_user->check_password($old_password)) {
            $this->form_validation->set_error('old_password', lang('base_password_is_invalid'));
            $form_ok = FALSE;
        } else if (!empty($password)) {
            try {
                $is_weak = $this->posix_user->is_weak_password($this->input->post('password'));
            } catch (Engine_Exception $e) {
                $this->page->view_exception($e);
                return;
            }

            if ($is_weak) {
                $this->form_validation->set_error('verify', lang('base_password_too_weak'));
                $form_ok = FALSE;
            }
        }

        // Handle form submit
        //-------------------

        $password_updated = FALSE;

        if ($this->input->post('submit') && ($form_ok)) {
            try {
                $this->posix_user->set_password($this->input->post('password'));

                // Handle page status a bit differently here
                $password_updated = TRUE;
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load the view data 
        //------------------- 

        try {
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load the views
        //---------------

        if ($password_updated)
            $this->page->view_form('updated', $data, lang('user_profile_app_name'));
        else
            $this->page->view_form('root_profile', $data, lang('user_profile_app_name'));
    }
}
