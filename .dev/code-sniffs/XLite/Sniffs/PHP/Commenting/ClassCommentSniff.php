<?php
/**
 * Parses and verifies the doc comments for classes.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('PHP_CodeSniffer_CommentParser_ClassCommentParser', true) === false) {
    $error = 'Class PHP_CodeSniffer_CommentParser_ClassCommentParser not found';
    throw new PHP_CodeSniffer_Exception($error);
}

if (class_exists('XLite_Sniffs_PHP_Commenting_FileCommentSniff', true) === false) {
    $error = 'Class XLite_Sniffs_PHP_Commenting_FileCommentSniff not found';
    throw new PHP_CodeSniffer_Exception($error);
}

/**
 * Parses and verifies the doc comments for classes.
 *
 * Verifies that :
 * <ul>
 *  <li>A doc comment exists.</li>
 *  <li>There is a blank newline after the short description.</li>
 *  <li>There is a blank newline between the long and short description.</li>
 *  <li>There is a blank newline between the long description and tags.</li>
 *  <li>Check the order of the tags.</li>
 *  <li>Check the indentation of each tag.</li>
 *  <li>Check required and optional tags and the format of their content.</li>
 * </ul>
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class XLite_Sniffs_PHP_Commenting_ClassCommentSniff extends XLite_Sniffs_PHP_Commenting_FileCommentSniff
{

    protected $tags = array(
		'see'        => array(
        	'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @link',
        ),
        'since'      => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @see (if used) or @link',
        ),
        'deprecated' => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @since (if used) or @see (if used) or @link',
        ),
        'Entity' => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @since (if used) or @see (if used) or @link or @deprecated (if used)',
        ),
        'Table' => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @Entity',
        ),
        'Index'      => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @Table',
		),
        'UniqueConstraint'      => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @Table',
        ),
        'HasLifecycleCallbacks' => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @Table',
        ),
        'InheritanceType' => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @Table',
        ),
        'DiscriminatorColumn' => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @Table',
        ),
        'DiscriminatorMap' => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @Table',
        ),
        'MappedSuperclass' => array(
            'required'       => false,
            'allow_multiple' => false,
            'order_text'     => 'follows @Table',
        ),
        'ListChild' => array(
            'required'       => false,
            'allow_multiple' => true,
            'order_text'     => 'any place',
        ),
	);

    protected $reqCodeRequire = 'REQ.PHP.4.4.3';
    protected $reqCodePHPVersion = false;
    protected $reqCodeForbidden = 'REQ.PHP.4.4.7';
    protected $reqCodeOnlyOne = 'REQ.PHP.4.4.6';

    protected $docBlock = 'class';


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_CLASS,
                T_INTERFACE,
               );

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $this->currentFile = $phpcsFile;

        $tokens = $phpcsFile->getTokens();
        $type   = strtolower($tokens[$stackPtr]['content']);
        $find   = array(
                   T_ABSTRACT,
                   T_WHITESPACE,
                   T_FINAL,
                  );

        // Extract the class comment docblock.
        $commentEnd = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);

        if ($commentEnd !== false && $tokens[$commentEnd]['code'] === T_COMMENT) {
            $error = "You must use \"/**\" style comments for a $type comment";
            $phpcsFile->addError($this->getReqPrefix('REQ.PHP.4.4.5') . $error, $stackPtr);
            return;
        } else if ($commentEnd === false
            || $tokens[$commentEnd]['code'] !== T_DOC_COMMENT
        ) {
            $phpcsFile->addError($this->getReqPrefix('REQ.PHP.4.4.1') . "Missing $type doc comment", $stackPtr);
            return;
        }

        $commentStart = ($phpcsFile->findPrevious(T_DOC_COMMENT, ($commentEnd - 1), null, true) + 1);
        $commentNext  = $phpcsFile->findPrevious(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);

        // Distinguish file and class comment.
        $prevClassToken = $phpcsFile->findPrevious(T_CLASS, ($stackPtr - 1));
        if ($prevClassToken === false) {
            // This is the first class token in this file, need extra checks.
            $prevNonComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($commentStart - 1), null, true);
            if ($prevNonComment !== false) {
                $prevComment = $phpcsFile->findPrevious(T_DOC_COMMENT, ($prevNonComment - 1));
                if ($prevComment === false) {
                    // There is only 1 doc comment between open tag and class token.
                    $newlineToken = $phpcsFile->findNext(T_WHITESPACE, ($commentEnd + 1), $stackPtr, false, $phpcsFile->eolChar);
                    if ($newlineToken !== false) {
                        $newlineToken = $phpcsFile->findNext(
                            T_WHITESPACE,
                            ($newlineToken + 1),
                            $stackPtr,
                            false,
                            $phpcsFile->eolChar
                        );

                        if ($newlineToken !== false) {
                            // Blank line between the class and the doc block.
                            // The doc block is most likely a file comment.
                            $error = "Missing $type doc comment";
                            $phpcsFile->addError($this->getReqPrefix('REQ.PHP.4.1.6') . $error, ($stackPtr + 1));
                            return;
                        }
                    }//end if
                }//end if
            }//end if
        }//end if

        $comment = $phpcsFile->getTokensAsString(
            $commentStart,
            ($commentEnd - $commentStart + 1)
        );

        // Parse the class comment.docblock.
        try {
            $this->commentParser = new PHP_CodeSniffer_CommentParser_ClassCommentParser($comment, $phpcsFile);
            $this->commentParser->parse();
        } catch (PHP_CodeSniffer_CommentParser_ParserException $e) {
            $line = ($e->getLineWithinComment() + $commentStart);
            $phpcsFile->addError($this->getReqPrefix('REQ.PHP.4.1.1') . $e->getMessage(), $line);
            return;
        }

        $comment = $this->commentParser->getComment();
        if (is_null($comment) === true) {
            $error = ucfirst($type).' doc comment is empty';
            $phpcsFile->addError($this->getReqPrefix('REQ.PHP.4.4.2') . $error, $commentStart);
            return;
        }

        // No extra newline before short description.
        $short        = $comment->getShortComment();
        $newlineCount = 0;
        $newlineSpan  = strspn($short, $phpcsFile->eolChar);
        if ($short !== '' && $newlineSpan > 0) {
            $line  = ($newlineSpan > 1) ? 'newlines' : 'newline';
            $error = "Extra $line found before $type comment short description";
            $phpcsFile->addError($this->getReqPrefix('REQ.PHP.4.1.7') . $error, ($commentStart + 1));
        }

        $newlineCount = (substr_count($short, $phpcsFile->eolChar) + 1);

        // Exactly one blank line between short and long description.
        $long = $comment->getLongComment();
        if (empty($long) === false) {
            $between        = $comment->getWhiteSpaceBetween();
            $newlineBetween = substr_count($between, $phpcsFile->eolChar);
            if ($newlineBetween !== 2) {
                $error = "There must be exactly one blank line between descriptions in $type comments";
                $phpcsFile->addError($this->getReqPrefix('REQ.PHP.4.1.18') . $error, ($commentStart + $newlineCount + 1));
            }

            $newlineCount += $newlineBetween;
        }

        // Exactly one blank line before tags.
        $tags = $this->commentParser->getTagOrders();
        if (count($tags) > 1) {
            $newlineSpan = $comment->getNewlineAfter();
            if ($newlineSpan !== 2) {
                $error = "There must be exactly one blank line before the tags in $type comments";
                if ($long !== '') {
                    $newlineCount += (substr_count($long, $phpcsFile->eolChar) - $newlineSpan + 1);
                }

                $phpcsFile->addError($this->getReqPrefix('REQ.PHP.4.1.18') . $error, ($commentStart + $newlineCount));
                $short = rtrim($short, $phpcsFile->eolChar.' ');
            }
        }

        // Check each tag.
        $this->processTags($commentStart, $commentEnd);

    }//end process()

}//end class

?>
