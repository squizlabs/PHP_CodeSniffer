// Test warning for when short_open_tag is off.

Some content <? echo $var; ?> Some more content

// Test multi-line.
Some content <?
echo $var;
?> Some more content

// Make sure skipping works.
Some content <?
echo '<?';
?> Some more content

// Only recognize closing tag after opener.
Some?> content <?
