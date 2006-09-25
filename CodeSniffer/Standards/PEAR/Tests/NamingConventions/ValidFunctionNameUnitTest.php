<?php
/**
 * +------------------------------------------------------------------------+
 * | BSD Licence                                                            |
 * +------------------------------------------------------------------------+
 * | This software is available to you under the BSD license,               |
 * | available in the LICENSE file accompanying this software.              |
 * | You may obtain a copy of the License at                                |
 * |                                                                        |
 * | http://matrix.squiz.net/developer/tools/php_cs/licence                 |
 * |                                                                        |
 * | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS    |
 * | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT      |
 * | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR  |
 * | A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT   |
 * | OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,  |
 * | SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT       |
 * | LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,  |
 * | DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY  |
 * | THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT    |
 * | (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE  |
 * | OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.   |
 * +------------------------------------------------------------------------+
 * | Copyright (c), 2006 Squiz Pty Ltd (ABN 77 084 670 600).                |
 * | All rights reserved.                                                   |
 * +------------------------------------------------------------------------+
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */

/**
 * Unit test class for the ValidFunctionName sniff.
 *
 * A sniff unit test checks a .inc file for expected violations of a single
 * coding standard. Expected errors and warnings are stored in this class.
 *
 * @package PHP_CodeSniffer
 * @author  Squiz Pty Ltd
 */
class PEAR_Tests_NamingConventions_ValidFunctionNameUnitTest extends AbstractSniffUnitTest
{


    /**
     * Returns the lines where errors should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of errors that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getErrorList()
    {
        return array(
                  10 => 1,
                  11 => 1,
                  12 => 1,
                  13 => 1,
                  14 => 1,
                  15 => 1,
                  16 => 1,
                  17 => 1,
                  18 => 1,
                  19 => 1,
                  23 => 1,
                  24 => 1,
                  25 => 1,
                  26 => 1,
                  27 => 1,
                  28 => 1,
                  29 => 1,
                  30 => 1,
                  31 => 1,
                  32 => 1,
                  34 => 1,
                  35 => 1,
                  36 => 1,
                  37 => 1,
                  38 => 1,
                  39 => 1,
                  42 => 1,
                  43 => 1,
                  44 => 1,
                  45 => 1,
                  49 => 1,
                  50 => 1,
                  51 => 1,
                  52 => 1,
                  55 => 1,
                  56 => 1,
                  57 => 1,
                  58 => 1,
                  66 => 1,
                  67 => 1,
                  68 => 1,
                  69 => 1,
                  70 => 1,
                  71 => 1,
                  72 => 1,
                  73 => 1,
                  74 => 1,
                  75 => 1,
                  79 => 1,
                  80 => 1,
                  81 => 1,
                  82 => 1,
                  83 => 1,
                  84 => 1,
                  85 => 1,
                  86 => 1,
                  87 => 1,
                  88 => 1,
                  90 => 1,
                  91 => 1,
                  92 => 1,
                  93 => 1,
                  94 => 1,
                  95 => 1,
                  98 => 1,
                  99 => 1,
                 100 => 1,
                 101 => 1,
                 105 => 1,
                 106 => 1,
                 107 => 1,
                 108 => 1,
                 111 => 1,
                 112 => 1,
                 113 => 1,
                 114 => 1,
                 120 => 1,
                 121 => 1,
                 122 => 1,
                 123 => 1,
                 124 => 1,
                 125 => 1,
                 126 => 1,
                 127 => 1,
                 128 => 1,
                 129 => 1,
                 148 => 1,
                 149 => 1,
                 152 => 1,
                 153 => 1,
                 154 => 1,
                 155 => 1,
                 156 => 1,
                 157 => 1,
                 158 => 1,
                 159 => 1,
                 160 => 1,
                 161 => 1,
                 162 => 1,
                 163 => 1,
                 164 => 1,
                 165 => 1,
                 166 => 1,
               );

    }//end getErrorList()


    /**
     * Returns the lines where warnings should occur.
     *
     * The key of the array should represent the line number and the value
     * should represent the number of warnings that should occur on that line.
     *
     * @return array(int => int)
     */
    public function getWarningList()
    {
        return array();

    }//end getWarningList()


}//end class

?>
