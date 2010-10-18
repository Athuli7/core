<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * LiteCommerce
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@litecommerce.com so we can send you a copy immediately.
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

require_once PATH_TESTS . '/FakeClass/Model/OrderItem.php';

class XLite_Tests_Model_OrderDetail extends XLite_Tests_TestCase
{
    protected $testOrder = array(
        'tracking'       => 'test t',
        'notes'          => 'Test note',
    );

    protected function setUp()
    {
        parent::setUp();

        \XLite\Core\Database::getEM()->clear();
    }

    public function testCreate()
    {
        $order = $this->getTestOrder();

        $this->assertEquals(2, $order->getDetails()->count(), 'check details count');

        $this->assertEquals('t1', $order->getDetails()->get(0)->getName(), 'check name');
        $this->assertEquals('123', $order->getDetails()->get(0)->getValue(), 'check value');
        $this->assertNull($order->getDetails()->get(0)->getLabel(), 'check label');

        $this->assertEquals('t2', $order->getDetails()->get(1)->getName(), 'check name #2');
        $this->assertEquals('456', $order->getDetails()->get(1)->getValue(), 'check value #2');
        $this->assertEquals('test', $order->getDetails()->get(1)->getLabel(), 'check label #2');

        $this->assertEquals($order->getOrderId(), $order->getDetails()->get(0)->getOrder()->getOrderId(), 'check order id');
    }

    public function testUpdate()
    {
        $order = $this->getTestOrder();

        $d = $order->getDetails()->get(0);

        $d->setName('t0');
        $d->setValue('999');
        $d->setLabel('zzz');

        $this->assertEquals('t0', $d->getName(), 'check name');
        $this->assertEquals('999', $d->getValue(), 'check value');
        $this->assertEquals('zzz', $d->getLabel(), 'check label');
    }

    public function testDelete()
    {
        $order = $this->getTestOrder();

        $d = $order->getDetails()->get(0);

        $order->getDetails()->removeElement($d);
        \XLite\Core\Database::getEM()->remove($d);

        $id = $d->getDetailId();

        \XLite\Core\Database::getEM()->flush();

        $d = \XLite\Core\Database::getRepo('XLite\Model\OrderDetail')
            ->find($id);

        $this->assertNull($d, 'check removed detail');
    }

    public function testGetDisplayName()
    {
        $order = $this->getTestOrder();

        $this->assertEquals('t1', $order->getDetails()->get(0)->getDisplayName(), 'check name');
        $this->assertEquals('test', $order->getDetails()->get(1)->getDisplayName(), 'check name #2');
    }

    protected function getProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneByEnabled(true);
    }

    protected function getTestOrder()
    {
        $order = new \XLite\Model\Order();

        $list = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findAll();
        $profile = array_shift($list);
        unset($list);

        $order->map($this->testOrder);
        $order->setCurrency(\XLite\Core\Database::getRepo('XLite\Model\Currency')->find(840));
        $order->setProfileId(0);

        $order->setDetail('t1', '123');
        $order->setDetail('t2', '456', 'test');

        $item = new \XLite\Model\OrderItem();

        $item->setProduct($this->getProduct());
        $item->setAmount(1);
        $item->setPrice($this->getProduct()->getPrice());

        $order->addItem($item);

        \XLite\Core\Database::getEM()->persist($order);
        \XLite\Core\Database::getEM()->flush();

        $order->setProfileCopy($profile);
        $order->calculate();

        \XLite\Core\Database::getEM()->persist($order);
        \XLite\Core\Database::getEM()->flush();

        return $order;
    }
}
