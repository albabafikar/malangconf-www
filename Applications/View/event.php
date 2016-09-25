<?php
/**
 * Displaying Single Page Event
 */
if (!isset($this) || !$this instanceof \Slim\Views\PhpRenderer) {
    return;
}

/**
 * Example Set Title
 */
$this->addAttribute('title', 'Event - Single Page');

/**
 * Require Header
 */
require_once __DIR__ . '/header.php';

echo '<pre>';
// getting info
print_r(\MalangPhp\Site\Conf\App::getInstance()->getCurrentRouteInfo()[2]);
echo '</pre>';

require_once __DIR__ . '/footer.php';
