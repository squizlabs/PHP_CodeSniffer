<?php

// Valid
$var1    = 'var1';
$var10   = 'var1';
$var100  = 'var1';
$var1000 = 'var1';

// Invalid
$var1    = 'var1';
$var10 = 'var1';
$var100 = 'var1';
$var1000 = 'var1';

// Valid
$var1  = 'var1';
$var10 = 'var1';

$var100  = 'var1';
$var1000 = 'var1';

// Invalid
$var1     = 'var1';
$var10    = 'var1';

$var100   = 'var1';
$var1000  = 'var1';

// Valid
$var1    .= 'var1';
$var10   .= 'var1';
$var100  .= 'var1';
$var1000 .= 'var1';

// Invalid
$var1    .= 'var1';
$var10 .= 'var1';
$var100 .= 'var1';
$var1000 .= 'var1';

// Valid
$var1     = 'var1';
$var10   .= 'var1';
$var100   = 'var1';
$var1000 .= 'var1';

// Invalid
$var1    = 'var1';
$var10   .= 'var1';
$var100  = 'var1';
$var1000 .= 'var1';

// Valid
$var1  .= 'var1';
$var10 .= 'var1';

$var100  .= 'var1';
$var1000 .= 'var1';

// Invalid
$var1     .= 'var1';
$var10    .= 'var1';

$var100   .= 'var1';
$var1000  .= 'var1';

// Valid
$var = 100;

// InValid
$var  = 100;

$commentStart = $phpcsFile->findPrevious();
$commentEnd   = $this->_phpcsFile;
$expected    .= '...';

// Invalid
$this->okButton    = new Button();
$content = new MyClass();
?>
