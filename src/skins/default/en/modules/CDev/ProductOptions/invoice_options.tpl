{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Display product options in Invoice
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011-2012 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.litecommerce.com/
 *}
<ul class="item-options-list">
  <li FOREACH="item.getProductOptions(),option">{option.getName()}: {option.getValue()}</li>
</ul>
