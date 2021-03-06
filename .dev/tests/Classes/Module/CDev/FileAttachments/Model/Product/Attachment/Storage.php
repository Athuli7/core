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
 * PHP version 5.3.0
 * 
 * @category  LiteCommerce
 * @author    Creative Development LLC <info@cdev.ru> 
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @see       ____file_see____
 * @since     1.0.10
 */

/**
 * XLite_Tests_Module_CDev_FileAttachments_Model_Product_Attachment_Storage 
 *
 * @see   ____class_see____
 * @since 1.0.12
 */
class XLite_Tests_Module_CDev_FileAttachments_Model_Product_Attachment_Storage extends XLite_Tests_TestCase
{
    /**
     * testLoading
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.12
     */
    public function testLoading()
    {
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_FILES . 'attachments');
        $product = $this->getProduct();

        $attach = new \XLite\Module\CDev\FileAttachments\Model\Product\Attachment;
        $product->addAttachments($attach);
        $attach->setProduct($product);

        $storage = $attach->getStorage();

        // Success    
        $this->assertTrue($storage->loadFromLocalFile(__DIR__ . LC_DS . '..' . LC_DS . 'max_ava.png'), 'check loading');
        $this->assertEquals('application/octet-stream', $storage->getMime(), 'check mime');
        $this->assertEquals('max_ava.png', $storage->getFileName(), 'check file name');
        $this->assertEquals(12673, $storage->getSize(), 'check size');
        $this->assertEquals(file_get_contents(__DIR__ . '/../max_ava.png'), $storage->getBody(), 'check body');
        $this->assertRegExp('/^http:\/\//Ss', $storage->getURL(), 'check URL');
        $this->assertEquals($storage->getURL(), $storage->getFrontURL(), 'check front url');
        $this->assertEquals('png', $storage->getExtension(), 'check extension');
        $this->assertEquals('mime-icon-png', $storage->getMimeClass(), 'check MIME class');
        $this->assertEquals('png file type', $storage->getMimeName(), 'check MIME name');

        // Fail
        $this->assertFalse($storage->loadFromLocalFile(__DIR__ . LC_DS . '..' . LC_DS . 'wrong.png'), 'check loading (fail)');

        // Duplicate
        $this->assertTrue($storage->loadFromLocalFile(__DIR__ . LC_DS . '..' . LC_DS . 'max_ava.png'), 'check loading (dup)');
        $this->assertRegExp('/^max_ava.png$/Ss', $storage->getFileName(), 'check file name (rename)');

        // Forbid extension
        $this->assertFalse($storage->loadFromLocalFile(__FILE__), 'check loading (forbid ext)');
        $this->assertEquals('extension', $storage->getLoadError(), 'check load error code');

        // Duplicate
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_FILES . 'attachments');
        $s1 = $this->getTestStorage();
        $s2 = $this->getTestStorage();
        $path = LC_DIR_FILES . 'attachments' . LC_DS . $s1->getPath();
        $this->assertTrue($s2->loadFromLocalFile($path), 'check duplicate loading');
        \XLite\Core\Database::getEM()->flush();

        $pid = $s1->getAttachment()->getProduct()->getProductId();
        $url = XLite::getInstance()->getShopURL('files/attachments/' . $pid . '/' . basename($path));
        $this->assertEquals($url, $s1->getFrontURL(), 'check 1 storage URL');
        $this->assertEquals($url, $s2->getFrontURL(), 'check 2 storage URL');

        $body = file_get_contents($path);
        $this->assertEquals(md5($body), md5($s1->getBody()), 'check 1 body');
        $this->assertEquals(md5($body), md5($s2->getBody()), 'check 2 body');

        ob_start();
        $s1->readOutput();
        $b1 = ob_get_contents();
        ob_end_clean();

        ob_start();
        $s2->readOutput();
        $b2 = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($body, $b1, 'check 1 output');
        $this->assertEquals($body, $b2, 'check 2 output');
    }

    /**
     * testRemove
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.12
     */
    public function testRemove()
    {
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_FILES . 'attachments');
        foreach (\XLite\Core\Database::getRepo('\XLite\Module\CDev\FileAttachments\Model\Product\Attachment')->findAll() as $storage) {
            \XLite\Core\Database::getEM()->remove($storage);
        }
        \XLite\Core\Database::getEM()->flush();

        $storage = $this->getTestStorage();

        $path = LC_DIR_FILES . 'attachments' . LC_DS . $storage->getPath();
        $this->assertTrue(file_exists($path), 'check exist');

        $storage->getAttachment()->getProduct()->getAttachments()->removeElement($storage->getAttachment());
        \XLite\Core\Database::getEM()->remove($storage->getAttachment());

        \XLite\Core\Database::getEM()->flush();

        $this->assertFalse(file_exists($path), 'check remove');

        // Duplicate
        $s1 = $this->getTestStorage();
        $s2 = $this->getTestStorage();
        $this->assertTrue($s2->loadFromLocalFile($path), 'check duplicate loading');
        \XLite\Core\Database::getEM()->flush();

        \XLite\Core\Database::getEM()->remove($s1->getAttachment());
        \XLite\Core\Database::getEM()->flush();
        $this->assertTrue(file_exists($path), 'check remove (duplicate)');

        \XLite\Core\Database::getEM()->remove($s2->getAttachment());
        \XLite\Core\Database::getEM()->flush();
        $this->assertFalse(file_exists($path), 'check remove (duplicate) #2');
    }

    /**
     * testRenewStorage
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.12
     */
    public function testRenewStorage()
    {
        \Includes\Utils\FileManager::unlinkRecursive(LC_DIR_FILES . 'attachments');
        $storage = $this->getTestStorage();
        $size = $storage->getSize();

        $path = LC_DIR_FILES . 'attachments' . LC_DS . $storage->getPath();
        $this->assertTrue(file_exists($path), 'check exist');

        $s2 = $this->getTestStorage();
        $this->assertTrue($s2->loadFromLocalFile($path), 'check duplicate loading');
        \XLite\Core\Database::getEM()->flush();

        unlink($path);
        copy(__DIR__ . '/../vertical_dots.png', $path);
        $this->assertTrue($storage->renewStorage(), 'check renew storage status');

        $this->assertNotEquals($size, $storage->getSize(), 'check old file size');
        $this->assertEquals(filesize($path), $storage->getSize(), 'check file size');
        $this->assertEquals(filesize($path), $s2->getSize(), 'check file size #2');
    }

    /**
     * getTestStorage
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.12
     */
    protected function getTestStorage()
    {
        $product = $this->getProduct();

        $attach = new \XLite\Module\CDev\FileAttachments\Model\Product\Attachment;
        $product->addAttachments($attach);
        $attach->setProduct($product);

        $storage = $attach->getStorage();

        $this->assertTrue($storage->loadFromLocalFile(__DIR__ . LC_DS . '..' . LC_DS . 'max_ava.png'), 'check loading');

        \XLite\Core\Database::getEM()->flush();

        return $storage;
    }
}
