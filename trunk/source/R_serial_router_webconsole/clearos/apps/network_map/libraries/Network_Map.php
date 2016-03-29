<?php

/**
 * Network map class.
 *
 * @category   apps
 * @package    network-map
 * @subpackage libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
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

// Classes
//--------

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\File as File;
use \clearos\apps\base\Shell as Shell;
use \clearos\apps\ldap\LDAP_Factory as LDAP_Factory;
use \clearos\apps\network\Hosts as Hosts;
use \clearos\apps\network\Iface_Manager as Iface_Manager;
use \clearos\apps\network\Network_Utils as Network_Utils;
use \clearos\apps\network_map\Arpwatch as Arpwatch;

clearos_load_library('base/Engine');
clearos_load_library('base/File');
clearos_load_library('base/Shell');
clearos_load_library('ldap/LDAP_Factory');
clearos_load_library('network/Hosts');
clearos_load_library('network/Iface_Manager');
clearos_load_library('network/Network_Utils');
clearos_load_library('network_map/Arpwatch');

// Exceptions
//-----------

use \clearos\apps\base\Validation_Exception as Validation_Exception;
use \clearos\apps\network_map\Network_Map_Already_Exists_Exception as Network_Map_Already_Exists_Exception;
use \clearos\apps\network_map\Network_Map_Not_Found_Exception as Network_Map_Not_Found_Exception;

clearos_load_library('base/Validation_Exception');
clearos_load_library('network_map/Network_Map_Already_Exists_Exception');
clearos_load_library('network_map/Network_Map_Not_Found_Exception');


///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Network map class.
 *
 * @category   apps
 * @package    network-map
 * @subpackage libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/network_map/
 */

