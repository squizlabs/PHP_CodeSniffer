<?php

// This is a parse error, but the sniff should still handle this correctly.
declare($something = $value);

const STRICT_TYPES = 1;
