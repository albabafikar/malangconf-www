<?php
/**
 * Malang Conf Site Index
 *
 * @license MIT
 */
use \MalangPhp\Site\Conf\App;

// Require vendor
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Applications/Loader.php';

// run applications development set
return App::getInstance()
    ->setEnvironment('development')
    ->run();
