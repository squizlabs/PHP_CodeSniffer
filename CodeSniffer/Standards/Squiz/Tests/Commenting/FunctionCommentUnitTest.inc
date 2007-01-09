<?php
class PHP_CodeSniffer_File
{

    /**
     * A simple function comment.
     *
     * long desc here
     *
     * @param bool   $stackPtr   The position in @ @unknown the stack of the token
     *                             that opened the scope
     * @param int    $detph    How many scope levels down we are.
     * @param string    $index    The index
     * @return
     * @throws
     */
    private function _functionCall($stackPtr, $depth=1, $index)
    {
        return $stackPtr;

    }//end _functionCall()

    //
    // Sample function comment
    //
    //
    //
    public function invalidCommentStyle()
    {

    }//end invalidCommentStyle()


    /**
     *
     * A simple function comment
     * Span multiple line
     *
     *
     * Long description with extra blank line before and after
     *
     *
     * @return void
     */
    public function extraDescriptionNewlines()
    {

    }//end extraDescriptionNewlines()


    /**
     * A simple function comment.
     * @return void
     */
    public function missingNewlinesBeforeTags()
    {

    }//end missingNewlinesBeforeTags()


    /**
     * Access tag should not be treated as a long description.
     *
     * @access public
     * @return void
     */
    public function accessTag()
    {

    }//end accessTag()

    /**
     * Constructor.
     *
     * No return tag
     */
    function PHP_CodeSniffer_File()
    {
        return;
    }


    /**
     * Destructor.
     *
     * No return tag too
     */
    function _PHP_CodeSniffer_File()
    {
        return;
    }


    /**
     * Destructor PHP5.
     */
    function __destruct()
    {
        return;
    }


    function missingComment()
    {
        return;
    }


    /**
     * no return tag.
     *
     */
    public function noReturn($one)
    {

    }//end noReturn()


    /**
     * Param not immediate.
     *
     * @return
     * @param   int   $threeSpaces
     * @param integer $superfluous
     * @param miss
     * @param
     */
    public function missingDescroption($threeSpaces)
    {

    }//end missingDescroption()


    /**
     * Param not immediate.
     *
     * @param integer  $one    comment
     * @param array    $two    comment
     * @param MyClas   $three  comment
     *
     * @since
     * @return void
     */
    public function oneSpaceAfterLongestVar($one, $two, MyClass $three)
    {

    }//end oneSpaceAfterLongestVar()


}//end class


/**
 * A simple function comment.
 *
 * @param string $str The string passed in by reference
 *
 * @return void
 * @return
 * @since   %release_version%
 */
public function functionOutsideClass(&$str)
{
    return;
}//end functionOutsideClass()


function missingCommentOutsideClass()
{
    return;
}//end missingCommentOutsideClass()


?><?php
function tagBeforeComment()
{
    return;
}//end tagBeforeComment()


/**
 * no return tag.
 *
 * @see    FunctionCommentSniff.php
 * @since  unknown.release.date
 */
public function noReturnOutsideClass()
{

}//end noReturnOutsideClass()


/**
 * Missing param comment.
 *
 * @param integer $one comment
 *
 * @since  4.0.12
 * @since  4.0.12-rc1
 * @see     wrong indent
 * @see
 * @return void
 * @extra  Invalid tag
 * @throws UnknownException unknown
 */
public function missingTwoParamComment($one, $two, $three)
{

}//end missingTwoParamComment()


/**
 * Missing return type.
 *
 * @since  4.0.12
 * @throws ExceptionWithNoComment
 * @return
 */
public function missingReturnType()
{

}//end missingReturnType()


?>
