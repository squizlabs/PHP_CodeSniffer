<?php
class PHP_CodeSniffer_File
{

    /**
     * A simple function comment.
     *
     * long desc here
     *
     * @param int $stackPtr   The position in the stack of the token that opened
     *                        the scope (eg. an IF token or FOR token).
     * @param int $detph    How many scope levels down we are.
     * @param int    $index    The index
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
     *
     * A simple function comment
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
     * A simple function comment
     * @return void
     */
    public function missingNewlinesBeforeTags()
    {

    }//end missingNewlinesBeforeTags()


}//end class


/**
 * A simple function comment
 *
 * @param string $str The string passed in by reference
 *
 * @return void
 */
public function functionOutsideClass(&$str)
{
    return;

}//end functionOutsideClass()

?>
