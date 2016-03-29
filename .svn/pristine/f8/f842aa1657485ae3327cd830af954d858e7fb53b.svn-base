<?php

/**
 * Arpwatch class.
 *
 * @category   apps
 * @package    network-map
 * @subpackage libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012-2013 ClearCenter
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

use \clearos\apps\base\Daemon as Daemon;
use \clearos\apps\base\File as File;
use \clearos\apps\base\Folder as Folder;
use \clearos\apps\network\Iface as Iface;

clearos_load_library('base/Daemon');
clearos_load_library('base/File');
clearos_load_library('base/Folder');
clearos_load_library('network/Iface');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Arpwatch class.
 *
 * @category   apps
 * @package    network-map
 * @subpackage libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012-2013 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

class Arpwatch extends Daemon
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const PATH_DATA = '/var/lib/arpwatch';

    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    protected $data = array();

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Arpwatch constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        parent::__construct('arpwatch');
    }

    /**
     * Returns arpwatch data.
     *
     * @param integer $max_days maximum number of days since last seen
     *
     * @return array array containing ARP data
     * @throws Engine_Exception
     */

    public function get_data($max_days = 30)
    {
        clearos_profile(__METHOD__, __LINE__);

        // Pull in MAC address database
        include clearos_app_base('network_map') . '/deploy/mac_database.php';

        $data = array();

        $folder = new Folder(self::PATH_DATA);
        $arp_files = $folder->get_listing();

        $min_date = time() - ($max_days * 24 * 60 * 60);

        foreach ($arp_files as $arp_file) {
            if (! preg_match('/^arp.*dat$/', $arp_file))
                continue;

            $file = new File(self::PATH_DATA . '/' . $arp_file);
            $lines = $file->get_contents_as_array();

            $iface_name = preg_replace('/^arp_/', '', $arp_file);
            $iface_name = preg_replace('/\.dat$/', '', $iface_name);

            $iface = new Iface($iface_name);
            $network = $iface->get_network();

            foreach ($lines as $line) {
                $matches = array();

                if (preg_match('/^([0-9a-zA-Z:]+)\s+([^\s]+)\s+([^\s]+)/', $line, $matches)) {

                    if ($matches[3] < $min_date)
                        continue;

                    $mac_matches = array();

                    if (preg_match('/^(.*):(.*):(.*):(.*):(.*):(.*)/', strtoupper($matches[1]), $mac_matches)) {
                        $mac = sprintf(
                            '%02s:%02s:%02s:%02s:%02s:%02s', 
                            $mac_matches[1], 
                            $mac_matches[2],
                            $mac_matches[3],
                            $mac_matches[4],
                            $mac_matches[5],
                            $mac_matches[6]
                        );
                        $mac_prefix = strtoupper(substr($mac, 0, 8));
                    } else {
                        $mac = $matches[1];
                        $mac_prefix = '';
                    }

                    $ip = $matches[2];

                    $data[$mac]['vendor'] = isset($mac_database[$mac_prefix]) ? $mac_database[$mac_prefix] : '';
                    $data[$mac]['mapping'][$ip] = array();
                    $data[$mac]['mapping'][$ip]['last_seen'] = $matches[3];
                    $data[$mac]['mapping'][$ip]['network'] = $network;
                    $data[$mac]['mapping'][$ip]['interface'] = $iface_name;
                }
            }
        }

        ksort($data);

        return $data;
    }
}
