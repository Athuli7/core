<?php
/**
 * This file is part of PHP_Depend.
 *
 * PHP Version 5
 *
 * Copyright (c) 2008-2011, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    SVN: $Id$
 * @link       http://www.pdepend.org/
 */

require_once dirname(__FILE__) . '/ASTNodeTest.php';

require_once 'PHP/Depend/Code/ASTConstant.php';

/**
 * Test case for the {@link PHP_Depend_Code_ASTConstant} class.
 *
 * @category   PHP
 * @package    PHP_Depend
 * @subpackage Code
 * @author     Manuel Pichler <mapi@pdepend.org>
 * @copyright  2008-2011 Manuel Pichler. All rights reserved.
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    Release: @package_version@
 * @link       http://www.pdepend.org/
 */
class PHP_Depend_Code_ASTConstantTest extends PHP_Depend_Code_ASTNodeTest
{
    /**
     * testAcceptInvokesVisitOnGivenVisitor
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptInvokesVisitOnGivenVisitor()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitConstant'));

        $constant = new PHP_Depend_Code_ASTConstant();
        $constant->accept($visitor);
    }

    /**
     * testAcceptReturnsReturnValueOfVisitMethod
     *
     * @return void
     * @covers PHP_Depend_Code_ASTNode
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testAcceptReturnsReturnValueOfVisitMethod()
    {
        $visitor = $this->getMock('PHP_Depend_Code_ASTVisitorI');
        $visitor->expects($this->once())
            ->method('__call')
            ->with($this->equalTo('visitConstant'))
            ->will($this->returnValue(42));

        $constant = new PHP_Depend_Code_ASTConstant();
        self::assertEquals(42, $constant->accept($visitor));
    }

    /**
     * Tests that a parsed constant has the expected object graph.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantGraphSimple()
    {
        $constant = $this->_getFirstConstantInFunction(__METHOD__);
        $this->assertEquals('FOO', $constant->getImage());
    }

    /**
     * Tests that a parsed constant has the expected object graph.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantGraphKeywordSelf()
    {
        $constant = $this->_getFirstConstantInFunction(__METHOD__);
        $this->assertEquals('self', $constant->getImage());
    }

    /**
     * Tests that a parsed constant has the expected object graph.
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantGraphKeywordParent()
    {
        $constant = $this->_getFirstConstantInFunction(__METHOD__);
        $this->assertEquals('parent', $constant->getImage());
    }

    /**
     * testConstantHasExpectedStartLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantHasExpectedStartLine()
    {
        $constant = $this->_getFirstConstantInFunction(__METHOD__);
        $this->assertEquals(4, $constant->getStartLine());
    }

    /**
     * testConstantHasExpectedStartColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantHasExpectedStartColumn()
    {
        $constant = $this->_getFirstConstantInFunction(__METHOD__);
        $this->assertEquals(12, $constant->getStartColumn());
    }

    /**
     * testConstantHasExpectedEndLine
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantHasExpectedEndLine()
    {
        $constant = $this->_getFirstConstantInFunction(__METHOD__);
        $this->assertEquals(4, $constant->getEndLine());
    }

    /**
     * testConstantHasExpectedEndColumn
     *
     * @return void
     * @covers PHP_Depend_Parser
     * @covers PHP_Depend_Builder_Default
     * @covers PHP_Depend_Code_ASTConstant
     * @group pdepend
     * @group pdepend::ast
     * @group unittest
     */
    public function testConstantHasExpectedEndColumn()
    {
        $constant = $this->_getFirstConstantInFunction(__METHOD__);
        $this->assertEquals(14, $constant->getEndColumn());
    }

    /**
     * Returns a node instance for the currently executed test case.
     *
     * @param string $testCase Name of the calling test case.
     *
     * @return PHP_Depend_Code_ASTConstant
     */
    private function _getFirstConstantInFunction($testCase)
    {
        return $this->getFirstNodeOfTypeInFunction(
            $testCase, PHP_Depend_Code_ASTConstant::CLAZZ
        );
    }
}