<?php

$_SERVER['SCRIPT_FILENAME'] = getcwd() . '/public/index.php';
chdir(__DIR__ . '/..');

require __DIR__ . '/../public/index.php';