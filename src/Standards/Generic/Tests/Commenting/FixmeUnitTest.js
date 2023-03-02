
/**
 * FIXME: Write this comment
 * FIXME
 */

// FIXME: remove this.
alert('test');

// FIXME remove this.
alert('test');

// fixme - remove this.

// Extract info from the array.
// FIXME: can this be done faster?

// Extract info from the array (fixme: make it faster)
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
