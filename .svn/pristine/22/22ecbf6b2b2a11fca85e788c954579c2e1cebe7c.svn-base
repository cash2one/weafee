<?php

/**
 * OS edition controller.
 *
 * @category   apps
 * @package    edition
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/edition/
 */

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * OS edition controller.
 *
 * @category   apps
 * @package    edition
 * @subpackage controllers
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/edition/
 */

class Edition extends ClearOS_Controller
{
    /**
     * Edition default controller.
     *
     * @return string
     */

    function index()
    {
        $this->lang->load('edition');
        $this->load->library('base/OS');
        $this->load->library('edition/Edition');

        // Load view data
        //---------------

        try {
            $os_name = $this->os->get_name();

            // Don't show upgrade on a Professional Edition
            if (preg_match('/ClearOS Professional/', $os_name))
                redirect($this->session->userdata['wizard_redirect']);
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Handle form submit
        //-------------------

        if ($this->input->post('edition')) {
            if ($this->input->post('edition') === 'professional')
                $this->edition->set('professional');
            else
                $this->edition->set('community');

            redirect($this->session->userdata['wizard_redirect']);
        }

        // Load views
        //-----------

        $this->page->view_form('edition/edition', $data, lang('edition_select_edition'));
    }
}
