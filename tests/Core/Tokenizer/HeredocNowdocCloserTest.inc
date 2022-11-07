<?php

/* testHeredocCloserNoIndent */
$heredoc = <<<EOD
some text
      some text
  some text
EOD;

/* testNowdocCloserNoIndent */
$nowdoc = <<<'EOD'
some text
      some text
  some text
EOD;

/* testHeredocCloserSpaceIndent */
$heredoc = <<<END
      a
     b
    c
    END;

/* testNowdocCloserSpaceIndent */
$nowdoc = <<<'END'
          a
        b
      c
     END;

/* testHeredocCloserTabIndent */
$heredoc = <<<"END"
		  a
		b
	  c
	 END;

/* testNowdocCloserTabIndent */
$nowdoc = <<<'END'
	  a
	 b
	c
	END;
