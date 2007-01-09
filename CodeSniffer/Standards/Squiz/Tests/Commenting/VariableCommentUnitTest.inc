<?php
class VariableCommentUnitTest
{

    /**
     * Short description of the member variable.
     *
     * Long description of member variable. Can span over multiple
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
     * Long description,
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

}//end class
?>
