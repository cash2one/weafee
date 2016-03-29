<?php

/**
 * Mapped devices controller.
 *
 * @category   apps
 * @package    network-map
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Mapped devices controller.
 *
 * @category   apps
 * @package    network-map
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

class Mapped extends ClearOS_Controller
{
    /**
     * Mapped devices.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->lang->load('network_map');
        $this->load->library('network_map/Network_Map');
        $this->load->library('network_map/Network_Map_Subscription');
        $this->load->factory('mode/Mode_Factory');

        // Load view data
        //---------------

        try {
            $data['form_type'] = ($this->mode->is_master_available()) ? 'edit' : 'view';
            $data['mappings'] = $this->network_map->get_mapped_list();
            $data['subscription'] = $this->network_map_subscription->get_info();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
 
        // Load views
        //-----------

        $this->page->view_form('network_map/mapped', $data, lang('network_map_mapped_devices'));
    }

    /**
     * Add mapping view.
     *
     * @param string $mac MAC address
     *
     * @return view
     */

    function add($mac)
    {
        $this->_item('add', $mac);
    }

    /**
     * Add mapping view.
     *
     * @param string $mac MAC address
     *
     * @return view
     */

    function add_unknown($mac)
    {
        $this->_item('add_unknown', $mac);
    }

    /**
     * Edit mapping view.
     *
     * @param string $mac MAC address
     *
     * @return view
     */

    function edit($mac)
    {
        $this->_item('edit', $mac);
    }

    /**
     * Mapping delete view.
     *
     * @param string $mac MAC address
     *
     * @return view
     */

    function delete($mac)
    { 
        // Load libraries
        //---------------

        $this->load->library('network_map/Network_Map');

        // Load view data
        //---------------

        try {
            $mapping = $this->network_map->get_entry($mac);
            $nickname = (empty($mapping['nickname'])) ? '' : ' - ' . $mapping['nickname'];
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $confirm_uri = '/app/network_map/mapped/destroy/' . $mac;
        $cancel_uri = '/app/network_map/mapped';
        $items = array($mac . $nickname);

        $this->page->view_confirm_delete($confirm_uri, $cancel_uri, $items);
    }

    /**
     * Mapping destroy.
     *
     * @param string $mac MAC address
     *
     * @return view
     */

    function destroy($mac)
    {
        try {
            $this->load->library('network_map/Network_Map');
    
            $this->network_map->delete_entry($mac);

            $this->page->set_status_deleted();
            redirect('/network_map/mapped');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * View mapping view.
     *
     * @param string $mac MAC address
     *
     * @return view
     */

    function view($mac)
    {
        $this->_item('view', $mac);
    }

    /**
     * Common add/edit form.
     *
     * @param string $form_type form type
     * @param string $mac       MAC address
     *
     * @return view
     */

    function _item($form_type, $mac)
    {
        // Load libraries
        //---------------

        $this->lang->load('network_map');
        $this->load->library('network_map/Network_Map');
        $this->load->library('network_map/Network_Map_Subscription');
        $this->load->factory('users/User_Manager_Factory');

        // Set validation rules
        //---------------------

        // Allow dashes in the MAC, but convert to colon - tracker #969
        $mac = preg_replace('/-/', ':', $mac);

        $check_exists = (($form_type === 'add_unknown') || ($form_type === 'add')) ? TRUE : FALSE;

        $this->form_validation->set_policy('mac', 'network_map/Network_Map', 'validate_mac', TRUE, $check_exists);
        $this->form_validation->set_policy('username', 'network_map/Network_Map', 'validate_username', TRUE);
        $this->form_validation->set_policy('vendor', 'network_map/Network_Map', 'validate_vendor');
        $this->form_validation->set_policy('nickname', 'network_map/Network_Map', 'validate_nickname');
        $this->form_validation->set_policy('type', 'network_map/Network_Map', 'validate_device_type');

        $raw_mapping = $this->input->post('mapping');
        $mapping = array();

        foreach ($raw_mapping as $ip_key => $details) {
            $ip = strtr($ip_key, '-_', '/.'); // Remunge the keys
            $mapping[$ip] = $details;

            $this->form_validation->set_policy("mapping[$ip_key]", 'network_map/Network_Map', 'validate_ip');
        }

        $form_ok = $this->form_validation->run();

        // Handle form submit
        //-------------------

        if ($this->input->post('submit') && ($form_ok === TRUE)) {

            try {
                if ($form_type === 'edit') {
                    $this->network_map->update_entry(
                        $this->input->post('mac'),
                        $this->input->post('username'),
                        $this->input->post('type'),
                        $this->input->post('nickname'),
                        $this->input->post('vendor')
                    );

                    $this->page->set_status_updated();
                } else {
                    $this->network_map->add_entry(
                        $this->input->post('mac'),
                        $this->input->post('username'),
                        $this->input->post('type'),
                        $this->input->post('nickname'),
                        $this->input->post('vendor')
                    );

                    $this->page->set_status_added();
                }

                $this->network_map->update_mappings(
                    $this->input->post('mac'),
                    $mapping
                );

                // We're breaking the UI standard a bit here.  Since users
                // will tend to want to map several systems in one session,
                // we send them back to the "unknown" summary and push out a
                // message that a system was successfully mapped.
                // Alos, go back to summary page when limit is reached
            
                $subscription = $this->network_map_subscription->get_info();

                if ($subscription['available'] == 0) {
                    redirect('/network_map');
                } else if ($form_type === 'add_unknown') {
                    $this->page->set_message(lang('network_map_device_mapping_succeeded'), 'highlight');
                    redirect('/network_map/unknown');
                } else {
                    redirect('/network_map/mapped');
                }
            } catch (Exception $e) {
                $this->page->view_exception($e);
                return;
            }
        }

        // Load view data
        //---------------

        try {
            $mapping = $this->network_map->get_entry($mac);
            $device_types = $this->network_map->get_device_types();
            $users = $this->user_manager->get_core_details();

            $data['usernames'] = array();
            // $data['usernames']['_notspecified'] = '-- not specified  --';

            $data['subscription'] = $this->network_map_subscription->get_info();

            foreach ($users as $username => $details)
                $data['usernames'][$username] = $username . ' -- ' . $details['core']['first_name'] . ' ' . $details['core']['last_name'];

            $data['form_type'] = $form_type;
            $data['mac'] = $mac;
            $data['vendor'] = $mapping['vendor'];
            $data['type'] = $mapping['type'];
            $data['nickname'] = $mapping['nickname'];
            $data['username'] = $mapping['username'];
            $data['mapping'] = $mapping['mapping'];
            $data['dhcp_convert_lease'] = isset($mapping['dhcp_convert_lease']) ? $mapping['dhcp_convert_lease'] : '';
            $data['types'] = $device_types;
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('network_map/item', $data, lang('base_add'));
    }
}
