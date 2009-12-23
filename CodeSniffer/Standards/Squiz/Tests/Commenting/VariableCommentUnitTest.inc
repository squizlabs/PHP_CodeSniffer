<?php
class VariableCommentUnitTest
{

    /**
     * short description of the member variable.
     *
     * long description of member variable. Can span over multiple
     * lines and can have multiple paragraphs.
     *
     * @var   array
     * @since %release_version%
     * @see   otherFunctions()
     * @see   anotherFunctions()
     */
     public $variableName = array();


     // Not "/**" style comment.
     //
     // @var   string
     // @since 4.0.0
     private  $_incorrectCommentStyle = '';


     protected $noComment = '';


    /**
     *
     * Extra newline before short comment.
     *
     * @var   string
     * @since %release_version%
     * @since another.since.tag
     */
     public $extraNewlineBeforeShort = '';


    /**
     * Extra newline between short description.
     *
     *
     *
     * - Long description,
     * with wrong tag order.
     *
     * @since %release_version%
     * @var   string
     */
     private $_extraNewlineBetween = '';


    /**
     * Extra newline before tags.
     *
     * Long description,
     * with two var tags.
     *
     *
     * @var   string
     * @var   array
     * @since incorrect.version.number
     */
     protected $extraNewlineBeforeTags = '';


    /**
     * No newline before tags, var tag missing.
     * @since 4.0.12
     * @see   otherFunctions()
     */
     protected $noNewlineBeforeTags = '';


    /**
     * Short comment that spans multiple
     * lines and does not end with a full stop
     * also var type is missing
     *
     * @var
     * @since
     */
     public $singleLineFullStopShortComment = '';


    /**
     * Incorrect tag indentation.
     *
     * @var     string
     * @since    %release_version%
     * @see        otherFunctions()
     * @see anotherFunctions()
     * @see
     */
     public $singleLineFullStopShortComment = '';


    /**
     * Missing since tag
     *
     * @var     string
     */
     public $missingSinceTag = '';


    /**
     * Unknown summary tag
     *
     * @var     string
     * @since   mysource4-was-released
     * @summary unknown tag
     */
     public $missingSinceTag = '';


    /**
     * T_VARIABLE check, without scope.
     *
     * @var   string
     * @since 4.0.0
     */
     $variableCheck = '';


    /**
     * T_VARIABLE check, var in string and in function.
     *
     * @param integer $var1 First variable.
     * @param integer $var2 Second variable.
     *
     * @return integer
     */
    protected function checkVariable($var1, $var2)
    {
        $var4 = 'A normal variable';
        $var5 = PHP_CodeSniffer_Tokens::$operators;
        echo "Printing $var1 $var2 and unknown $var3 in a double quoted  string\n";
        foreach (self::$_array as $index => $content) {
            echo $content;
        }

        return $var1;

    }//end checkVariable()


    /**
     *
     *
     */
     $emptyVarDoc = '';

     /**
     * Var type checking (int v.s. integer).
     *
     * @var   int
     * @since %release_version%
     */
     private $_varSimpleTypeCheck;


     /**
     * Var type checking (array(int => string) v.s. array(int => string)).
     *
     * @var   array(int => string)
     * @since %release_version%
     */
     private $_varArrayTypeCheck;


     /**
     * Var type checking (STRING v.s. string).
     *
     * @var   STRING
     * @since %release_version%
     */
     private $_varCaseTypeCheck;


    /**
     * @var   integer
     * @since %release_version%
     */
     private $_varWithNoShortComment;

     protected $noComment2 = '';


}//end class


/**
 * VariableCommentUnitTest2.
 *
 * Long description goes here.
 *
 * @since 4.0.0
 */
class VariableCommentUnitTest2
{

   public $hello;

    /** Comment starts here.
     *
     * @var   string
     * @since 4.0.0
     */
     private $_varCaseTypeCheck;

}//end class

?>
