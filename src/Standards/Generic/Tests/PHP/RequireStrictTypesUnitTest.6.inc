<?php

// This is a parse error, but the sniff should still handle this correctly.
declare($something = STRICT_TYPES);
