<?php

declare(strict_types=1);

error_reporting(E_ALL|E_STRICT);
ini_set('display_errors', '1');
ini_set('error_reporting', (string)(E_ALL | E_STRICT));

function q(iterable $a) {};

var_dump(class_exists('iterable'));