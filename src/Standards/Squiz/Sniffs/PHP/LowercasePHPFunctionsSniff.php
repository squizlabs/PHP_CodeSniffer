<?php
/**
 * Ensures all calls to inbuilt PHP functions are lowercase.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class LowercasePHPFunctionsSniff implements Sniff
{

    /**
     * String -> int hash map of all php built in function names
     *
     * @var array
     */
    private $builtInFunctions;

    /**
     * Imported function (`use function ... [as ...]`)
     *
     * @var array<string, string>
     */
    private $imported = [];

    /**
     * Currently processed namespace
     *
     * @var string
     */
    private $currentNamespace;

    /**
     * Currently processed file
     *
     * @var string
     */
    private $currentFile;

    /**
     * Functions defined in current namespace
     *
     * @var array<string, string>
     */
    private $functionsInNamespace = [];


    /**
     * Construct the LowercasePHPFunctionSniff
     */
    public function __construct()
    {

        $allFunctions           = get_defined_functions();
        $this->builtInFunctions = array_flip($allFunctions['internal']);

    }//end __construct()


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_STRING];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->currentFile !== $phpcsFile->getFilename()) {
            $this->currentFile      = $phpcsFile->getFilename();
            $this->currentNamespace = null;
            $this->imported         = [];
        }

        $namespace = $this->getNamespace($phpcsFile, $stackPtr);
        if ($this->currentNamespace !== $namespace) {
            $this->imported         = [];
            $this->currentNamespace = $namespace;
            $this->functionsInNamespace = $this->getFunctionsInNamespace($phpcsFile, $stackPtr);
        }

        $tokens = $phpcsFile->getTokens();

        // Make sure this is a function call.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false) {
            // Not a function call.
            return;
        }

        $isUse = false;
        if ($tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            // Not a function call, check if it is import function.
            // If it will be import global function next non-empty token
            // should be semicolon or "as".
            if ($tokens[$next]['code'] !== T_SEMICOLON
                && $tokens[$next]['code'] !== T_AS
            ) {
                return;
            }

            // Check if previous token is T_USE.
            $prev = $phpcsFile->findPrevious(
                (Tokens::$emptyTokens + [T_NS_SEPARATOR => T_NS_SEPARATOR, T_STRING => T_STRING]),
                ($stackPtr - 1),
                null,
                true
            );
            if ($tokens[$prev]['code'] !== T_USE) {
                // It is not import function.
                return;
            }

            // Check if after T_USE we have "function" keyword.
            $afterUse = $phpcsFile->findNext(Tokens::$emptyTokens, ($prev + 1), null, true);
            if ($tokens[$afterUse]['code'] !== T_STRING
                || strtolower($tokens[$afterUse]['content']) !== 'function'
            ) {
                // It is not import function.
                return;
            }

            $firstString = $phpcsFile->findNext(T_STRING, ($afterUse + 1));
            $isUse       = $firstString === $stackPtr;
            $this->storeImportedFunction($phpcsFile, $next, $stackPtr, $isUse);
        }//end if

        $prev = $phpcsFile->findPrevious([T_WHITESPACE, T_BITWISE_AND], ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] === T_FUNCTION) {
            // Function declaration, not a function call.
            return;
        }

        $isGlobalUse = false;
        if ($isUse === false) {
            if ($tokens[$prev]['code'] === T_NEW) {
                // Object creation, not an inbuilt function.
                return;
            }

            if ($tokens[$prev]['code'] === T_OBJECT_OPERATOR) {
                // Not an inbuilt function.
                return;
            }

            if ($tokens[$prev]['code'] === T_DOUBLE_COLON) {
                // Not an inbuilt function.
                return;
            }

            if ($tokens[$prev]['code'] === T_NS_SEPARATOR) {
                $beforePrev = $phpcsFile->findPrevious(
                    Tokens::$emptyTokens,
                    ($prev - 1),
                    null,
                    true
                );
                if ($tokens[$beforePrev]['code'] === T_STRING) {
                    // Namespaced class/function, not an inbuilt function.
                    return;
                }

                $isGlobalUse = true;
            }
        }//end if

        // Make sure it is an inbuilt PHP function
        // and it is not imported function.
        // PHP_CodeSniffer can possibly include user defined functions
        // through the use of vendor/autoload.php.
        $content       = $tokens[$stackPtr]['content'];
        $lower         = strtolower($content);
        $isBuiltIn     = isset($this->builtInFunctions[$lower]);
        $isImported    = $isGlobalUse === false
            && $isUse === false
            && isset($this->imported[$lower]);
        $isInNamespace = $isGlobalUse === false
            && $isUse === false
            && isset($this->functionsInNamespace[$lower]);

        if ($isInNamespace === true) {
            if ($content !== $this->functionsInNamespace[$lower]) {
                $error = 'Call function form namespace is invalid; expected "%s" but found "%s"';
                $data  = [
                    $this->functionsInNamespace[$lower],
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'FunctionInNamespace', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, $this->functionsInNamespace[$lower]);
                }
            }

            return;
        }

        if ($isImported === true) {
            if ($content !== $this->imported[$lower]) {
                $error = 'Call imported function is invalid; expected "%s" but found "%s"';
                $data  = [
                    $this->imported[$lower],
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'CallImported', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, $this->imported[$lower]);
                }
            }

            return;
        }

        if ($isBuiltIn === true) {
            if ($content !== $lower) {
                $error = 'Calls to inbuilt PHP functions must be lowercase; expected "%s" but found "%s"';
                $data  = [
                    strtolower($content),
                    $content,
                ];

                $fix = $phpcsFile->addFixableError($error, $stackPtr, 'CallUppercase', $data);
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken($stackPtr, strtolower($content));
                }
            }

            return;
        }

    }//end process()


    /**
     * Keep imported function in local property.
     *
     * @param File $phpcsFile        The file being scanned.
     * @param int  $next             Next non-empty token after $stackPtr.
     * @param int  $stackPtr         Position of function name.
     * @param bool $isGlobalFunction Whether imported function is global or not.
     *
     * @return void
     */
    private function storeImportedFunction(File $phpcsFile, $next, $stackPtr, $isGlobalFunction)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$next]['code'] === T_AS) {
            // Function imported with alias.
            $alias = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), null, true);
            if ($alias !== false && $tokens[$alias]['code'] === T_STRING) {
                $content = $tokens[$alias]['content'];
                $lower   = strtolower($content);
                $this->imported[$lower] = $content;
            }
        } else {
            // Function imported without alias.
            $content = $tokens[$stackPtr]['content'];
            $lower   = strtolower($content);
            if ($isGlobalFunction === true) {
                $this->imported[$lower] = $lower;
            } else {
                $this->imported[$lower] = $content;
            }
        }

    }//end storeImportedFunction()


    /**
     * Find namespace for current token.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack.
     *
     * @return string
     */
    private function getNamespace(File $phpcsFile, $stackPtr)
    {
        $nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($nsStart !== false) {
            $nsEnd = $phpcsFile->findNext([T_NS_SEPARATOR, T_STRING, T_WHITESPACE], ($nsStart + 1), null, true);
            return trim($phpcsFile->getTokensAsString(($nsStart + 1), ($nsEnd - $nsStart - 1)));
        }

        return '';

    }//end getNamespace()


    /**
     * Find all functions defined in current namespace.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int  $stackPtr  The position of the current token in the stack.
     *
     * @return array<string, string> Array of defined function names in current namespace.
     */
    private function getFunctionsInNamespace(File $phpcsFile, $stackPtr)
    {
        $first = 0;
        $last  = $phpcsFile->numTokens;

        $tokens = $phpcsFile->getTokens();

        $nsStart = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
        if ($nsStart !== false && isset($tokens[$nsStart]['scope_opener']) === true) {
            $first = $tokens[$nsStart]['scope_opener'];
            $last  = $tokens[$nsStart]['scope_closer'];
        }

        $functions = [];

        $function = $first;
        while (($function = $phpcsFile->findNext(T_FUNCTION, ($function + 1), $last)) !== false) {
            $token = $tokens[$function];
            if ($nsStart !== false) {
                unset($token['conditions'][$nsStart]);
            }

            if (empty($token['conditions']) === true) {
                // It is a function definition.
                $name = $phpcsFile->findPrevious(T_STRING, ($token['parenthesis_opener'] - 1), $function);
                if ($name !== false) {
                    $content = $tokens[$name]['content'];
                    $functions[strtolower($content)] = $content;
                }
            }
        }

        return $functions;

    }//end getFunctionsInNamespace()


}//end class
