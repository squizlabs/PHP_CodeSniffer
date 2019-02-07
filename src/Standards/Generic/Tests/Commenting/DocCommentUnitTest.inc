<?php
/**
 * Short description.
 *
 * Long description
 * over multiple lines.
 *
 * @tag1 one
 * @tag2 two
 * @tag3 three
 */

/**
 * short description
 *
 * long description
 * over multiple lines.
 * @tag1 one
 */

/**
 *
 * Short description
 *
 *
 * Long description
 * over multiple lines
 *
 *
 * @tag1 one
 *
 */

/*
    This is not a doc block.
 */

/** Short description.
 *
 * @tag one
 * @tag2 two
 * @tagThree three
 * @tagFour  four
 */

/**
 * Short description.
 *
 * @tag one
 *
 * @param
 * @param
 *
 */

/**
 * Short description.
 * @param
 * @param
 * @tag one
 */

/**
 * Short description.
 *
 *
 * @param
 *
 * @param
 *
 *
 * @tag one
 */

/**
 * Short description.
 *
 * @param
 *
 * @tag one
 * @param
 */

/**
 * Short description.
 *
 * @groupOne one
 * @groupOne  two
 *
 * @group2 one
 * @group2 two
 *
 *
 * @g3
 * @g3  two
 */

/**
 * Short description
 * over multiple lines.
 *
 * Long description.
 *
 * @param
 *
 * @tag one
 */

/**
 * Short description.
 *
 * @tag1 one   some comment across
 *             multiple lines
 * @tag1 two   some comment across
 *             multiple lines
 * @tag1 three some comment across
 *             multiple lines
 */

/**
 * Returns true if the specified string is in the camel caps format.
 *
 * @param boolean $classFormat If true, check to see if the string is in the
 *                             class format. Class format strings must start
 *                             with a capital letter and contain no
 *                             underscores.
 * @param boolean $strict      If true, the string must not have two capital
 *                             letters next to each other. If false, a
 *                             relaxed camel caps policy is used to allow
 *                             for acronyms.
 *
 * @return boolean
 */

/**
 * Verifies that a @throws tag exists for a function that throws exceptions.
 * Verifies the number of @throws tags and the number of throw tokens matches.
 * Verifies the exception type.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Comment
 *
 * @one
 * @two
 * @one
 *
 * @two   something
 *        here
 * @two   foo
 * @three something
 *        here
 * @three bar
 */

/**
 * @ var Comment
 */

/** @var Database $mockedDatabase */
/** @var Container $mockedContainer */

/**
 * 这是一条测试评论.
 */

/**
 * I'm a function short-description
 * @return boolean
 */

/**
 * this is a test
 * @author test
 * @param boolean $foo blah
 * @return boolean
 * @param boolean $bar Blah.
 */

/**
 * Short description.
 *
 * @tag    one
 * @param  int    $number
 * @param  string $text
 * @return something
 */

/**
 *
 * @param  int    $number
 * @param  string $text
 * @return something
 */

/**
 * @param int $number
 */

/**
 * étude des ...
 */

/**doc comment */

        /**
         * Document behaviour with missing blank lines with indented docblocks.
         * @param
         * @param
         * @tag one
         *
         */

        /** Indented doc comment */

/**
 * Verify and document sniff behaviour when the "tag value" is indented with a mix of tabs and spaces.
 * The below is "correctly" aligned.
 *
 * @category  PHP
 * @package	  PHP_CodeSniffer
 * @author	  Greg Sherwood <gsherwood@squiz.net>
 * @author	  Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license	  https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link	  http://pear.php.net/package/PHP_CodeSniffer
 */

/**
 * Verify and document sniff behaviour when the "tag value" is indented with a mix of tabs and spaces.
 * The below is incorrectly aligned.
 *
 * @category		PHP
 * @package		  PHP_CodeSniffer
 * @author	  Greg Sherwood <gsherwood@squiz.net>
 * @author	Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006-2012 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license	  	https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 * @link		http://pear.php.net/package/PHP_CodeSniffer
 */

/** No docblock close tag. Must be last test without new line.