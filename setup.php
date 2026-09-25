<?php

/** Backwards-compatible CLI entry point. Use `php migrate` for new deploys. */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require __DIR__ . '/migrate';
