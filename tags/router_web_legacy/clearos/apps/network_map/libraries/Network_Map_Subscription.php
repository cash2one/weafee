<?php

/**
 * Network map subscription engine class.
 *
 * @category   apps
 * @package    network-map
 * @subpackage libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2013 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\network_map;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('network_map');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\base\OS as OS;
use \clearos\apps\network_map\Network_Map as Network_Map;
use \clearos\apps\clearcenter\Subscription_Engine as Subscription_Engine;

clearos_load_library('base/OS');
clearos_load_library('network_map/Network_Map');
clearos_load_library('clearcenter/Subscription_Engine');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Network map license engine class.
 *
 * @category   apps
 * @package    network-map
 * @subpackage libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2013 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

class Network_Map_Subscription extends Subscription_Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Network Map subscription constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Returns subscription information.
     *
     * @return array subscription information
     *
     * @throws Engine_Exception
     */

    public function get_info()
    {
        clearos_profile(__METHOD__, __LINE__);

        $os = new OS();
        $os_name = $os->get_name();

         // FIXME: see license spec
        if (preg_match('/ClearOS Professional/', $os_name))
            $total = 2000;
        else
            $total = 20;

        $network_map = new Network_Map();

        try {
            $entries = $network_map->get_mapped_list();
            $used = count($entries);
            $available = $total - $used;
        } catch (\Exception $e) {
            $used = 0;
            $available = 0;
        }

        $info = array(
            'app_name' => lang('network_map_app_name'),
            'type' => Subscription_Engine::TYPE_DEVICE,
            'total' => $total,
            'used' => $used,
            'available' => $available
        );

        return $info;
    }
}
