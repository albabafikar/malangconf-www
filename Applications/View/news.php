<?php
/**
 * Displaying Single Page News
 */
if (!isset($this) || !$this instanceof \Slim\Views\PhpRenderer) {
    return;
}
/**
 * Example Set Title
 */
$this->addAttribute('title', 'News - Single Page');

/**
 * Require Header
 */
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';
