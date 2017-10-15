<?php
/* @codingStandardsIgnoreFile */

class TestMemberProperties
{
    /* testVar */
    var $varA = true;

    /* testPublic */
    public $varB = true;

    /* testProtected */
    protected $varC = true;

    /* testPrivate */
    private $varD = true;

    /* testStatic */
    static $varE = true;

    /* testStaticVar */
    static var $varF = true;

    /* testVarStatic */
    var static $varG = true;

    /* testPublicStatic */
    public static $varH = true;

    /* testProtectedStatic */
    static protected $varI = true;

    /* testPrivateStatic */
    private static $varJ = true;

    /* testNoPrefix */
    $varK = true;

    /* testMethodParam */
    public function methodName($param) {
        /* testImportedGlobal */
        global $importedGlobal = true;

        /* testLocalVariable */
        $localVariable = true;
    }
}

interface Base
{
    /* testInterfaceProperty */
    protected $anonymous;
}

/* testGlobalVariable */
$globalVariable = true;

/* testNotAVariable */
return;
