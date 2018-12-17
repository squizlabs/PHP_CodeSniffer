<?php
require_once __DIR__ . '/../autoload.php';

// phpstan extensions
require_once __DIR__ . '/ConfigPropertyReflection.php';
require_once __DIR__ . '/ConfigReflectionExtension.php';

// init app configuration
require_once __DIR__ . '/../tests/bootstrap.php';
require_once __DIR__ . '/../src/Util/Tokens.php';

require_once __DIR__ . '/../CodeSniffer.conf.dist';
