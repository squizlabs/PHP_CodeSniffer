<?php
/**
 * FIXME: really need to fix this before I commit.
 * FIXME
 */

// FIXME: this is broken, don't forget to fix it
error_log('test');

// FIXME remove this.
Debug::bam('test');

// fixme - fix this.

// Extract info from the array.
// FIXME: this is just a stub, fill in later

// Extract info from the array (fixme: finish later)
// To do this, use a function!
// nofixme! NOFIXME! NOfixme!
//FIXME.
//éfixme
//fixmeé

/**
 * While there is no official "fix me" tag, only `@todo`, let's support a tag for the purpose of this sniff anyway.
 *
 * @fixme This message should be picked up.
 * @fixme: This message should be picked up too.
 * @fixme - here is a message
 *
 * The below should not show a message as there is no description associated with the tag.
 * @fixme
 * @anothertag
 *
 * @param string $something FIXME: add description
 */