class Network_Map extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const FILE_STATE = '/var/clearos/network_map/state';
    const FILE_MAC_MAP = '/var/clearos/network_map/macs.dat';
    const FILE_USERNAME_MAP = '/var/clearos/network_map/usernames.dat';

    const COMMAND_EXPORT_MAP = '/usr/sbin/export-network-map';

    const TYPE_DEFINED = 'defined';
    const TYPE_UNKNOWN = 'unknown';

    const NETWORK_LDAP_CONTAINER = 'ou=Network';
    const NETWORK_LDAP_NAME = 'Network';

    const DEVICE_DESKTOP = 'desktop';
    const DEVICE_LAPTOP = 'laptop';
    const DEVICE_MEDIA = 'media';
    const DEVICE_MOBILE = 'mobile';
    const DEVICE_OTHER = 'other';
    const DEVICE_PRINTER = 'printer';
    const DEVICE_NETWORK = 'network';
    const DEVICE_TABLET = 'tablet';
    const DEVICE_SERVER = 'server';

    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    protected $is_loaded = FALSE;
    protected $config = array();
    protected $subnets = array();

    protected $types = array();
    protected $ldaph = NULL;
    protected $mac_db = array();
    protected $device_types = array();

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Network Map constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        $this->types = array(
            self::TYPE_DEFINED => lang('network_map_defined'),
            self::TYPE_UNKNOWN => lang('network_map_unknown')
        );

        $this->device_types = array(
            self::DEVICE_LAPTOP => lang('network_map_laptop'),
            self::DEVICE_DESKTOP => lang('network_map_desktop'),
            self::DEVICE_TABLET => lang('network_map_tablet'),
            self::DEVICE_MOBILE => lang('network_map_mobile'),
            self::DEVICE_NETWORK => lang('network_map_network_device'),
            self::DEVICE_PRINTER => lang('network_map_printer'),
            self::DEVICE_SERVER => lang('network_map_server'),
            self::DEVICE_MEDIA => lang('network_map_media'),
            self::DEVICE_OTHER => lang('network_map_other')
        );
    }

    /**
     * Adds a network map entry.
     *
     * @param string $mac      MAC address
     * @param string $username username
     * @param string $type     type
     * @param string $nickname nickname
     * @param string $vendor   vendor
     *
     * @return void
     * @throws Engine_Exception, Network_Map_Already_Exists_Exception
     */

    public function add_entry($mac, $username, $type, $nickname, $vendor)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_mac($mac));
        Validation_Exception::is_valid($this->validate_username($username));
        Validation_Exception::is_valid($this->validate_device_type($type));
        Validation_Exception::is_valid($this->validate_nickname($nickname));

        $mac = preg_replace('/-/', ':', $mac);

        // Add LDAP device object
        //-----------------------

        $ldap_object['objectClass'] = array(
            'top',
            'clearcenterNetworkDeviceAccount'
        );

        $ldap_object['clearcenterNetworkMAC'] = $mac;
        $ldap_object['clearcenterNetworkUsername'] = $username;
        $ldap_object['clearcenterNetworkDeviceType'] = $type;

        if (! empty($vendor))
            $ldap_object['clearcenterNetworkDeviceVendor'] = $vendor;

        if (! empty($nickname))
            $ldap_object['clearcenterNetworkNickname'] = $nickname;

        $dn = "clearcenterNetworkMAC=$mac," . self::get_network_container();

        $this->_get_ldap_handle();

        if ($this->ldaph->exists($dn))
            throw new Network_Map_Already_Exists_Exception();

        $this->ldaph->add($dn, $ldap_object);
    }

    /**
     * Deletes a network mapping.
     *
     * @param string $mac MAC address
     *
     * @return void
     * @throws Engine_Exception, Network_Map_Not_Found_Exception
     */

    public function delete_entry($mac)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_mac($mac));

        $mac = preg_replace('/-/', ':', $mac);

        $this->_get_ldap_handle();

        $dn = "clearcenterNetworkMAC=$mac," . self::get_network_container();

        if (! $this->ldaph->exists($dn))
            throw new Network_Map_Not_Found_Exception();

        $this->ldaph->delete($dn);
    }

    /**
     * Exports IP/username mapping.
     *
     * @return void
     * @throws Engine_Exception
     */

    public function export_mapping()
    {
        clearos_profile(__METHOD__, __LINE__);

        $list = $this->get_mapped_list();
        
        $usernames = array();
        $macs = array();

        foreach ($list as $mac => $device) {
            if (! empty($device['mapping'])) {
                foreach ($device['mapping'] as $ip => $details) {
                    $usernames[$ip] = $device['username'];
                    $macs[$ip] = $mac;
                }
            }
        }

        // Macs
        $file = new File(self::FILE_MAC_MAP);

        if ($file->exists())
            $file->delete();

        $file->create('root', 'root', '0644');
        $file->add_lines(json_encode($macs));

        // Usernames
        $file = new File(self::FILE_USERNAME_MAP);

        if ($file->exists())
            $file->delete();

        $file->create('root', 'root', '0644');
        $file->add_lines(json_encode($usernames));
    }

    /**
     * Returns device types.
     *
     * @return array array containing device data
     * @throws Engine_Exception
     */

    public function get_device_types()
    {
        clearos_profile(__METHOD__, __LINE__);

        return $this->device_types;
    }

    /**
     * Returns network map information.
     *
     * @param string $mac MAC address
     *
     * @return array array containing network map information
     * @throws Engine_Exception
     */

    public function get_entry($mac)
    {
        clearos_profile(__METHOD__, __LINE__);

        $mac = preg_replace('/-/', ':', $mac);

        $mappings = $this->get_mapped_list();

        foreach ($mappings as $mac_item => $details) {
            if ($mac_item == $mac)
                return $details;
        }

        $mappings = $this->get_unknown_list();

        foreach ($mappings as $mac_item => $details) {
            if ($mac_item == $mac)
                return $details;
        }
    }

    /**
     * Returns network mappings information.
     *
     * @return array array containing mapping data
     * @throws Engine_Exception
     */

    public function get_mapped_list()
    {
        clearos_profile(__METHOD__, __LINE__);

        // Grab network information
        //-------------------------

        $hosts = new Hosts();
        $hostnames = $hosts->get_entries();

        $iface_manager = new Iface_Manager();
        // $lans = $iface_manager->get_most_trusted_networks(TRUE);

        // Grab list from LDAP
        //--------------------

        $this->_get_ldap_handle();

        $mappings = array();

        $result = $this->ldaph->search(
            "(objectclass=clearcenterNetworkDeviceAccount)",
            self::get_network_container()
        );

        $this->ldaph->sort($result, 'clearcenterNetworkMAC');
        $entry = $this->ldaph->get_first_entry($result);

        while ($entry) {
            $attributes = $this->ldaph->get_attributes($entry);

            $mac = $attributes['clearcenterNetworkMAC'][0];

            // General mapping information
            //----------------------------

            $mapping = array();
            $mapping['username'] = $attributes['clearcenterNetworkUsername'][0];
            $mapping['type'] = $attributes['clearcenterNetworkDeviceType'][0];

            if (! empty($attributes['clearcenterNetworkNickname']))
                $mapping['nickname'] = $attributes['clearcenterNetworkNickname'][0];

            if (! empty($attributes['clearcenterNetworkDeviceVendor']))
                $mapping['vendor'] = $attributes['clearcenterNetworkDeviceVendor'][0];

            // Network/IP mappings
            //--------------------

            if (! empty($attributes['clearcenterNetworkMapping'])) {
                array_shift($attributes['clearcenterNetworkMapping']);

                foreach ($attributes['clearcenterNetworkMapping'] as $network_map) {
                    $parsed_items = preg_split('/\|/', $network_map);
                    $ip = $parsed_items[0];
                    $network = $parsed_items[1];

                    // IP
                    $mapping['mapping'][$ip]['network'] = $network;

                    // Local/remote flag
                    // $mapping['mapping'][$ip]['is_local'] = in_array($network, $lans) ? TRUE : FALSE;

                    // Add hostname information
                    $hex_ip = bin2hex(inet_pton($ip));
                    $hostname = (array_key_exists($hex_ip, $hostnames)) ? $hostnames[$hex_ip]['hostname'] : '';

                    $mapping['mapping'][$ip]['hostname'] = $hostname;
                }
            }

            $mappings[$mac] = $mapping;

            $entry = $this->ldaph->next_entry($entry);
        }

        return $mappings;
    }

    /**
     * Return mapping data by IP.
     *
     * @return array array containing mapping data keyed by IP
     * @throws Engine_Exception
     */

    public function get_mapping_by_ip()
    {
        clearos_profile(__METHOD__, __LINE__);

        $list = $this->get_mapped_list();
        
        $mapping = array();

        foreach ($list as $mac => $device) {
            if (! empty($device['mapping'])) {
                foreach ($device['mapping'] as $ip => $details) {
                    $mapping[$ip]['username'] = $device['username'];
                    $mapping[$ip]['type'] = (empty($device['type'])) ? '' : $device['type'];
                    $mapping[$ip]['vendor'] = (empty($device['vendor'])) ? '' : $device['vendor'];
                    $mapping[$ip]['nickname'] = (empty($device['nickname'])) ? '' : $device['nickname'];
                }
            }
        }

        return $mapping;
    }

    /**
     * Returns unknown MAC list.
     *
     * Lease data is keyed by MAC address, but sorted by IP.
     *
     * @return array array containing unknown MACs
     * @throws Engine_Exception
     */

    public function get_unknown_list()
    {
        clearos_profile(__METHOD__, __LINE__);

        // Hostname information
        //---------------------

        $hosts = new Hosts();
        $hostnames = $hosts->get_entries();

        // DHCP lease information
        //-----------------------

        $leases = array();

        if (clearos_app_installed('dhcp')) {
            clearos_load_library('dhcp/Dnsmasq');

            $dhcp = new \clearos\apps\dhcp\Dnsmasq();
            $leases = $dhcp->get_leases();
        }

        // ARP information
        //----------------

        $arpwatch = new Arpwatch();
        $arps = $arpwatch->get_data();

        // Merge all the above data
        //-------------------------

        $defined = $this->get_mapped_list();
        $unknown = array();

        foreach ($arps as $mac => $mac_object) {
            if (array_key_exists($mac, $defined))
                continue;

            foreach ($mac_object['mapping'] as $ip => $details) {
                // IP calculations
                $hex_ip = bin2hex(inet_pton($ip));
                $long_ip = ip2long($ip);
                $hex_mac = hexdec(preg_replace('/\:/', '', $mac));
                $lease_key = $long_ip . '.' . $hex_mac;

                // Hostname
                $hostname = (array_key_exists($hex_ip, $hostnames)) ? $hostnames[$hex_ip]['hostname'] : '';

                // Lease
                if (array_key_exists($lease_key, $leases)) {
                    $dhcp_lease = TRUE;
                    $dhcp_type = ($leases[$lease_key]['type'] == 'dynamic') ? 'dynamic' : 'static';
                } else {
                    $dhcp_lease = FALSE;
                    $dhcp_type = '';
                }

                // Entry
                if (!isset($unknown[$mac]))
                    $unknown[$mac]['vendor'] = $mac_object['vendor'];

                $mapping = array();
                $mapping['network'] = $details['network'];
                $mapping['last_seen'] = $details['last_seen'];
                $mapping['interface'] = $details['interface'];
                $mapping['hostname'] = $hostname;
                $mapping['dhcp_lease'] = $dhcp_lease;
                $mapping['dhcp_type'] = $dhcp_type;

                $unknown[$mac]['mapping'][$ip] = $mapping;
            }
        }

        return $unknown;
    }

    /**
     * Returns map types.
     *
     * @return array map types
     * @throws Engine_Exception
     */

    public function get_types()
    {
        clearos_profile(__METHOD__, __LINE__);

        return $this->types;
    }

    /**
     * Returns state of map entry.
     *
     * @param string $mac MAC address
     *
     * @return boolean TRUE if map entry exists
     * @throws Engine_Exception
     */

    public function exists_entry($mac)
    {
        clearos_profile(__METHOD__, __LINE__);

        $mac = preg_replace('/-/', ':', $mac);

        $mappings = $this->get_mapped_list();

        foreach ($mappings as $map) {
            if ($map['mac'] === $mac)
                return TRUE;
        }

        return FALSE;
    }

    /**
     * Initializes master/slave mode for accounts.
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    public function initialize()
    {
        clearos_profile(__METHOD__, __LINE__);

        if (clearos_library_installed('central_management/File_Synchronization')) {
            clearos_load_library('central_management/File_Synchronization');

            $file_list = array('network-map-state' => self::FILE_STATE);

            $filesync = new \clearos\apps\central_management\File_Synchronization();
            $filesync->write_configlet('network_map', $file_list);
        }
    }

    /**
     * Runs export mapping.
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    public function run_export_mapping()
    {
        clearos_profile(__METHOD__, __LINE__);

        $options['background'] = TRUE;

        $shell = new Shell();
        $shell->execute(self::COMMAND_EXPORT_MAP, '', TRUE, $options);
    }

    /**
     * Updates an existing entry.
     *
     * @param string $mac      MAC address
     * @param string $username username
     * @param string $type     type
     * @param string $nickname nickname
     * @param string $vendor   vendor
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    public function update_entry($mac, $username, $type, $nickname, $vendor)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_mac($mac));
        Validation_Exception::is_valid($this->validate_username($username));
        Validation_Exception::is_valid($this->validate_device_type($type));
        Validation_Exception::is_valid($this->validate_nickname($nickname));
        Validation_Exception::is_valid($this->validate_vendor($vendor));

        $mac = preg_replace('/-/', ':', $mac);

        // Update LDAP object
        //-------------------

        // LDAP uses empty arrays to delete attributes
        // TODO: create a function call for this

        $type = (empty($type)) ? array() : array($type);
        $vendor = (empty($vendor)) ? array() : array($vendor);
        $nickname = (empty($nickname)) ? array() : array($nickname);

        $ldap_attributes[] = array(
            'clearcenterNetworkUsername' => array($username),
            'clearcenterNetworkDeviceType' => $type,
            'clearcenterNetworkDeviceVendor' => $vendor,
            'clearcenterNetworkNickname' => $nickname
        );

        $dn = "clearcenterNetworkMAC=$mac," . self::get_network_container();

        $this->_get_ldap_handle();

        foreach ($ldap_attributes as $attribute)
            $this->ldaph->modify_attribute($dn, $attribute);
    }

    /**
     * Updates an existing mapping.
     *
     * @param string $mac      MAC address
     * @param string $mappings network/IP mapping
     *
     * @return void
     * @throws Engine_Exception, Validation_Exception
     */

    public function update_mappings($mac, $mappings)
    {
        clearos_profile(__METHOD__, __LINE__);

        Validation_Exception::is_valid($this->validate_mac($mac));

        foreach ($mappings as $ip => $new_ip) {
            if (!empty($new_ip))
                Validation_Exception::is_valid($this->validate_ip($new_ip));
        }

        $mac = preg_replace('/-/', ':', $mac);

        // Merge mapping attributes, remove any mappings with empty IPs (deleted)
        //-----------------------------------------------------------------------

        $ldap_mappings = array();

        $info = $this->get_entry($mac);

        if (! empty($info['mapping'])) {
            foreach ($info['mapping'] as $ip => $details)
                $ldap_mappings[$ip] = $ip . '|' . $details['network']; 
        }

        // Add network information to ease master/slave deployments
        //---------------------------------------------------------

        $iface_manager = new Iface_Manager();
        $lans = $iface_manager->get_most_trusted_networks(TRUE);

        foreach ($mappings as $ip => $new_ip) {
            unset($ldap_mappings[$ip]);

            if (Network_Utils::is_valid_ip($new_ip)) {
                $network = '';

                foreach ($lans as $lan) {
                    if (Network_Utils::ip_on_network($new_ip, $lan))
                        $network = $lan;
                }

                $ldap_mappings[$new_ip] = $new_ip . '|' . $network;
            }
        }

        // Update LDAP object
        //-------------------

        if (count($ldap_mappings) > 0) {
            foreach ($ldap_mappings as $network => $payload)
                $ldap_object['clearcenterNetworkMapping'][] = $payload;
        } else {
            $ldap_object['clearcenterNetworkMapping'] = array();
        }

        $dn = "clearcenterNetworkMAC=$mac," . self::get_network_container();

        $this->_get_ldap_handle();

        $this->ldaph->modify_attribute($dn, $ldap_object);

        $this->run_export_mapping();
    }

    ///////////////////////////////////////////////////////////////////////////////
    // V A L I D A T I O N
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Validates device type.
     *
     * @param string $type device type
     *
     * @return string error message if type is invalid
     */

    public function validate_device_type($type)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! array_key_exists($type, $this->device_types))
            return lang('network_map_device_type_invalid');
    }

    /**
     * Validates hostname.
     *
     * @param string $hostname hostname
     *
     * @return string error message if hostname is invalid
     */

    public function validate_hostname($hostname)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! Network_Utils::is_valid_hostname($hostname))
            return lang('network_hostname_invalid');
    }

    /**
     * Validates IP.
     *
     * @param string $ip IP address
     *
     * @return string error message if IP is invalid
     */

    public function validate_ip($ip)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! Network_Utils::is_valid_ip($ip))
            return lang('network_ip_invalid');
    }

    /**
     * Validates MAC address.
     *
     * @param string  $mac          MAC address
     * @param boolean $check_exists set to TRUE if checking for existing entry
     *
     * @return string error message if MAC address is invalid
     */

    public function validate_mac($mac, $check_exists)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! Network_Utils::is_valid_mac($mac, TRUE))
            return lang('network_mac_address_invalid');

        if ($check_exists) { 
            $list = $this->get_mapped_list();
            if (array_key_exists($mac, $list))
                return lang('network_map_network_map_already_exists');
        }
    }

    /**
     * Validates nickname.
     *
     * @param string $nickname nickname
     *
     * @return string error message if nickname is invalid
     */

    public function validate_nickname($nickname)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (empty($nickname))
            return;

        if (! preg_match('/^[a-zA-Z0-9\._\- ]*$/', $nickname))
            return lang('network_map_nickname_invalid');
    }

    /**
     * Validates username.
     *
     * @param string $username username
     *
     * @return string error message if username is invalid
     */

    public function validate_username($username)
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Validates vendor.
     *
     * @param string $vendor vendor
     *
     * @return string error message if vendor is invalid
     */

    public function validate_vendor($vendor)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (empty($vendor))
            return;

        if (! preg_match('/^[a-zA-Z0-9,\._\- ]*$/', $vendor))
            return lang('network_map_vendor_invalid');
    }

    ///////////////////////////////////////////////////////////////////////////////
    // F R I E N D  M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Returns network container/OU.
     *
     * @access private
     * @return string server container/OU
     */

    public static function get_network_container()
    {
        clearos_profile(__METHOD__, __LINE__);

        $ldap = LDAP_Factory::create();

        return self::NETWORK_LDAP_CONTAINER . ',' . $ldap->get_base_dn();
    }

    ///////////////////////////////////////////////////////////////////////////////
    // P R I V A T E  M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Returns the vendor for a given MAC address.
     *
     * @param string $mac MAC address
     *
     * @return void
     * @throws Engine_Exception
     */

    protected function _get_vendor($mac)
    {
        clearos_profile(__METHOD__, __LINE__);

        $mac = preg_replace('/-/', ':', $mac);

        // Pull in MAC address database
        if (empty($this->mac_database)) {
            include clearos_app_base('network_map') . '/deploy/mac_database.php';
            $this->mac_database = $mac_database;
        }

        $vendor = '';
        $matches = array();

        if (preg_match('/^(.*):(.*):(.*):(.*):(.*):(.*)/', strtoupper($mac), $matches)) {
            $mac = sprintf(
                '%02s:%02s:%02s:%02s:%02s:%02s',
                $matches[1],
                $matches[2],
                $matches[3],
                $matches[4],
                $matches[5],
                $matches[6]
            );

            $mac_prefix = strtoupper(substr($mac, 0, 8));
            $vendor = isset($this->mac_database[$mac_prefix]) ? $this->mac_database[$mac_prefix] : '';
        }

        return $vendor;
    }

    /**
     * Initializes and sets object LDAP handle
     *
     * @return void
     * @throws Engine_Exception
     */

    protected function _get_ldap_handle()
    {
        clearos_profile(__METHOD__, __LINE__);

        if (! empty($this->ldaph))
            return;

        $ldap = LDAP_Factory::create();
        $this->ldaph = $ldap->get_ldap_handle();

        $ldap_object['objectClass'] = array(
            'top',
            'organizationalUnit'
        );

        $ldap_object['ou'] = self::NETWORK_LDAP_NAME;
        $dn = self::NETWORK_LDAP_CONTAINER . ',' . $ldap->get_base_dn();

        if (! $this->ldaph->exists($dn))
            $this->ldaph->add($dn, $ldap_object);
    }
}
