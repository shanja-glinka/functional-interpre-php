<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/larapack/dd/src/helper.php';

use ShanjaGlinka\Interpre\Interpre;
use ShanjaGlinka\Interpre\InterpreterModeEnum;


$interpre = Interpre::getInstance();
$interpre->setMode(InterpreterModeEnum::TEXT_INTERPRETER_MODE);
