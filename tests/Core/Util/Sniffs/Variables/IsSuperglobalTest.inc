<?php

/* testNotAVariable */
return $something;

/* testNotAReservedVar */
echo $something;

/* testReservedVarNotSuperglobal */
echo $php_errormsg;

/* testReservedVarIsSuperglobal */
echo $_GET['key'];

/* testGLOBALSArrayKeyNotAReservedVar */
echo $GLOBALS['key'];

/* testGLOBALSArrayKeyVar */
echo $GLOBALS[$something];

/* testGLOBALSArrayKeyReservedVar */
echo $GLOBALS[$php_errormsg];

// Won't work, but not our concern.
/* testGLOBALSArrayKeySuperglobal */
echo $GLOBALS[$_COOKIE];

/* testGLOBALSArrayKeyNotSingleString */
echo $GLOBALS['key'.'more'];

/* testGLOBALSArrayKeyInterpolatedVar */
echo $GLOBALS["{$key}more"];

/* testGLOBALSArrayKeySingleStringSuperglobal */
echo $GLOBALS['_GET'];

/* testGLOBALSArrayKeySuperglobalWithKey */
echo $GLOBALS[$_GET['key']];

/* testSuperglobalKeyNotGLOBALSArray */
echo $not_globals['_GET'];
