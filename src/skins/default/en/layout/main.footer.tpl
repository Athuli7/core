{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Footer
 *
 * @author    Creative Development LLC <info@cdev.ru>
 * @copyright Copyright (c) 2011 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version   GIT: $Id$
 * @link      http://www.litecommerce.com/
 * @since     3.0.0
 *
 * @ListChild (list="layout.main", weight="500")
 *}

<div id="footer-area">

  <widget class="\XLite\View\Menu\Customer\Footer" />

  <div id="footer">
    <div class="section">
      {displayViewListContent(#sidebar.footer#)}
    </div>
  </div>

</div>
