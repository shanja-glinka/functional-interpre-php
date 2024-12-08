<?php


require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/larapack/dd/src/helper.php';

if (!isset($_GET['mode'])) {
    $_GET['mode'] = 'textInterpreter';
}

require_once __DIR__ . '/tests/index.php';
