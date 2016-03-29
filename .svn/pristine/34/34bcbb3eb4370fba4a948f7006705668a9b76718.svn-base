<?php

/**
 * Unknown devices controller.
 *
 * @category   apps
 * @package    network-map
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012-2013 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Unknown devices controller.
 *
 * @category   apps
 * @package    network-map
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012-2013 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

class Unknown extends ClearOS_Controller
{
    /**
     * Unknown devices.
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
            $data['report_type'] = $report_type;
            $data['mappings'] = $this->network_map->get_unknown_list();
            $data['subscription'] = $this->network_map_subscription->get_info();
            $data['form_type'] = ($this->mode->is_master_available()) ? 'edit' : 'view';
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
 
        // Load views
        //-----------

        $options['type'] = MY_Page::TYPE_WIDE_CONFIGURATION;

        $this->page->view_form('network_map/unknown', $data, lang('network_map_unknown_devices'), $options);
    }
}
