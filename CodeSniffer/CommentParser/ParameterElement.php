<?php
/**
 * A class to represent param tags within a function comment.
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

require_once 'PHP/CodeSniffer/CommentParser/AbstractDocElement.php';

/**
 * A class to represent param tags within a function comment.
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class PHP_CodeSniffer_CommentParser_ParameterElement extends PHP_CodeSniffer_CommentParser_AbstractDocElement
{

    /**
     * The variable name of this parameter name, including the $ sign.
     *
     * @var string
     */
    private $_varName = '';

    /**
     * The comment of this parameter tag.
     *
     * @var string
     */
    private $_comment = '';

    /**
     * The variable type of this parameter tag.
     *
     * @var string
     */
    private $_type = '';

    /**
     * The whitespace that exists before the variable name.
     *
     * @var string
     */
    private $_varNameWhitespace = '';

    /**
     * The whitespace that exists before the comment.
     *
     * @var string
     */
    private $_commentWhitespace = null;

    /**
     * The whitespace that exists before the variable type.
     *
     * @var string
     */
    private $_typeWhitespace = '';


    /**
     * Constructs a PHP_CodeSniffer_CommentParser_ParameterElement.
     *
     * @param PHP_CodeSniffer_CommentParser_DocElement $previousElement The element previous to this one.
     * @param array                                    $tokens          The tokens that make up this element.
     */
    public function __construct($previousElement, $tokens)
    {
        parent::__construct($previousElement, $tokens, 'param');

    }//end __construct()


    /**
     * Returns the element names that this tag is comprised of, in the order
     * that they appear in the tag.
     *
     * @return array(string)
     * @see processSubElement()
     */
    protected function getSubElements()
    {
        return array(
                'type',
                'varName',
                'comment',
               );

    }//end getSubElements()


    /**
     * Processes the sub element with the specified name.
     *
     * @param string $name             The name of the sub element to process.
     * @param string $content          The content of this sub element.
     * @param string $beforeWhitespace The whitespace that exists before the
     *                                 sub element.
     *
     * @return void
     * @see getSubElements()
     */
    protected function processSubElement($name, $content, $beforeWhitespace)
    {
        $element           = '_'.$name;
        $whitespace        = $element.'Whitespace';
        $this->$element    = $content;
        $this->$whitespace = $beforeWhitespace;

    }//end processSubElement()


    /**
     * Returns the variable name that this parameter tag represents.
     *
     * @return string
     */
    public function getVarName()
    {
        return $this->_varName;

    }//end getVarName()


    /**
     * Returns the variable type that this string represents.
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;

    }//end getType()


    /**
     * Returns the comment of this comment for this parameter.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->_comment;

    }//end getComment()


    /**
     * Returns the whitespace before the variable type.
     *
     * @return stirng
     * @see getWhiteSpaceBeforeVarName()
     * @see getWhiteSpaceBeforeComment()
     */
    public function getWhiteSpaceBeforeType()
    {
        return $this->_typeWhitespace;

    }//end getWhiteSpaceBeforeType()


    /**
     * Returns the whitespace before the variable name.
     *
     * @return string
     * @see getWhiteSpaceBeforeComment()
     * @see getWhiteSpaceBeforeType()
     */
    public function getWhiteSpaceBeforeVarName()
    {
        return $this->_varNameWhitespace;

    }//end getWhiteSpaceBeforeVarName()


    /**
     * Returns the whitespace before the comment.
     *
     * @return string
     * @see getWhiteSpaceBeforeVarName()
     * @see getWhiteSpaceBeforeType()
     */
    public function getWhiteSpaceBeforeComment()
    {
        return $this->_commentWhitespace;

    }//end getWhiteSpaceBeforeComment()


    /**
     * Returns the postition of this parameter are it appears in the comment.
     *
     * This method differs from getOrder as it is only relative to method
     * parameters.
     *
     * @return int
     */
    public function getPosition()
    {
        if (($this->getPreviousElement() instanceof PHP_CodeSniffer_CommentParser_ParameterElement) === false) {
            return 1;
        } else {
            return $this->getPreviousElement()->getPosition() + 1;
        }

    }//end getPosition()


    /**
     * Returns true if this parameter aligns with the other parameter.
     *
     * @param PHP_CodeSniffer_CommentParser_ParameterElement $other The other parameter to check alignment
     *                                                              with.
     *
     * @return boolean
     */
    public function alignsWith(PHP_CodeSniffer_CommentParser_ParameterElement $other)
    {
        $otherLength  = strlen($other->_typeWhitespace) + strlen($other->_type);
        $otherLength += strlen($other->_varNameWhitespace) + strlen($other->_varName);
        $otherLength += strlen($other->_commentWhitespace);

        $myLength  = strlen($this->_typeWhitespace) + strlen($this->_type);
        $myLength += strlen($this->_varNameWhitespace) + strlen($this->_varName);
        $myLength += strlen($this->_commentWhitespace);

        return ($myLength === $otherLength);

    }//end alignsWith()


}//end class


?>
