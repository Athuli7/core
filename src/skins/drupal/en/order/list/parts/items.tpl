{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Orders list items block
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   SVN: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 * @ListChild (list="orders.childs", weight="20")
 *}
<ul IF="getPageData()" class="list">

  <li FOREACH="getPageData(),order" class="order-{order.order_id}">

    <div class="order-body-item">

      <div class="title">

        <ul class="order-body-item-spec">
          <li class="date">{time_format(order.date)}</li>
          <li class="details">
            <button onclick="javascript:document.location='{buildURL(#order#,##,_ARRAY_(#order_id#^order.order_id))}';">{t(#Details#)}</button>
          </li>
          <li class="order-shipping-status"><widget template="common/order_status.tpl" /></li>
        </ul>

        <ul class="order-body-item-spec2">
          <li class="orderid">{t(#Order ID#)}: <span class="orderid">#{order.order_id}</span></li>
          <li class="total">{t(#Grand total#)}: <span class="sum">{price_format(order,#total#):h}</span></li>
          {** 
            *TODO divide main status into payment/shipping separated statuses

          <li class="order-payment-status"><widget template="common/order_payment_status.tpl" /></li>
          *}
        </ul>


      </div>

      <div class="order-body-items-list">

        <widget class="\XLite\View\OrderItemsShort" full="true" order="{order}" />

      <div>

    </div>

  </li>

</ul>
