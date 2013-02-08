<?php
class VariableCommentUnitTest
{

    /**
     * short description of the member variable.
     *
     * long description of member variable. Can span over multiple
     * lines and can have multiple paragraphs.
     *
     * @var array
     * @see otherFunctions()
     * @see anotherFunctions()
     */
     public $variableName = array();


     // Not "/**" style comment.
     //
     // @var   string
     private  $_incorrectCommentStyle = '';


     protected $noComment = '';


    /**
     *
     * Extra newline before short comment.
     *
     * @var string
     */
     public $extraNewlineBeforeShort = '';


    /**
     * Extra newline between short description.
     *
     *
     *
     * - Long description,
     *
     * @var string
     */
     private $_extraNewlineBetween = '';


    /**
     * Extra newline before tags.
     *
     * Long description,
     * with two var tags.
     *
     *
     * @var string
     * @var array
     */
     protected $extraNewlineBeforeTags = '';


    /**
     * No newline before tags, var tag missing.
     * @see otherFunctions()
     */
     protected $noNewlineBeforeTags = '';


    /**
     * Short comment that spans multiple
     * lines and does not end with a full stop
     * also var type is missing
     *
     * @var
     */
     public $singleLineFullStopShortComment = '';


    /**
     * Incorrect tag indentation.
     *
     * @var     string
     * @see        otherFunctions()
     * @see anotherFunctions()
     * @see
     */
     public $singleLineFullStopShortComment = '';


    /**
     * Unknown summary tag
     *
     * @var     string
     * @summary unknown tag
     */
     public $missingSinceTag = '';


    /**
     * T_VARIABLE check, without scope.
     *
     * @var string
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
     * @var int
     */
     private $_varSimpleTypeCheck;


     /**
     * Var type checking (array(int => string) v.s. array(int => string)).
     *
     * @var array(int => string)
     */
     private $_varArrayTypeCheck;


     /**
     * Var type checking (STRING v.s. string).
     *
     * @var STRING
     */
     private $_varCaseTypeCheck;


    /**
     * @var integer
     */
     private $_varWithNoShortComment;

     protected $noComment2 = '';


}//end class


/**
 * VariableCommentUnitTest2.
 *
 * Long description goes here.
 *
 */
class VariableCommentUnitTest2
{

   public $hello;

    /** Comment starts here.
     *
     * @var string
     *
     */
     private $_varCaseTypeCheck;

     /**
      * 这是一条测试评论.
      *
      * @var string
      */
     public $foo;

}//end class

?>
