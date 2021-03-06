<?php

namespace Behat\Behat\Context;

/*
 * This file is part of the Behat.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Context interface.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface ContextInterface
{
    /**
     * Returns all added subcontexts.
     *
     * @return  array
     */
    function getSubcontexts();

    /**
     * Finds subcontext by it's name.
     *
     * @return  Behat\Behat\Context\ContextInterface
     */
    function getSubcontextByClassName($className);
}
