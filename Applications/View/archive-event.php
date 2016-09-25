<?php
/**
 * Displaying Archive Events
 */
if (!isset($this) || !$this instanceof \Slim\Views\PhpRenderer) {
    return;
}

/**
 * Example Set Title
 */
$this->addAttribute('title', 'Events - Archive');

/**
 * Require Header
 */
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/footer.php';
