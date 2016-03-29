<?php

/**
 * Network map overview controller.
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
 * Network map overview controller.
 *
 * @category   apps
 * @package    network-map
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

class Network_Map extends ClearOS_Controller
{
    /**
     * Network map overview.
     *
     * @return view
     */

    function index()
    {
        // Show account status widget if we're not in a happy state
        //---------------------------------------------------------

        $this->load->module('accounts/status');

        if ($this->status->unhappy()) {
            $this->status->widget('network_map');
            return;
        }

        // Load libraries
        //---------------

        $this->lang->load('network_map');
        $this->load->factory('mode/Mode_Factory');

        // Load views
        //-----------

        $controllers = array('network_map/subscription', 'network_map/unknown_summary', 'network_map/mapped');

        if (!$this->mode->is_master_available())
            array_unshift($controllers, 'central_management/offline');

        $this->page->view_controllers($controllers, lang('network_map_app_name'));
    }
}
