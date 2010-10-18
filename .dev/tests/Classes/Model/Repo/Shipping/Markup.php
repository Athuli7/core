<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * XLite\Model\Repo\Shipping\Markup class tests
 *
 * @category   LiteCommerce
 * @package    Tests
 * @subpackage Classes
 * @author     Creative Development LLC <info@cdev.ru>
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

class XLite_Tests_Model_Repo_Shipping_Markup extends XLite_Tests_TestCase
{
    /**
     * testFindMarkupsByProcessor 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function testFindMarkupsByProcessor()
    {
        // Test on real profile
        $markups = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup')->findMarkupsByProcessor('offline', $this->getTestOrder());

        $this->assertTrue(is_array($markups), 'findMarkupsByProcessor() must return an array');

        foreach ($markups as $markup) {
            $this->assertTrue($markup instanceof \XLite\Model\Shipping\Markup, 'findMarkupsByProcessor() must return an array of \XLite\Model\Shipping\Markup instances');
        }

        // Test on anonymous user (no profile)
        $markups = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup')->findMarkupsByProcessor('offline', $this->getTestOrder(false));

        $this->assertTrue(is_array($markups), 'findMarkupsByProcessor() must return an array');

        foreach ($markups as $markup) {
            $this->assertTrue($markup instanceof \XLite\Model\Shipping\Markup, 'findMarkupsByProcessor() must return an array of \XLite\Model\Shipping\Markup instances');
        }
    }

    /**
     * testFindMarkupsByZoneAndMethod 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function testFindMarkupsByZoneAndMethod()
    {
        $methodId = 100;
        $zoneId = 1;

        $markups = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup')->findMarkupsByZoneAndMethod(999999, 999999999);

        $this->assertTrue(is_array($markups), 'findMarkupsByZoneAndMethod() must return an array');

        $markups = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup')->findMarkupsByZoneAndMethod($zoneId, $methodId);

        $this->assertTrue(is_array($markups), 'findMarkupsByZoneAndMethod() must return an array');

        foreach ($markups as $markup) {
            $this->assertTrue($markup instanceof \XLite\Model\Shipping\Markup, 'findMarkupsByZoneAndMethod() must return an array of \XLite\Model\Shipping\Markup instances');
        }
    }

    /**
     * findMarkupsByIds 
     * 
     * @return void
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function testFindMarkupsByIds()
    {
        $allMarkups = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup')->findAll();

        $allMarkupsIds = array();
        foreach ($allMarkups as $markup) {
            $allMarkupsIds[] = $markup->getMarkupId();
        }

        $markups = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Markup')->findMarkupsByIds($allMarkupsIds);

        $markupsIds = array();
        foreach ($markups as $markup) {
            $markupsIds[] = $markup->getMarkupId();
        }

        $this->assertEquals($allMarkupsIds, $markupsIds, 'Markup Ids comparison failed');
    }

    /**
     * getTestOrder 
     * 
     * @param bool $profile Flag: if true, then get real profile from database
     *  
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function getTestOrder($profile = true)
    {
        $order = new \XLite\Model\Order();

        if ($profile) {
            $profiles = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findAll();
            $profile = array_shift($profiles);
            $profileId = $profile->getProfileId();
            unset($profiles);

        } else {
            $profileId = 0;
        }

        $order->setCurrency(\XLite\Core\Database::getRepo('XLite\Model\Currency')->find(840));
        $order->setProfileId($profileId);
        $order->setSubTotal(17.99);

        return $order;
    }

}
