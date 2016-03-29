<?php

/**
 * User certificates controller.
 *
 * @category   apps
 * @package    user-certificates
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/user_certificates/
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

use \clearos\apps\mode\Mode_Engine as Mode_Engine;
use \Exception as Exception;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * User certificates controller.
 *
 * @category   apps
 * @package    user-certificates
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/user_certificates/
 */

class Certificates extends ClearOS_Controller
{
    /**
     * User certificates default controller.
     *
     * @return view
     */

    function index()
    {
        // Show account status widget if we're not in a happy state
        //---------------------------------------------------------

        $this->load->module('accounts/status');

        if ($this->status->unhappy()) {
            $this->status->widget('user_certificates');
            return;
        }

        // Bail if root
        //-------------

        $username = $this->session->userdata('username');

        if ($username === 'root') {
            $this->page->view_form('root_warning', $data, lang('user_certificates_app_name'));
            return;
        }

        // Load libraries
        //---------------

        $this->lang->load('certificate_manager');
        $this->lang->load('user_certificates');
        $this->load->library('certificate_manager/SSL');
        $this->load->factory('mode/Mode_Factory');
        $this->load->factory('users/User_Factory', $username);

        // Validation
        //-----------

        $this->form_validation->set_policy('password', 'users/User_Engine', 'validate_password', TRUE);
        $this->form_validation->set_policy('verify', 'users/User_Engine', 'validate_password', TRUE);

        $form_ok = $this->form_validation->run();

        // Extra Validation
        //------------------

        $password = ($this->input->post('password')) ? $this->input->post('password') : '';
        $verify = ($this->input->post('verify')) ? $this->input->post('verify') : '';

        if ($password != $verify) {
            $this->form_validation->set_error('verify', lang('base_password_and_verify_do_not_match'));
            $form_ok = FALSE;
        }

        // Handle form submit
        //-------------------

        if ($this->input->post('submit') && ($form_ok)) {
            try {
                $this->ssl->create_default_client_certificate(
                    $username,
                    $this->input->post('password'),
                    $this->input->post('verify')
                );
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load the view data 
        //------------------- 

        try {
            $data['username'] = $username;

            $data['certs']['ca-cert.pem'] = lang('certificate_manager_certificate_authority');
            $data['certs']['client-' . $username . '-cert.pem'] = lang('certificate_manager_certificate');
            $data['certs']['client-' . $username . '-key.pem'] = lang('certificate_manager_private_key');
            $data['certs']['client-' . $username . '.p12'] = lang('certificate_manager_pkcs12');

            $cert_exists = $this->ssl->exists_default_client_certificate($username);
            $ca_exists = $this->ssl->exists_certificate_authority();
            $viewable = ($this->mode->get_mode() === Mode_Engine::MODE_SLAVE) ? FALSE : TRUE;

            $user_info = $this->user->get_info();
            $is_cert_user = ($user_info['plugins']['user_certificates']) ? TRUE : FALSE;
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load the views
        //---------------

        if (!$viewable)
            $this->page->view_form('unavailable', $data, lang('user_certificates_app_name'));
        else if (!$is_cert_user)
            $this->page->view_form('disabled', $data, lang('user_certificates_app_name'));
        else if (!$ca_exists)
            $this->page->view_form('uninitialized', $data, lang('user_certificates_app_name'));
        else if ((!$cert_exists && $username != 'root'))
            $this->page->view_form('initialize', $data, lang('user_certificates_app_name'));
        else
            $this->page->view_form('certificates', $data, lang('user_certificates_certificates'));
    }

    /**
     * Destroys certificates.
     *
     * @return view
     */

    function destroy()
    {
        $this->load->library('certificate_manager/SSL');

        $username = $this->session->userdata('username');

        $this->ssl->delete_default_client_certificate($username);

        redirect('/user_certificates');
    }

    /**
     * Downloads certificate to requesting client.
     *
     * @param string $certificate requested certificate
     *
     * @return string certificate
     */

    function download($certificate)
    {
        $this->_install_download('download', $certificate);
    }

    /**
     * Installs certificate on requesting client.
     *
     * @param string $certificate requested certificate
     *
     * @return string certificate
     */

    function install($certificate)
    {
        $this->_install_download('install', $certificate);
    }

    /**
     * Resets all certificates.
     *
     * @return view
     */

    function reset()
    {
        $this->lang->load('certificate_manager');

        $confirm_uri = '/app/user_certificates/certificates/destroy';
        $cancel_uri = '/app/user_certificates/certificates';
        $items = array(lang('certificate_manager_security_certificates'));

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Common install/download method.
     *
     * @param string $type        install or download
     * @param string $certificate requested certificate
     *
     * @return string certificate
     */

    function _install_download($type, $certificate)
    {
        // Load dependencies
        //------------------

        $this->lang->load('certificate_manager');
        $this->load->library('certificate_manager/SSL');

        $username = $this->session->userdata('username');

        // Load view data
        //---------------

        if ($certificate === 'ca-cert.pem')
            $filename = 'ca-cert.pem';
        else if ($certificate === 'client-' . $username . '-cert.pem')
            $filename = 'client-' . $username . '-cert.pem';
        else if ($certificate === 'client-' . $username . '-key.pem')
            $filename = 'private/client-' . $username . '-key.pem';
        else if ($certificate === 'client-' . $username . '.p12')
            $filename = 'client-' . $username . '.p12';
        else
            redirect('/user_certificates');

        try {
            $attributes = $this->ssl->get_certificate_attributes($filename);
        } catch (Engine_Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load view
        //----------

        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $attributes['filesize']);

        if ($type === 'download') {
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . preg_replace('/.*\//', '', $filename . ';'));
        } else {
            if (! empty($attributes['pkcs12']))
                header('Content-Type: application/x-pkcs12-signature');
            else if (! empty($attributes['ca']))
                header('Content-Type: application/x-x509-ca-cert');
            else
                header('Content-Type: application/x-x509-user-cert');
        }

        echo $attributes['file_contents'];
    }
}
