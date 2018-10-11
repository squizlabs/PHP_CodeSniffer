<?php

/**
<<<<<<< HEAD
 * @var string $bar
=======
 * @var string $bar
>>>>>>> master
 */

/*
 * Testing detecting merge conflicts using different comment styles.
 *
<<<<<<< HEAD
 * @var string $bar
 */
public function foo($bar){ }

/*
=======
 * @var string $bar
>>>>>>> master
*/

// Comment
<<<<<<< HEAD
// Second comment line. NOTE: The above opener breaks the tokenizer.
=======
// New second comment line
>>>>>>> master
// Third comment line
