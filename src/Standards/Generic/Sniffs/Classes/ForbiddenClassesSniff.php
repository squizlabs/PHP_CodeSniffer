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
     * Tokens from PHP7 return types
     *
     * @var array
     */
    private static $returnTypeTokens = array(
                                        T_NS_SEPARATOR,
                                        T_STRING,
                                        T_RETURN_TYPE,
                                       );

    /**
     * List of native type hints to be excluded when resolving the fully qualified class name
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
     * List of PHPDoc tags to check for forbidden classes
     *
     * @var array
     */
    private static $phpDocTags = array(
                                  '@var',
                                  '@param',
                                  '@property',
                                  '@return',
                                 );

    /**
     * List of native types used in PHPDoc
     *
     * @var array
     */
    private static $phpDocNativeTypes = array(
                                         'string',
                                         'integer',
                                         'int',
                                         'boolean',
                                         'bool',
                                         'float',
                                         'double',
                                         'object',
                                         'mixed',
                                         'array',
                                         'resource',
                                         'void',
                                         'null',
                                         'callback',
                                         'false',
                                         'true',
                                         'self',
                                         'static',
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
     * When in a class, pointer position where the class ends
     *
     * @var integer
     */
    private $inClassUntil = -1;

    /**
     * Configurable list of forbidden classes and the alternatives to be used
     *
     * @var array
     */
    public $forbiddenClasses = array('Foo\Bar\ForbiddenClass' => 'Foo\Bar\AlternativeClass');

    /**
     * The usages that are forbidden
     *
     * @var array
     */
    public $usages = array(
                      'extends',
                      'implements',
                      'new',
                      'static-call',
                      'trait-use',
                      'type-hint',
                      'phpdoc',
                     );

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
        $tokens = array(
                   T_NAMESPACE,
                   T_USE,
                   T_CLASS,
                  );

        if (in_array('extends', $this->usages) === true) {
            $tokens[] = T_EXTENDS;
        }

        if (in_array('implements', $this->usages) === true) {
            $tokens[] = T_IMPLEMENTS;
        }

        if (in_array('new', $this->usages) === true) {
            $tokens[] = T_NEW;
        }

        if (in_array('static-call', $this->usages) === true) {
            $tokens[] = T_DOUBLE_COLON;
        }

        if (in_array('type-hint', $this->usages) === true) {
            $tokens[] = T_FUNCTION;
            $tokens[] = T_CLOSURE;
        }

        if (in_array('phpdoc', $this->usages) === true) {
            $tokens[] = T_DOC_COMMENT_TAG;
        }

        return $tokens;

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
            list($this->currentNamespace) = $this->getNextContent($tokens, ($stackPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));
            $this->useStatements          = array();
            return;
        }

        if ($tokens[$stackPtr]['code'] === T_USE) {
            $notInClass = $stackPtr > $this->inClassUntil;
            if ($notInClass === true) {
                // We're outside of a class definition. Use statements are class imports.
                $useSemiColonPtr = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
                $useStartPtr     = $stackPtr;
                do {
                    list($useNamespace) = $this->getNextContent($tokens, ($useStartPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));

                    // Check if there is an alias defined for that use statement.
                    $aliasTokenPtr = $phpcsFile->findNext(array_merge(self::$namespaceTokens, array(T_WHITESPACE)), ($useStartPtr + 1), null, true);
                    if ($aliasTokenPtr !== false && $tokens[$aliasTokenPtr]['code'] === T_AS) {
                        list($alias) = $this->getNextContent($tokens, ($aliasTokenPtr + 1), array(T_STRING), array(T_WHITESPACE));
                    } else {
                        $alias            = $useNamespace;
                        $lastBackslashPos = strrpos($useNamespace, '\\');
                        if ($lastBackslashPos !== false) {
                            // Take the alias from the class path.
                            $alias = substr($useNamespace, ($lastBackslashPos + 1));
                        }
                    }

                    $this->useStatements[$alias] = $useNamespace;

                    // Find start position of the next import statement.
                    $useStartPtr = $phpcsFile->findNext(T_COMMA, ($useStartPtr + 1));
                } while ($useStartPtr !== false && $useStartPtr < $useSemiColonPtr);

                return;
            }//end if

            if (in_array('trait-use', $this->usages) === true) {
                // We're in a class definition. Use statements are trait imports.
                $useSemiColonPtr = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
                $useStartPtr     = $stackPtr;
                do {
                    list($traitClass, $traitClassPtr) = $this->getNextContent($tokens, ($useStartPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));
                    $fullyQualifiedClassName          = $this->getFullyQualifiedClassName($traitClass);
                    $this->checkClassName($phpcsFile, $fullyQualifiedClassName, $traitClassPtr);

                    // Find start position of the next trait import statement.
                    $useStartPtr = $phpcsFile->findNext(T_COMMA, ($useStartPtr + 1));
                } while ($useStartPtr !== false && $useStartPtr < $useSemiColonPtr);

                return;
            }
        }//end if

        // Detect if we're in a class definition. Then, use statements have to be interpreted as Trait imports.
        if ($tokens[$stackPtr]['code'] === T_CLASS) {
            $this->inClassUntil = $tokens[$stackPtr]['scope_closer'];
            return;
        }

        if (in_array('new', $this->usages) === true && $tokens[$stackPtr]['code'] === T_NEW) {
            list($className, $classNamePtr) = $this->getNextContent($tokens, ($stackPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));
            $fullyQualifiedClassName        = $this->getFullyQualifiedClassName($className);
            $this->checkClassName($phpcsFile, $fullyQualifiedClassName, $classNamePtr);
            return;
        }

        if (in_array('extends', $this->usages) === true && $tokens[$stackPtr]['code'] === T_EXTENDS) {
            list($className, $classNamePtr) = $this->getNextContent($tokens, ($stackPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));
            $fullyQualifiedClassName        = $this->getFullyQualifiedClassName($className);
            $this->checkClassName($phpcsFile, $fullyQualifiedClassName, $classNamePtr);
            return;
        }

        if (in_array('implements', $this->usages) === true && $tokens[$stackPtr]['code'] === T_IMPLEMENTS) {
            $implementsEndPtr   = $phpcsFile->findNext(T_OPEN_CURLY_BRACKET, ($stackPtr + 1));
            $implementsStartPtr = $stackPtr;
            do {
                list($implementsClass, $implementsClassPtr) = $this->getNextContent($tokens, ($implementsStartPtr + 1), self::$namespaceTokens, array(T_WHITESPACE));
                $fullyQualifiedClassName = $this->getFullyQualifiedClassName($implementsClass);
                $this->checkClassName($phpcsFile, $fullyQualifiedClassName, $implementsClassPtr);

                // Find start position of the next trait-use statement.
                $implementsStartPtr = $phpcsFile->findNext(T_COMMA, ($implementsStartPtr + 1));
            } while ($implementsStartPtr !== false && $implementsStartPtr < $implementsEndPtr);

            return;
        }

        if (in_array('static-call', $this->usages) === true && $tokens[$stackPtr]['code'] === T_DOUBLE_COLON) {
            list($className, $classNamePtr) = $this->getPrevContent($tokens, ($stackPtr - 1), self::$namespaceTokens, array(T_WHITESPACE));
            $fullyQualifiedClassName        = $this->getFullyQualifiedClassName($className);
            $this->checkClassName($phpcsFile, $fullyQualifiedClassName, $classNamePtr);
            return;
        }

        if (in_array('type-hint', $this->usages) === true && $tokens[$stackPtr]['code'] === T_FUNCTION || $tokens[$stackPtr]['code'] === T_CLOSURE) {
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
            for ($i = ($openBracket + 1); $i <= $closeBracket; $i = ($endOfTypeHintPtr + 1)) {
                $endOfTypeHintPtr = $phpcsFile->findNext(T_VARIABLE, $i);
                if ($endOfTypeHintPtr === false || $endOfTypeHintPtr > $closeBracket) {
                    break;
                }

                list($typeHint, $typeHintPtr) = $this->getPrevContent($tokens, ($endOfTypeHintPtr - 1), self::$namespaceTokens, array(T_WHITESPACE, T_BITWISE_AND));
                if (strlen($typeHint) > 0) {
                    $fullyQualifiedClassName = $this->getFullyQualifiedClassName($typeHint);
                    $this->checkClassName($phpcsFile, $fullyQualifiedClassName, $typeHintPtr);
                }
            }

            // Check for PHP7 return type hint.
            $colonTokenPtr = $phpcsFile->findNext(array_merge(self::$namespaceTokens, array(T_WHITESPACE)), ($closeBracket + 1), null, true);
            if ($colonTokenPtr !== false && $tokens[$colonTokenPtr]['code'] === T_COLON) {
                list($returnType, $returnTypePtr) = $this->getNextContent($tokens, ($colonTokenPtr + 1), self::$returnTypeTokens, array(T_WHITESPACE));
                $fullyQualifiedClassName          = $this->getFullyQualifiedClassName($returnType);
                $this->checkClassName($phpcsFile, $fullyQualifiedClassName, $returnTypePtr);
            }

            return;
        }//end if

        if (in_array('phpdoc', $this->usages) === true && $tokens[$stackPtr]['code'] === T_DOC_COMMENT_TAG) {
            if (in_array($tokens[$stackPtr]['content'], self::$phpDocTags) !== true) {
                return;
            }

            $phpDocStrPtr = ($stackPtr + 2);
            if ($tokens[$phpDocStrPtr]['code'] === T_DOC_COMMENT_STRING) {
                preg_match('/^([^$&.\s]+)/', $tokens[$phpDocStrPtr]['content'], $matches);
                if (isset($matches[1]) === true) {
                    $phpDocTypes = explode('|', $matches[1]);
                    foreach ($phpDocTypes as $phpDocType) {
                        if (in_array($phpDocType, self::$phpDocNativeTypes) === true) {
                            // Do not check native PHPDoc types.
                            continue;
                        }

                        if (substr($phpDocType, -2) === '[]') {
                            // Get type from array.
                            $phpDocType = substr($phpDocType, 0, (strlen($phpDocType) - 2));
                        }

                        $fullyQualifiedClassName = $this->getFullyQualifiedClassName($phpDocType);
                        $this->checkClassName($phpcsFile, $fullyQualifiedClassName, $phpDocStrPtr);
                    }
                }
            }//end if

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
     * @return array
     */
    private function getPrevContent($tokens, $startPtr, $allowedTokens, $skipTokens)
    {
        $i = $startPtr;
        for (; $i >= 0; $i--) {
            if (in_array($tokens[$i]['code'], $skipTokens) === false) {
                break;
            }
        }

        $stringStartPtr = $i;
        $string         = '';
        for (; $i >= 0; $i--) {
            if (in_array($tokens[$i]['code'], $allowedTokens) === true) {
                $string         = $tokens[$i]['content'].$string;
                $stringStartPtr = $i;
            } else {
                break;
            }
        }

        return array(
                $string,
                $stringStartPtr,
               );

    }//end getPrevContent()


    /**
     * Get string of allowed tokens previous from a certain position
     *
     * @param array $tokens        The token stream.
     * @param int   $startPtr      The start position in the token stream.
     * @param array $allowedTokens The allowed tokens to retrieve content from.
     * @param array $skipTokens    Tokens to be skipped at the beginning.
     *
     * @return array
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

        $stringStartPtr = $i;
        $string         = '';
        for (; $i < $numTokens; $i++) {
            if (in_array($tokens[$i]['code'], $allowedTokens) === true) {
                $string .= $tokens[$i]['content'];
            } else {
                break;
            }
        }

        return array(
                $string,
                $stringStartPtr,
               );

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
