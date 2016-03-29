<?php

/**
 * OS Edition class.
 *
 * @category   apps
 * @package    edition
 * @subpackage libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/edition/
 */

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\edition;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('edition');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\File as File;
use \clearos\apps\base\Yum as Yum;

clearos_load_library('base/Engine');
clearos_load_library('base/File');
clearos_load_library('base/Yum');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * OS Edition class.
 *
 * @category   apps
 * @package    edition
 * @subpackage libraries
 * @author     ClearCenter <developer@clearcenter.com>
 * @copyright  2012 ClearCenter
 * @license    http://www.clearcenter.com/app_license ClearCenter license
 * @link       http://www.clearcenter.com/support/documentation/clearos/edition/
 */

class Edition extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // C O N S T A N T S
    ///////////////////////////////////////////////////////////////////////////////

    const COMMAND_PID = '/sbin/pidof';
    const COMMAND_WC_YUM = '/usr/sbin/wc-yum';
    const EDITION_COMMUNITY = 'community';
    const EDITION_PROFESSIONAL = 'professional';
    const FILE_REPO = '/etc/yum.repos.d/clearos-professional.repo';

    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    protected $source_nepo = NULL;

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Edition constructor.
     */

    public function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);

        $this->source_repo = clearos_app_base('edition') . '/deploy/clearos-professional.repo';
    }

    /**
     * Enables upgrade to Professional.
     * 
     * @param string $edition edition
     *
     * @return integer exit code
     * @throws Validation_Exception
     */

    public function set($edition)
    {
        clearos_profile(__METHOD__, __LINE__);

        $target_repo = new File(self::FILE_REPO);

        if ($target_repo->exists())
            $target_repo->delete();

        if ($edition == self::EDITION_PROFESSIONAL) {
            $source_repo = new File($this->source_repo);
            $source_repo->copy_to(self::FILE_REPO);
        }

        try {
            $yum = new Yum();
            $yum->clean();
        } catch (Exception $e) {
            // Not fatal
        }
    }
}
