<?php
/**
 * Issue #1081.
 */

/**
 * Return description function + mixed return types.
 *
 * @return bool|int This is a description.
 */
function returnTypeWithDescriptionA()
{
    return 5;

}//end returnTypeWithDescriptionA()


/**
 * Return description function + mixed return types.
 *
 * @return real|bool This is a description.
 */
function returnTypeWithDescriptionB()
{
    return 5;

}//end returnTypeWithDescriptionB()


/**
 * Return description function + lots of different mixed return types.
 *
 * @return int|object|string[]|real|double|float|bool|array(int=>MyClass)|callable And here we have a description
 */
function returnTypeWithDescriptionC()
{
    return 5;

}//end returnTypeWithDescriptionC()


/**
 * Return description function + lots of different mixed return types.
 *
 * @return array(int=>bool)|\OtherVendor\Package\SomeClass2|MyClass[]|void And here we have a description
 */
function returnTypeWithDescriptionD()
{

}//end returnTypeWithDescriptionD()
