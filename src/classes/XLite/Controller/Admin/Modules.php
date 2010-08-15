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
 * @package    XLite
 * @subpackage Controller
 * @author     Creative Development LLC <info@cdev.ru> 
 * @copyright  Copyright (c) 2010 Creative Development LLC <info@cdev.ru>. All rights reserved
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @version    SVN: $Id$
 * @link       http://www.litecommerce.com/
 * @see        ____file_see____
 * @since      3.0.0
 */

namespace XLite\Controller\Admin;

/**
 * Modules
 * 
 * @package XLite
 * @see     ____class_see____
 * @since   3.0.0
 */
class Modules extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Modules list (cache)
     * 
     * @var    array
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $modules = null;

    /**
     * Current module type 
     * 
     * @var    integer
     * @access protected
     * @see    ____var_see____
     * @since  3.0.0
     */
    protected $currentModuleType = null;

    /**
     * Handles the request.
     * Parses the request variables if necessary. Attempts to call the specified action function 
     * 
     * @return void
     * @access public
     * @since  3.0.0
     */
    public function handleRequest()
    {
        \XLite\Core\Database::getRepo('\XLite\Model\Module')->checkModules();

        parent::handleRequest();
    }

    /**
     * Get modules list
     * 
     * @param integer $type Module type
     *  
     * @return array
     * @access public
     * @see    ____func_see____
     * @since  3.0.0
     */
    public function getModules($type = null)
    {
        if (is_null($this->modules) || $type !== $this->currentModuleType) {
            $this->currentModuleType = $type;
            $this->modules = \XLite\Core\Database::getRepo('\XLite\Model\Module')->findByType($type);
        }

        return $this->modules;
    }

    /**
     * Update modules list
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionUpdate()
    {
        $activeModules = isset(\XLite\Core\Request::getInstance()->active_modules)
            ? \XLite\Core\Request::getInstance()->active_modules
            : array();

        $moduleType = isset(\XLite\Core\Request::getInstance()->module_type)
            ? \XLite\Core\Request::getInstance()->module_type
            : null;

        $this->set('returnUrl', $this->buildUrl('modules'));

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Module')->findByType($moduleType) as $module) {
            $module->setEnabled(in_array($module->getModuleId(), $activeModules));
            $module->disableDepended();
            \XLite\Core\Database::getEM()->persist($module);
        }

        \XLite\Core\Database::getEM()->flush();
        \XLite::setCleanUpCacheFlag(true);
    }

    /**
     * Uninstall module
     * 
     * @return void
     * @access protected
     * @see    ____func_see____
     * @since  3.0.0
     */
    protected function doActionUninstall()
    {
        $module = \XLite\Core\Database::getRepo('\XLite\Model\Module')->find(
            \XLite\Core\Request::getInstance()->module_id
        );

        if (!$module) {

            \XLite\Core\TopMessage::getInstance()->addError('The module to uninstall has not been found');

        } else {
            $notes = $module->getMainClass()->getPostUninstallationNotes();

            $module->disableDepended();

            \XLite::setCleanUpCacheFlag(true);

            $status = $module->uninstall();

            \XLite\Core\Database::getEM()->remove($module);
            \XLite\Core\Database::getEM()->flush();

            if ($status) {
                \XLite\Core\TopMessage::getInstance()->addInfo('The module has been uninstalled successfully');

            } else {
                \XLite\Core\TopMessage::getInstance()->addWarning('The module has been partially uninstalled');
            }

            if ($notes) {
                \XLite\Core\TopMessage::getInstance()->add(
                    $notes,
                    \XLite\Core\TopMessage::INFO,
                    true
                );
            }
        }
    }

}
