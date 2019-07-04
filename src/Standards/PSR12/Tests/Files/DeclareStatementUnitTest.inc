<?php
declare/* test */(ticks=1);
declare   (ticks=1);
declare(   ticks=1);
declare(TIcKs  =1);
declare(ticks=   1);
declare(ticks=1   );
declare(ticks=1);
declare(ticks=1   )    ;
declare(ticks=1) /* test */;
declare(/* test */ ticks=1    )    ;
declare(/* test */ ticks /* test */ =1);
declare(  ticks
=1   );

    declare(ticks=1) { }

  declare(ticks=1)
{ }

declare(ticks=1)
{

    }

declare(ticks=1) { }//end comment

declare(ticks=1) { }$x =1;

    declare(ticks=1) {
      $test = true;
    }

declare(  ticks // phpcs:ignore Standard.Category.SniffName -- fixing is undesirable
=1);

declare(ticks=1) {
}$x =1; // phpcs:ignore Standard.Category.SniffName -- fixing is undesirable

declare(ticks=1) { // test
}

declare(ticks=1) {$test = true;
}

        declare(ticks=1) { /* test */  /* test */      $test = true;
}

declare(ticks=1) {  $test = true;
}
