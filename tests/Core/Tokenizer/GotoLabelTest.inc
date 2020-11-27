<?php

/* testGotoStatement */
goto marker;
echo 'Foo';

/* testGotoDeclaration */
marker:
echo 'Bar';

/* testGotoStatementInLoop */
for($i=0,$j=50; $i<100; $i++) {
  while($j--) {
    if($j==17) GOTO end;
  }
}
echo "i = $i";
?>
<div><?php $cond ? TEST_A : TEST_B ?></div>

<?php
/* testGotoDeclarationOutsideLoop */
end:
echo 'j hit 17';

switch ($x) {
    /* testNotGotoDeclarationGlobalConstant */
    case CONSTANT:
        // Do something.
        break;

    /* testNotGotoDeclarationNamespacedConstant */
    case MyNS\CONSTANT:
        // Do something.
        break;

    /* testNotGotoDeclarationClassConstant */
    case MyClass::CONSTANT:
        // Do something.
        break;

    /* testNotGotoDeclarationClassProperty */
    case $obj->property:
        // Do something.
        break;
}

switch (true) {
    /* testNotGotoDeclarationGlobalConstantInTernary */
    case $x === ($cond) ? CONST_A : CONST_B:
        // Do something.
        break;
}
