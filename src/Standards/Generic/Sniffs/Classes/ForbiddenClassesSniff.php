<?php
/**
 * Discourages the use of certain classes.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Classes;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ForbiddenClassesSniff implements Sniff
{


    /**
     * Tokens from namespaces and class paths
     *
     * @var array
     */
    private static $namespaceTokens = array(
                                       T_NS_SEPARATOR,
                                       T_STRING,
                                      );

    /**
     * List of native type hints to be excluded when resolving the fully qualified
     * class name
     *
     * @var array
     */
    private static $nativeTypeHints = array(
                                       'void',
                                       'self',
                                       'array',
                                       'callable',
                                       'bool',
                                       'float',
                                       'int',
                                       'string',
                                      );

    /**
     * Keep track of the current namespace
     *
     * @var string
     */
    private $currentNamespace;

    /**
     * Use statements in the current namespace
     *
     * @var array
     */
    private $useStatements = array();

    /**
     * Configurable list of forbidden classes and the alternatives to be used
     *
     * @var array
     */
    public $forbiddenClasses = array('Foo\Bar\ForbiddenClass' => 'Foo\Bar\AlternativeClass');

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var boolean
     */
    public $error = true;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
                T_NAMESPACE,
                T_USE,
                T_NEW,
                T_DOUBLE_COLON,
                T_FUNCTION,
                T_CLOSURE,
               );

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] === T_NAMESPACE) {
            $this->currentNamespace = $this->getNextContent($tokens, ($stackPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));
            $this->useStatements    = array();
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_USE) {
            $useNamespace = $this->getNextContent($tokens, ($stackPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));

            // Check if there is an alias defined for that use statement.
            $aliasTokenPtr = $phpcsFile->findNext(array_merge(self::$namespaceTokens, array(T_WHITESPACE)), ($stackPtr + 1), null, true);
            if ($aliasTokenPtr !== false && $tokens[$aliasTokenPtr]['code'] === T_AS) {
                $alias = $this->getNextContent($tokens, ($aliasTokenPtr + 1), array(T_STRING), array(T_WHITESPACE));
            } else {
                $alias            = $useNamespace;
                $lastBackslashPos = strrpos($useNamespace, '\\');
                if ($lastBackslashPos !== false) {
                    // Take the alias from the class path.
                    $alias = substr($useNamespace, ($lastBackslashPos + 1));
                }
            }

            $this->useStatements[$alias] = $useNamespace;
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_NEW) {
            $className = $this->getNextContent($tokens, ($stackPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));
            $fullyQualifiedClassName = $this->getFullyQualifiedClassName($className);
            $this->checkClassName($phpcsFile, $fullyQualifiedClassName, ($stackPtr + 1));
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_DOUBLE_COLON) {
            $className = $this->getPrevContent($tokens, ($stackPtr - 1), self::$namespaceTokens, array(T_WHITESPACE));
            $fullyQualifiedClassName = $this->getFullyQualifiedClassName($className);
            $this->checkClassName($phpcsFile, $fullyQualifiedClassName, ($stackPtr - 1));
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_FUNCTION || $tokens[$stackPtr]['code'] === T_CLOSURE) {
            $ignoreTokens = Tokens::$emptyTokens;
            // Call by reference.
            $ignoreTokens[] = T_BITWISE_AND;
            $ignoreTokens[] = T_STRING;

            $openBracket = $phpcsFile->findNext($ignoreTokens, ($stackPtr + 1), null, true);
            if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
                return;
            }

            if (isset($tokens[$openBracket]['parenthesis_closer']) === false) {
                return;
            }

            $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
            for ($i = ($openBracket + 1); $i <= $closeBracket; $i = ($variablePtr + 1)) {
                $variablePtr = $phpcsFile->findNext(T_VARIABLE, $i);
                if ($variablePtr === false || $variablePtr > $closeBracket) {
                    break;
                }

                $typeHint = $this->getPrevContent($tokens, ($variablePtr - 1), self::$namespaceTokens, array(T_WHITESPACE, T_BITWISE_AND));
                if (strlen($typeHint) > 0) {
                    $fullyQualifiedClassName = $this->getFullyQualifiedClassName($typeHint);
                    $this->checkClassName($phpcsFile, $fullyQualifiedClassName, ($stackPtr - 1));
                }
            }

            return;
        }//end if

    }//end process()


    /**
     * Get string of allowed tokens next from a certain position
     *
     * @param array $tokens        The token stream.
     * @param int   $startPtr      The start position in the token stream.
     * @param array $allowedTokens The allowed tokens to retrieve content from.
     * @param array $skipTokens    Tokens to be skipped at the beginning.
     *
     * @return string
     */
    private function getPrevContent($tokens, $startPtr, $allowedTokens, $skipTokens)
    {
        $i = $startPtr;
        for (; $i >= 0; $i--) {
            if (in_array($tokens[$i]['code'], $skipTokens) === false) {
                break;
            }
        }

        $string = '';
        for (; $i >= 0; $i--) {
            if (in_array($tokens[$i]['code'], $allowedTokens) === true) {
                $string = $tokens[$i]['content'].$string;
            } else {
                break;
            }
        }

        return $string;

    }//end getPrevContent()


    /**
     * Get string of allowed tokens previous from a certain position
     *
     * @param array $tokens        The token stream.
     * @param int   $startPtr      The start position in the token stream.
     * @param array $allowedTokens The allowed tokens to retrieve content from.
     * @param array $skipTokens    Tokens to be skipped at the beginning.
     *
     * @return string
     */
    private function getNextContent($tokens, $startPtr, $allowedTokens, $skipTokens)
    {
        $numTokens = count($tokens);
        $i         = $startPtr;
        for (; $i < $numTokens; $i++) {
            if (in_array($tokens[$i]['code'], $skipTokens) === false) {
                break;
            }
        }

        $string = '';
        for (; $i < $numTokens; $i++) {
            if (in_array($tokens[$i]['code'], $allowedTokens) === true) {
                $string .= $tokens[$i]['content'];
            } else {
                break;
            }
        }

        return $string;

    }//end getNextContent()


    /**
     * Get the fully qualified class name
     *
     * @param string $className The class name to resolve fully qualified class name.
     *
     * @return string
     */
    private function getFullyQualifiedClassName($className)
    {
        if (in_array($className, self::$nativeTypeHints) === true) {
            return $className;
        }

        if (isset($className[0]) === true && $className[0] === '\\') {
            return substr($className, 1);
        }

        $nsSeparatorPos = strpos($className, '\\');
        if ($nsSeparatorPos === false) {
            if (isset($this->useStatements[$className]) === true) {
                return $this->useStatements[$className];
            } else {
                return $this->currentNamespace.'\\'.$className;
            }
        }

        $nsFirstPart      = substr($className, 0, $nsSeparatorPos);
        $nsRemainingParts = substr($className, ($nsSeparatorPos + 1));
        if (isset($this->useStatements[$nsFirstPart]) === true) {
            $classPath = $this->useStatements[$nsFirstPart];
            if (strlen($nsRemainingParts) > 0) {
                $classPath .= '\\'.$nsRemainingParts;
            }

            return $classPath;
        } else {
            return $this->currentNamespace.'\\'.$className;
        }

    }//end getFullyQualifiedClassName()


    /**
     * Check if a class name is forbidden and add an error
     *
     * @param File   $phpcsFile The file being scanned.
     * @param string $className The class name to check if forbidden.
     * @param int    $stackPtr  The position of the forbidden className in the token array.
     *
     * @return void
     */
    private function checkClassName(File $phpcsFile, $className, $stackPtr)
    {
        if (isset($this->forbiddenClasses[$className]) === true) {
            $this->addError($phpcsFile, $stackPtr, $className);
        }

    }//end checkClassName()


    /**
     * Generates the error or warning for this sniff.
     *
     * @param File   $phpcsFile The file being scanned.
     * @param int    $stackPtr  The position of the forbidden className in the token array.
     * @param string $className The name of the forbidden class.
     *
     * @return void
     */
    protected function addError($phpcsFile, $stackPtr, $className)
    {
        $data  = array($className);
        $error = 'The use of className %s() is ';
        if ($this->error === true) {
            $type   = 'Found';
            $error .= 'forbidden';
        } else {
            $type   = 'Discouraged';
            $error .= 'discouraged';
        }

        if ($this->forbiddenClasses[$className] !== null
            && $this->forbiddenClasses[$className] !== 'null'
        ) {
            $type  .= 'WithAlternative';
            $data[] = $this->forbiddenClasses[$className];
            $error .= '; use %s() instead';
        }

        if ($this->error === true) {
            $phpcsFile->addError($error, $stackPtr, $type, $data);
        } else {
            $phpcsFile->addWarning($error, $stackPtr, $type, $data);
        }

    }//end addError()


}//end class
