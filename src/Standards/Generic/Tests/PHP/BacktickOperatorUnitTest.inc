<?php
$output = `ls -al`;

// Testing an invalid phpcs:set annotations.
// This test is unrelated to this sniff, but the issue needs a sniff to be tested.
// phpcs:set Generic.PHP.BacktickOperator unknown 10

// Make sure errors after an incorrect annotation are still being thrown.
$output = `ls -al`;
