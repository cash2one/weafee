<?php

/**
 * Firewall Custom controller.
 *
 * @category   apps
 * @package    firewall-custom
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/firewall_custom/
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

use \clearos\apps\firewall_custom\Firewall_Custom as Firewall_Custom_Class;

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Firewall Custom controller.
 *
 * @category   apps
 * @package    firewall-custom
 * @subpackage controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/firewall_custom/
 */

class Firewall_Custom extends ClearOS_Controller
{

    /**
     * Firewall Custom default controller
     *
     * @return view
     */

    function index()
    {
        // Load dependencies
        //------------------

        $this->lang->load('firewall_custom');
        $this->load->library('firewall/Firewall');
        $this->load->library('firewall_custom/Firewall_Custom');
        $this->load->library('network/Network');

        // Load view data
        //---------------

        try {
            $data['rules'] = $this->firewall_custom->get_rules();
            $data['network_mode'] = $this->network->get_mode();
            $data['panic'] = $this->firewall->is_panic();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load view
        //----------

        $options['type'] = MY_Page::TYPE_WIDE_CONFIGURATION;

        $this->page->view_form('summary', $data, lang('firewall_custom_app_name'), $options);
    }

    /**
     * Add rule.
     *
     * @param integer $line line number
     *
     * @return view
     */

    function add_edit($line = -1)
    {
        // Load libraries
        //---------------

        $this->load->library('firewall_custom/Firewall_Custom');
        $this->lang->load('firewall_custom');
        $this->lang->load('base');

        // Set validation rules
        //---------------------

        $this->form_validation->set_policy('entry', 'firewall_custom/Firewall_Custom', 'validate_entry', TRUE);
        $this->form_validation->set_policy('description', 'firewall_custom/Firewall_Custom', 'validate_description', TRUE);

        // Handle form submit
        //-------------------

        if ($this->form_validation->run()) {
            try {
                if ($line >= 0) {
                    $this->firewall_custom->update_rule(
                        $line,
                        $this->input->post('entry'),
                        $this->input->post('description'),
                        $this->input->post('enabled')
                    );
                    $this->page->set_status_updated();
                } else {
                    $this->firewall_custom->add_rule(
                        $this->input->post('entry'),
                        $this->input->post('description'),
                        $this->input->post('enabled'),
                        $this->input->post('priority')
                    );
                    $this->page->set_status_added();
                }

                redirect('/firewall_custom');
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        if ($line >= 0)
            $data = $this->firewall_custom->get_rule($line);

        // Load the views
        //---------------

        $this->page->view_form('firewall_custom/add_edit', $data, lang('base_add'));
    }

    /**
     * Toggle enable/disable of rule.
     *
     * @param integer $line line number
     *
     * @return view
     */

    function toggle($line)
    {
        // Load libraries
        //---------------

        $this->load->library('firewall_custom/Firewall_Custom');
        $this->lang->load('firewall_custom');
        $this->lang->load('base');

        // Handle form submit
        //-------------------

        try {
            $data = $this->firewall_custom->get_rule($line);
            $this->firewall_custom->update_rule(
                $line,
                $data['entry'],
                $data['description'],
                !$data['enabled']
            );
            $this->page->set_status_updated();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        redirect('/firewall_custom');
    }

    /**
     * Delete custom rule.
     *
     * @param integer $line    line number
     * @param string  $confirm confirm deletion
     *
     * @return view
     */

    function delete($line, $confirm = NULL)
    {
        $confirm_uri = '/app/firewall_custom/delete/' . $line . '/1';
        $cancel_uri = '/app/firewall_custom';

        $this->load->library('firewall_custom/Firewall_Custom');
        $this->lang->load('firewall_custom');

        if ($confirm != NULL) {
            try {
                $this->firewall_custom->delete_rule($line);

                $this->page->set_status_deleted();
                redirect('/firewall_custom');
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        $rule = $this->firewall_custom->get_rule($line);

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, array($rule['description']));
    }

    /**
     * Prioritize rule.
     *
     * @param integer $line     line number
     * @param integer $priority priority
     *
     * @return view
     */

    function priority($line, $priority)
    {
        // Load libraries
        //---------------

        $this->load->library('firewall_custom/Firewall_Custom');

        // Load view data
        //---------------

        try {
            $this->firewall_custom->set_rule_priority($line, $priority);
            $this->page->set_status_updated();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load view
        //----------

        redirect('/firewall_custom');
    }
}
