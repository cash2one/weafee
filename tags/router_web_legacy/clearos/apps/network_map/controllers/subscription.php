<?php

/**
 * Network map subscription controller.
 *
 * @category   apps
 * @package    network-map
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2013 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Network map subscription controller.
 *
 * @category   apps
 * @package    network-map
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2013 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

class Subscription extends ClearOS_Controller
{
    /**
     * Network map subscription default controller.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->lang->load('network_map');
        $this->load->library('network_map/Network_Map_Subscription');

        // Load view data
        //---------------

        try {
            $data['subscription'] = $this->network_map_subscription->get_info();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
 
        // Load views
        //-----------

        if ($data['subscription']['available'] < 0)
            $this->page->view_form('network_map/subscription', $data, lang('base_subscription'));
    }
}
