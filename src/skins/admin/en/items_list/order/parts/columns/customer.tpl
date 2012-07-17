{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Item name
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 * @since     1.0.0
 * @ListChild (list="itemsList.order.admin.search.columns", weight="60")
 *}

<td class="customer">
  <a IF="!order.orig_profile.getProfileId()=order.profile.getProfileId()"
    class="customer"
    href="{buildURL(#profile#,##,_ARRAY_(#profile_id#^order.orig_profile.getProfileId()))}">
    {order.profile.billing_address.title} {order.profile.billing_address.firstname} {order.profile.billing_address.lastname}
  </a>
  <span IF="order.orig_profile.getProfileId()=order.profile.getProfileId()" class="customer">
    {order.profile.billing_address.title} {order.profile.billing_address.firstname} {order.profile.billing_address.lastname}
  </span>
</td>
