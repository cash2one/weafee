<?php

/**
 * Unknown summary controller.
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
 * Unknown summary controller.
 *
 * @category   apps
 * @package    network-map
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

class Unknown_Summary extends ClearOS_Controller
{
    /**
     * Unknown summary view.
     *
     * @return view
     */

    function index()
    {
        // Load libraries
        //---------------

        $this->lang->load('network_map');

        // Load views
        //-----------

        $this->page->view_form('network_map/summary', array(), lang('network_map_unknown_devices'));
    }

    /**
     * Returns number of unknown devices.
     *
     * @return integer number of unknown devices
     */

    function get_count()
    {
        // Load dependencies
        //------------------

        $this->load->library('network_map/Network_Map');

        // Run synchronize
        //----------------

        try {
            sleep(2);
            $data['error_code'] = 0;
            $data['count'] = count($this->network_map->get_unknown_list());
        } catch (Exception $e) {
            $data['error_code'] = clearos_exception_code($e);
            $data['error_message'] = clearos_exception_message($e);
        }

        // Return status message
        //----------------------

        $this->output->set_header("Content-Type: application/json");
        $this->output->set_output(json_encode($data));
    }
}
