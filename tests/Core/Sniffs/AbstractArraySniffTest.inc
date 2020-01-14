<?php

/* testSimpleValues */
$foo = [1,2,3];

/* testSimpleKeyValues */
$foo = ['1'=>1,'2'=>2,'3'=>3];

/* testMissingKeys */
$foo = ['1'=>1,2,'3'=>3];

/* testMultiTokenKeys */
$paths = array(
    Init::ROOT_DIR.'/a' => 'a',
    Init::ROOT_DIR.'/b' => 'b',
);

/* testMissingKeysCoalesceTernary */
return [
    $a => static function () { return [1,2,3]; },
    $b ?? $c,
    $d ? [$e] : [$f],
];

/* testTernaryValues */
$foo = [
    '1' => $row['status'] === 'rejected'
        ? self::REJECTED_CODE
        : self::VERIFIED_CODE,
    '2' => in_array($row['status'], array('notverified', 'unverified'), true)
        ? self::STATUS_PENDING
        : self::STATUS_VERIFIED,
    '3' => strtotime($row['date']),
];

/* testHeredocValues */
$foo = array(
    <<<HERE
HERE
,
    <<<HERE
HERE
  ,
   );

/* testArrowFunctionValue */
$foo = array(
    1 => '1',
    2 => fn ($x) => yield 'a' => $x,
    3 => '3',
);
