<?php

/**
 * Samba OpenLDAP group extension.
 *
 * @category   apps
 * @package    samba-extension
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/samba_extension/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\samba_extension;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('samba');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\samba\OpenLDAP_Driver as OpenLDAP_Driver;
use \clearos\apps\samba_common\Samba as Samba;

clearos_load_library('base/Engine');
clearos_load_library('samba/OpenLDAP_Driver');
clearos_load_library('samba_common/Samba');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Samba OpenLDAP group extension.
 *
 * @category   apps
 * @package    samba-extension
 * @subpackage libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/samba_extension/
 */

class OpenLDAP_Group_Extension extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    protected $info_map = array();

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Samba OpenLDAP_group extension constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        include clearos_app_base('samba_extension') . '/deploy/group_map.php';

        $this->info_map = $info_map;
    }

    /** 
     * Add LDAP attributes hook.
     *
     * @param array $group_info  group information in hash array
     * @param array $ldap_object LDAP object
     *
     * @return array LDAP attributes
     * @throws Engine_Exception
     */

    public function add_attributes_hook($group_info, $ldap_object)
    {
        clearos_profile(__METHOD__, __LINE__);

        // Process attributes
        //-------------------

        $samba = new Samba();

        $sid = $samba->get_domain_sid();
        $domain = $samba->get_workgroup();

        // Add built-in attributes
        //------------------------

        if (isset($group_info['extensions']['samba']['sid']))
            $attributes['sambaSID'] = $group_info['extensions']['samba']['sid'];
        else
            $attributes['sambaSID'] = $sid . '-' . $group_info['core']['gid_number'];

        if (isset($group_info['extensions']['samba']['group_type']))
            $attributes['sambaGroupType'] = $group_info['extensions']['samba']['group_type'];
        else
            $attributes['sambaGroupType'] = 2;

        if (isset($group_info['extensions']['samba']['display_name']))
            $attributes['displayName'] = $group_info['extensions']['samba']['display_name'];
        else
            $attributes['displayName'] = $group_info['core']['group_name'];

        if (isset($group_info['extensions']['samba']['sid_list']))
            $attributes['sambaSIDList'] = $group_info['extensions']['samba']['sid_list'];

        $attributes['objectClass'][] = 'sambaGroupMapping';

        return $attributes;
    }

    /**
     * Returns group info hash array.
     *
     * @param array $attributes LDAP attributes
     *
     * @return array group info array
     * @throws Engine_Exception
     */

    public function get_info_hook($attributes)
    {
        clearos_profile(__METHOD__, __LINE__);

        $info = array();

        if (isset($attributes['sambaSID']))
            $info['sid'] = $attributes['sambaSID'][0];

        return $info;
    }

    /** 
     * Returns user info hash array.
     *
     * @return array user info array
     * @throws Engine_Exception
     */

    public function get_info_map_hook()
    {
        clearos_profile(__METHOD__, __LINE__);

        return $this->info_map;
    }

    /** 
     * Update LDAP attributes hook.
     *
     * @param array $group_info  group information in hash array
     * @param array $ldap_object LDAP object
     *
     * @return array LDAP attributes
     * @throws Engine_Exception
     */

    public function update_attributes_hook($group_info, $ldap_object)
    {
        clearos_profile(__METHOD__, __LINE__);

        if (isset($group_info['extensions']['samba']['sid']))
            $attributes['sambaSID'] = $group_info['extensions']['samba']['sid'];

        if (isset($group_info['extensions']['samba']['group_type']))
            $attributes['sambaGroupType'] = $group_info['extensions']['samba']['group_type'];

        if (isset($group_info['extensions']['samba']['display_name']))
            $attributes['displayName'] = $group_info['extensions']['samba']['display_name'];

        if (isset($group_info['extensions']['samba']['sid_list']))
            $attributes['sambaSIDList'] = $group_info['extensions']['samba']['sid_list'];

        $attributes['objectClass'][] = 'sambaGroupMapping';

        return $attributes;
    }
}
