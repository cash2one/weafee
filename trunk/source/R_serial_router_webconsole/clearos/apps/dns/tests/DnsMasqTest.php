<?php

/**
 * DnsMasq class.
 *
 * @category   apps
 * @package    dns
 * @subpackage Tests
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2003-2011 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/dns/
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
// P H P U N I T
///////////////////////////////////////////////////////////////////////////////

require_once 'PHPUnit/Framework.php';

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = isset($_ENV['CLEAROS_BOOTSTRAP']) ? $_ENV['CLEAROS_BOOTSTRAP'] : '/usr/clearos/framework/shared';
require_once($bootstrap . '/bootstrap.php');

clearos_load_library('dns/DnsMasq');


///////////////////////////////////////////////////////////////////////////////
// T E S T
///////////////////////////////////////////////////////////////////////////////

/**
 * Test class for DnsMasq.
 * Generated by PHPUnit on 2010-11-21 at 22:43:12.
 */
class DnsMasqTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    DnsMasq
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->object = new DnsMasq;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    /**
     * @todo Implement testAddStaticLease().
     */
    public function testAddStaticLease() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAddSubnet().
     */
    public function testAddSubnet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDeleteStaticLease().
     */
    public function testDeleteStaticLease() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testDeleteSubnet().
     */
    public function testDeleteSubnet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testEnableDhcpAutomagically().
     */
    public function testEnableDhcpAutomagically() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSubnetExists().
     */
    public function testSubnetExists() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetAuthoritativeState().
     */
    public function testGetAuthoritativeState() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDhcpInterfaces().
     */
    public function testGetDhcpInterfaces() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDhcpState().
     */
    public function testGetDhcpState() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDomainName().
     */
    public function testGetDomainName() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetLeases().
     */
    public function testGetLeases() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetActiveLeases().
     */
    public function testGetActiveLeases() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetStaticLeases().
     */
    public function testGetStaticLeases() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetSubnet().
     */
    public function testGetSubnet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetSubnetDefault().
     */
    public function testGetSubnetDefault() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetSubnets().
     */
    public function testGetSubnets() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetAuthoritativeState().
     */
    public function testSetAuthoritativeState() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetDhcpState().
     */
    public function testSetDhcpState() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetDomainName().
     */
    public function testSetDomainName() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testUpdateSubnet().
     */
    public function testUpdateSubnet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValidateSubnet().
     */
    public function testValidateSubnet() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * testValidateDomain().
     */
    public function testValidateDomain() {
		$result = $this->object->ValidateDomain('example.com');
		$expected = '';
		$this->assertTrue($result === $expected);
    }

    public function testValidateDomainEmpty() {
		$result = $this->object->ValidateDomain('');
		$expected = '';
		$this->assertTrue($result !== $expected);
    }

    public function testValidateDomainInvalidChars() {
		$result = $this->object->ValidateDomain('@#$@');
		$expected = '';
		$this->assertTrue($result !== $expected);
    }

    /**
     * testValidateStartIp().
     */
    public function testValidateStartIp() {
		$result = $this->object->ValidateStartIp('4.4.4.4');
		$expected = '';
		$this->assertTrue($result === $expected);
    }

    public function testValidateStartIpEmpty() {
		$result = $this->object->ValidateStartIp('');
		$expected = '';
		$this->assertTrue($result !== $expected);
    }

    public function testValidateStartIpInvalidChars() {
		$result = $this->object->ValidateStartIp('@#$@');
		$expected = '';
		$this->assertTrue($result !== $expected);
    }

    /**
     * @todo Implement testValidateEndIp().
     */
    public function testValidateEndIp() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValidateGateway().
     */
    public function testValidateGateway() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValidateNtp().
     */
    public function testValidateNtp() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValidateTftp().
     */
    public function testValidateTftp() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testValidateWins().
     */
    public function testValidateWins() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement test__destruct().
     */
    public function test__destruct() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement test_LoadConfig().
     */
    public function test_LoadConfig() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement test_SaveConfig().
     */
    public function test_SaveConfig() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }
}
?>