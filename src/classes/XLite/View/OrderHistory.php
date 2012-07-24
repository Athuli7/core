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
 * @since     1.0.0
 */

namespace XLite\View;

/**
 * Order history widget
 *
 * @see   ____class_see____
 * @since 1.0.0
 *
 */
class OrderHistory extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_ORDER = 'order';

    /**
     * Date presentation format
     */
    const DAY_DATE_FORMAT = 'M j, Y';
    const EVENT_DATE_FORMAT = 'h:i a';

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getOrderId()
    {
        return \XLite\Core\Request::getInstance()->order_id;
    }

    /**
     * Register CSS files
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'order/history/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'order/history/script.js';

        return $list;
    }

    /**
     * Return default template
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDefaultTemplate()
    {
        return 'order/history/body.tpl';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getOrderId();
    }

    /**
     * Get blocks for the events of order
     *
     * @return array
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getOrderHistoryEventsBlock()
    {
        $result = array();

        foreach (\XLite\Core\Database::getRepo('XLite\Model\OrderHistoryEvents')->search($this->getOrderId()) as $event) {

            $result[$this->getDayDate($event->getDate())][] = $event;
        }

        return $result;
    }

    /**
     * Date getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDate(\XLite\Model\OrderHistoryEvents $event)
    {
        return date(static::EVENT_DATE_FORMAT, $event->getDate());
    }

    /**
     * Description getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDescription(\XLite\Model\OrderHistoryEvents $event)
    {
        return $event->getDescription();
    }

    /**
     * Details getter
     *
     * @param \XLite\Model\OrderHistoryEvents $event
     *
     * @return type
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDetails(\XLite\Model\OrderHistoryEvents $event)
    {
        return $event->getDetails();
    }

    /**
     * Get day of the given date
     *
     * @param integer $date
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getDayDate($date)
    {
        return date(static::DAY_DATE_FORMAT, $date);
    }

    /**
     * Return header of the block
     *
     * @param string $index
     *
     * @return string
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function getHeaderBlock($index)
    {
        return $index;
    }
}
